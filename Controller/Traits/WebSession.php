<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Symfony\Component\HttpFoundation\Session\Session as Sessions;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @property Sessions|SessionInterface $session
 */
trait WebSession
{
    /**
     * @var string
     */
    protected $skWebsiteToken = 'session-key-website-token';

    /**
     * Set array item in session
     *
     * @param string $sessionKey
     * @param string $setKey
     * @param mixed  $setValue
     *
     * @return array
     */
    public function sessionArraySet(string $sessionKey, string $setKey, $setValue)
    {
        $origin = $this->session->get($sessionKey, []);
        unset($origin[$setKey]);

        $origin[$setKey] = $setValue;
        $this->session->set($sessionKey, $origin);

        return $origin;
    }

    /**
     * Get array item in session
     *
     * @param string $sessionKey
     * @param string $getKey
     * @param bool   $delete
     *
     * @return mixed
     */
    public function sessionArrayGet(string $sessionKey, string $getKey, bool $delete = false)
    {
        $origin = $this->session->get($sessionKey, []);
        if (!isset($origin[$getKey])) {
            return null;
        }

        if ($delete) {
            $item = Helper::dig($origin, $getKey);
            $this->session->set($sessionKey, $origin);
        } else {
            $item = $origin[$getKey] ?? null;
        }

        return $item;
    }

    /**
     * Create website token
     *
     * @param int $liveTimes
     * @param int $liveSecond
     *
     * @return string
     */
    public function createWebsiteToken(int $liveTimes = 1, int $liveSecond = 0): string
    {
        $token = Helper::generateToken();
        $value = [
            'times'  => $liveTimes,
            'expire' => $liveSecond ? (time() + $liveSecond) : 0,
        ];

        $this->sessionArraySet($this->skWebsiteToken, $token, $value);

        return $token;
    }

    /**
     * Validation the website token
     *
     * @param string $token
     *
     * @return bool
     */
    public function validWebsiteToken(string $token): bool
    {
        $value = $this->sessionArrayGet($this->skWebsiteToken, $token);
        if (empty($value)) {
            return false;
        }

        if ($value['expire'] && $value['expire'] < time()) {
            $this->sessionArrayGet($this->skWebsiteToken, $token, true);

            return false;
        }

        return true;
    }

    /**
     * Remove the website token
     *
     * @param string $token
     * @param int    $times
     */
    public function invalidWebsiteToken(string $token, int $times = 1)
    {
        $value = $this->sessionArrayGet($this->skWebsiteToken, $token);
        if (empty($value)) {
            return;
        }

        if ($value['times'] > $times) {
            $value['times'] -= $times;
            $this->sessionArraySet($this->skWebsiteToken, $token, $value);
        } else {
            $this->sessionArrayGet($this->skWebsiteToken, $token, true);
        }
    }
}