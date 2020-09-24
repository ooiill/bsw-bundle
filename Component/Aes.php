<?php

namespace Leon\BswBundle\Component;

class Aes
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $iv;

    /**
     * @var string
     */
    protected $method = 'AES-128-CBC';

    /**
     * Aes constructor.
     *
     * @param string      $key
     * @param string      $iv
     * @param string|null $method
     */
    public function __construct(string $key, string $iv, string $method = null)
    {
        $this->key = $key;
        $this->iv = $iv;
        $method && $this->method = $method;
    }

    /**
     * AES encode
     *
     * @param string $text
     *
     * @return string
     */
    public function AESEncode(string $text): string
    {
        return base64_encode(openssl_encrypt($text, $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv));
    }

    /**
     * AES decode
     *
     * @param string $cryptText
     *
     * @return string
     */
    public function AESDecode(string $cryptText): string
    {
        return openssl_decrypt(base64_decode($cryptText), $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * Public-Key Cryptography Standards Padding
     *
     * @param string $text
     * @param int    $blockSize
     *
     * @return string
     */
    public function PKCSPadding(string $text, int $blockSize = 8): string
    {
        $pad = $blockSize - (strlen($text) % $blockSize);

        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * Public-Key Cryptography Standards Un Padding
     *
     * @param string $text
     *
     * @return string
     */
    public function PKCSUnPadding(string $text): string
    {
        $pad = ord($text[strlen($text) - 1]);

        if ($pad > strlen($text)) {
            return false;
        }

        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }

        return substr($text, 0, -1 * $pad);
    }
}