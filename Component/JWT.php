<?php

namespace Leon\BswBundle\Component;

use Leon\BswBundle\Module\Exception\FileNotExistsException;
use InvalidArgumentException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Claim\Basic;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Key;

class JWT
{
    /**
     * @var string
     */
    protected $privateFile;

    /**
     * @var string
     */
    protected $publicFile;

    /**
     * @var string
     */
    protected $issuer;

    /**
     * @var string
     */
    protected $type;

    /**
     * JWT constructor.
     *
     * @param string $privateFile
     * @param string $publicFile
     * @param string $issuer
     * @param string $type
     *
     * @throws
     */
    public function __construct(string $privateFile, string $publicFile, string $issuer, string $type)
    {
        if (!file_exists($privateFile)) {
            throw new FileNotExistsException("Private key file don't exists ({$privateFile})");
        }

        if (!file_exists($publicFile)) {
            throw new FileNotExistsException("Public key file don't exists ({$publicFile})");
        }

        $this->privateFile = $privateFile;
        $this->publicFile = $publicFile;
        $this->issuer = $issuer;

        if (!in_array($type, ['hmac', 'rsa'])) {
            throw new InvalidArgumentException("Type error for jwt ({$type})");
        }

        $this->type = ucfirst($type);
    }

    /**
     * Get signer
     *
     * @return mixed
     */
    private function signer()
    {
        $class = "\Lcobucci\JWT\Signer\\{$this->type}\Sha256";

        return new $class();
    }

    /**
     * Get keychain
     *
     * @param bool $contrary
     *
     * @return bool|Key|string
     */
    private function keychain(bool $contrary = false)
    {
        if ($this->type == 'Hmac') {
            return file_get_contents($this->privateFile);
        }

        $keychain = new Keychain();
        if ($contrary) {
            return $keychain->getPublicKey("file://{$this->publicFile}");
        }

        return $keychain->getPrivateKey("file://{$this->privateFile}");
    }

    /**
     * Create token with JWT
     *
     * @param array $data
     * @param int   $expireTime
     *
     * @return string
     */
    public function token(array $data, int $expireTime)
    {
        $now = time();
        $token = (new Builder())
            ->setIssuer($this->issuer)
            ->setIssuedAt($now)
            ->setExpiration($now + $expireTime);

        foreach ($data as $key => $value) {
            $token = $token->set($key, $value);
        };

        $token = $token
            ->sign($this->signer(), $this->keychain())
            ->getToken();

        return strval($token);
    }

    /**
     * Parse token
     *
     * @param string $token
     *
     * @return array|false|null
     */
    public function parse(string $token)
    {
        $token = (new Parser())->parse($token);

        // verify sign
        if (!$token->verify($this->signer(), $this->keychain(true))) {
            return false;
        }

        // validation expire
        if (!$token->validate(new ValidationData())) {
            return null;
        }

        /**
         * @var Basic $item
         */
        $args = [];
        foreach ($token->getClaims() as $item) {
            $args[$item->getName()] = $item->getValue();
        }

        return $args;
    }
}