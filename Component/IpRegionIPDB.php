<?php

namespace Leon\BswBundle\Component;

use Exception;
use InvalidArgumentException;
use UnexpectedValueException;
use BadMethodCallException;

/**
 * @see https://github.com/ipipdotnet/ipdb-php
 */
class IpRegionIPDB
{
    const IPV4 = 1;
    const IPV6 = 2;

    protected $file       = null;
    protected $fileSize   = 0;
    protected $nodeCount  = 0;
    protected $nodeOffset = 0;
    protected $meta       = [];
    protected $database   = '';

    protected $v4offset      = 0;
    protected $v6offsetCache = [];

    /**
     * IpRegionIPDB constructor.
     *
     * @param $ip2regionFile
     *
     * @throws Exception
     */
    public function __construct(string $ip2regionFile)
    {
        $this->database = $ip2regionFile;
        $this->init();
    }

    /**
     * @throws Exception
     */
    private function init()
    {
        if (is_readable($this->database) === false) {
            throw new InvalidArgumentException(
                "The IP Database file `{$this->database}` does not exist or is not readable"
            );
        }

        $this->file = @fopen($this->database, 'rb');
        if ($this->file === false) {
            throw new InvalidArgumentException("IP Database File opening `{$this->database}`");
        }

        $this->fileSize = @filesize($this->database);
        if ($this->fileSize === false) {
            throw new UnexpectedValueException("Error determining the size of `{$this->database}`");
        }

        $metaLength = unpack('N', fread($this->file, 4))[1];
        $text = fread($this->file, $metaLength);
        $this->meta = Helper::jsonArray($text);
        if (isset($this->meta['fields']) === false || isset($this->meta['languages']) === false) {
            throw new Exception('IP Database metadata error');
        }

        $fileSize = 4 + $metaLength + $this->meta['total_size'];
        if ($fileSize != $this->fileSize) {
            throw  new Exception('IP Database size error');
        }

        $this->nodeCount = $this->meta['node_count'];
        $this->nodeOffset = 4 + $metaLength;
    }

    /**
     * @param string $ip
     * @param string $language
     *
     * @return array|null
     */
    public function find(string $ip, string $language)
    {
        if (is_resource($this->file) === false) {
            throw new BadMethodCallException('IPIP DB closed');
        }

        if (isset($this->meta['languages'][$language]) === false) {
            throw new InvalidArgumentException("Language {$language} is not support");
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) === false) {
            throw new InvalidArgumentException("The value `{$ip}` is not a valid IP address");
        }

        if (strpos($ip, '.') !== false && !$this->supportV4()) {
            throw new InvalidArgumentException("The Database not support IPv4 address");
        } elseif (strpos($ip, ':') !== false && !$this->supportV6()) {
            throw new InvalidArgumentException("The Database not support IPv6 address");
        }

        try {
            $node = $this->findNode($ip);
            if ($node > 0) {
                $data = $this->resolve($node);
                $values = explode("\t", $data);

                return array_slice($values, $this->meta['languages'][$language], count($this->meta['fields']));
            }
        } catch (Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * @param string $ip
     * @param string $language
     *
     * @return array|null
     */
    public function findMap(string $ip, string $language)
    {
        $array = $this->find($ip, $language);
        if (null === $array) {
            return null;
        }

        return array_combine($this->meta['fields'], $array);
    }

    /**
     * @param string $ip
     *
     * @return int|mixed
     * @throws Exception
     */
    private function findNode(string $ip)
    {
        $binary = inet_pton($ip);
        $bitCount = strlen($binary) * 8; // 32 | 128
        $key = substr($binary, 0, 2);
        $node = 0;
        $index = 0;

        if ($bitCount === 32) {
            if ($this->v4offset === 0) {
                for ($i = 0; $i < 96 && $node < $this->nodeCount; $i++) {
                    if ($i >= 80) {
                        $idx = 1;
                    } else {
                        $idx = 0;
                    }
                    $node = $this->readNode($node, $idx);
                    if ($node > $this->nodeCount) {
                        return 0;
                    }
                }
                $this->v4offset = $node;
            } else {
                $node = $this->v4offset;
            }
        } else {
            if (isset($this->v6offsetCache[$key])) {
                $index = 16;
                $node = $this->v6offsetCache[$key];
            }
        }

        for ($i = $index; $i < $bitCount; $i++) {
            if ($node >= $this->nodeCount) {
                break;
            }
            $node = $this->readNode($node, 1 & ((0xFF & ord($binary[$i >> 3])) >> 7 - ($i % 8)));
            if ($i == 15) {
                $this->v6offsetCache[$key] = $node;
            }
        }

        if ($node === $this->nodeCount) {
            return 0;
        } elseif ($node > $this->nodeCount) {
            return $node;
        }

        throw new Exception("Find node failed");
    }

    /**
     * @param int $node
     * @param int $index
     *
     * @return mixed
     * @throws Exception
     */
    private function readNode(int $node, int $index)
    {
        return unpack('N', $this->read($this->file, $node * 8 + $index * 4, 4))[1];
    }

    /**
     * @param int $node
     *
     * @return bool|string|null
     * @throws Exception
     */
    private function resolve(int $node)
    {
        $resolved = $node - $this->nodeCount + $this->nodeCount * 8;
        if ($resolved >= $this->fileSize) {
            return null;
        }
        $bytes = $this->read($this->file, $resolved, 2);
        $size = unpack('N', str_pad($bytes, 4, "\x00", STR_PAD_LEFT))[1];
        $resolved += 2;

        return $this->read($this->file, $resolved, $size);
    }

    public function close()
    {
        if (is_resource($this->file) === true) {
            fclose($this->file);
        }
    }

    /**
     * @param     $stream
     * @param int $offset
     * @param int $length
     *
     * @return bool|string
     * @throws Exception
     */
    private function read($stream, int $offset, int $length)
    {
        if ($length > 0) {
            if (fseek($stream, $offset + $this->nodeOffset) === 0) {
                $value = fread($stream, $length);
                if (strlen($value) === $length) {
                    return $value;
                }
            }
            throw new Exception("The Database file read bad data");
        }

        return '';
    }

    /**
     * @return bool
     */
    public function supportV6()
    {
        return ($this->meta['ip_version'] & self::IPV6) === self::IPV6;
    }

    /**
     * @return bool
     */
    public function supportV4()
    {
        return ($this->meta['ip_version'] & self::IPV4) === self::IPV4;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return mixed
     */
    public function getBuildTime()
    {
        return $this->meta['build'];
    }
}