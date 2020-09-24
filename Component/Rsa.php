<?php

namespace Leon\BswBundle\Component;

class Rsa
{
    /**
     * @const int
     */
    const TEXT_MAX_LEN = 117;

    /**
     * @const int
     */
    const CRYPT_TEXT_MAX_LEN = 172;

    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * Rsa constructor.
     *
     * @param string $privateFile
     * @param string $publicFile
     */
    public function __construct(string $privateFile, string $publicFile)
    {
        $this->privateKey = file_get_contents($privateFile);
        $this->publicKey = file_get_contents($publicFile);
    }

    /**
     * Get public key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return openssl_pkey_get_public($this->publicKey);
    }

    /**
     * Get private key
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return openssl_pkey_get_private($this->privateKey);
    }

    /**
     * Encrypt by public key
     *
     * @param string $string
     * @param string $cryptText
     *
     * @return string
     */
    public function encryptByPublicKey(string $string, string $cryptText = null)
    {
        $data = substr($string, 0, self::TEXT_MAX_LEN);

        if (openssl_public_encrypt($data, $encrypted, $this->getPublicKey())) {
            $cryptText .= base64_encode($encrypted);
        }

        $string = substr($string, self::TEXT_MAX_LEN);
        if (strlen($string) > 0) {
            return $this->encryptByPublicKey($string, $cryptText);
        }

        return $cryptText;
    }

    /**
     * Encrypt by private key
     *
     * @param string $string
     * @param string $cryptText
     *
     * @return string
     */
    public function encryptByPrivateKey(string $string, string $cryptText = null)
    {
        $data = substr($string, 0, self::TEXT_MAX_LEN);

        if (openssl_private_encrypt($data, $encrypted, $this->getPrivateKey())) {
            $cryptText .= base64_encode($encrypted);
        }

        $string = substr($string, self::TEXT_MAX_LEN);
        if (strlen($string) > 0) {
            return $this->encryptByPrivateKey($string, $cryptText);
        }

        return $cryptText;
    }

    /**
     * Decrypt by public key
     *
     * @param string $cryptText
     * @param string $text
     * @param bool   $fromJS
     *
     * @return string
     */
    public function decryptByPublicKey(string $cryptText, string $text = null, bool $fromJS = false)
    {
        $padding = $fromJS ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;
        $data = base64_decode(substr($cryptText, 0, self::CRYPT_TEXT_MAX_LEN));

        if (openssl_public_decrypt($data, $decrypted, $this->getPublicKey(), $padding)) {
            $text .= $fromJS ? trim(strrev($decrypted)) : $decrypted;
        }

        $cryptText = substr($cryptText, self::CRYPT_TEXT_MAX_LEN);
        if (strlen($cryptText) > 0) {
            return $this->decryptByPublicKey($cryptText, $text, $fromJS);
        }

        return $text;
    }

    /**
     * Decrypt by private key
     *
     * @param string $cryptText
     * @param string $text
     * @param bool   $fromJS
     *
     * @return string
     */
    public function decryptByPrivateKey(string $cryptText, string $text = null, bool $fromJS = false)
    {
        $padding = $fromJS ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;
        $data = base64_decode(substr($cryptText, 0, self::CRYPT_TEXT_MAX_LEN));

        if (openssl_private_decrypt($data, $decrypted, $this->getPrivateKey(), $padding)) {
            $text .= $fromJS ? trim(strrev($decrypted)) : $decrypted;
        }

        $cryptText = substr($cryptText, self::CRYPT_TEXT_MAX_LEN);
        if (strlen($cryptText) > 0) {
            return $this->decryptByPrivateKey($cryptText, $text, $fromJS);
        }

        return $text;
    }
}