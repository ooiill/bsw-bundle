<?php

namespace Leon\BswBundle\Component;

/**
 * @see https://github.com/zengzhan/qqzeng-ip
 */
class IpRegionDAT
{
    protected $firstStartIpOffset;
    protected $lastStartIpOffset;
    protected $prefixStartOffset;
    protected $prefixEndOffset;
    protected $ipCount;
    protected $prefixCount;
    protected $fp;
    protected $prefix_array = [];

    /**
     * IpRegionQQ constructor.
     *
     * @param string $ip2regionFile
     */
    public function __construct(string $ip2regionFile)
    {
        $this->fp = @fopen($ip2regionFile, 'rb');
        $buf = $this->read($this->fp, 0, 16);
        $this->firstStartIpOffset = $this->bytesToLong($buf[0], $buf[1], $buf[2], $buf[3]);
        $this->lastStartIpOffset = $this->bytesToLong($buf[4], $buf[5], $buf[6], $buf[7]);
        $this->prefixStartOffset = $this->bytesToLong($buf[8], $buf[9], $buf[10], $buf[11]);
        $this->prefixEndOffset = $this->bytesToLong($buf[12], $buf[13], $buf[14], $buf[15]);
        $this->ipCount = floor(($this->lastStartIpOffset - $this->firstStartIpOffset) / 12) + 1;
        $this->prefixCount = floor(($this->prefixEndOffset - $this->prefixStartOffset) / 9) + 1;
        $pref_buf = $this->read($this->fp, $this->prefixStartOffset, $this->prefixCount * 9);
        for ($k = 0; $k < $this->prefixCount; $k++) {
            $i = $k * 9;
            $start_index = $this->bytesToLong(
                $pref_buf[1 + $i],
                $pref_buf[2 + $i],
                $pref_buf[3 + $i],
                $pref_buf[4 + $i]
            );
            $end_index = $this->bytesToLong($pref_buf[5 + $i], $pref_buf[6 + $i], $pref_buf[7 + $i], $pref_buf[8 + $i]);
            $this->prefix_array[ord($pref_buf[$i])] = [
                'start_index' => $start_index,
                'end_index'   => $end_index,
            ];
        }
    }

    /**
     * @param string $ip_address
     *
     * @return bool|string|null
     */
    public function get(string $ip_address)
    {
        if ($ip_address == '') {
            return null;
        }

        $startIp = 0;
        $endIp = 0;
        $local_offset = 0;
        $local_length = 0;
        $prefix = explode('.', $ip_address) [0];
        $ipNum = $this->ip2int($ip_address);
        if (array_key_exists($prefix, $this->prefix_array)) {
            $index = $this->prefix_array[$prefix];
            $low = $index['start_index'];
            $high = $index['end_index'];
        } else {
            return "";
        }
        $left = $low == $high ? $low : $this->binarySearch($low, $high, $ipNum);
        $this->getIndex($left, $startIp, $endIp, $local_offset, $local_length);
        if (($startIp <= $ipNum) && ($endIp >= $ipNum)) {
            return $this->getLocal($local_offset, $local_length);
        } else {
            return null;
        }
    }

    /**
     * @param int $low
     * @param int $high
     * @param int $k
     *
     * @return float|int
     */
    private function binarySearch(int $low, int $high, int $k)
    {
        $M = 0;
        while ($low <= $high) {
            $mid = floor(($low + $high) / 2);
            $endIpNum = $this->GetEndIp($mid);
            if ($endIpNum >= $k) {
                $M = $mid;
                if ($mid == 0) {
                    break;
                }
                $high = $mid - 1;
            } else $low = $mid + 1;
        }

        return $M;
    }

    /**
     * @param $left
     * @param $startIp
     * @param $endIp
     * @param $local_offset
     * @param $local_length
     */
    private function getIndex($left, &$startIp, &$endIp, &$local_offset, &$local_length)
    {
        $left_offset = $this->firstStartIpOffset + ($left * 12);
        $buf = $this->read($this->fp, $left_offset, 12);
        $startIp = $this->bytesToLong($buf[0], $buf[1], $buf[2], $buf[3]);
        $endIp = $this->bytesToLong($buf[4], $buf[5], $buf[6], $buf[7]);
        $r3 = (ord($buf[8]) << 0 | ord($buf[9]) << 8 | ord($buf[10]) << 16);
        if ($r3 < 0)
            $r3 += 4294967296;
        $local_offset = $r3;
        $local_length = ord($buf[11]);
    }

    /**
     * @param $left
     *
     * @return int
     */
    private function getEndIp($left): int
    {
        $left_offset = $this->firstStartIpOffset + ($left * 12) + 4;
        $buf = $this->read($this->fp, $left_offset, 4);

        return $this->bytesToLong($buf[0], $buf[1], $buf[2], $buf[3]);
    }

    /**
     * @param $local_offset
     * @param $local_length
     *
     * @return bool|string
     */
    private function getLocal($local_offset, $local_length)
    {
        return $this->read($this->fp, $local_offset, $local_length);
    }

    /**
     * @param $stream
     * @param $offset
     * @param $numberOfBytes
     *
     * @return bool|string
     */
    private function read($stream, $offset, $numberOfBytes)
    {
        if (fseek($stream, $offset) == 0) {
            return fread($stream, $numberOfBytes);
        }

        return false;
    }

    /**
     * @param string $strIP
     *
     * @return int
     */
    private function ip2int(string $strIP): int
    {
        $lngIP = ip2long($strIP);
        if ($lngIP < 0) {
            $lngIP += 4294967296;
        }

        return $lngIP;
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     *
     * @return int
     */
    private function bytesToLong($a, $b, $c, $d): int
    {
        $ipLong = (ord($a) << 0) | (ord($b) << 8) | (ord($c) << 16) | (ord($d) << 24);
        if ($ipLong < 0) {
            $ipLong += 4294967296;
        }

        return $ipLong;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->fp !== null) {
            fclose($this->fp);
        }
    }
}