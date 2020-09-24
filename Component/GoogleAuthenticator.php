<?php

namespace Leon\BswBundle\Component;

use Exception;

class GoogleAuthenticator
{
    /**
     * @var int
     */
    protected $codeLength = 6;

    /**
     * @param int $secretLength
     *
     * @return string
     * @throws Exception
     */
    public function createSecret(int $secretLength = 16): string
    {
        $validChars = $this->getBase32LookupTable();
        if ($secretLength < 16 || $secretLength > 128) {
            throw new Exception('Bad secret length');
        }

        $secret = '';
        $rnd = false;
        if (function_exists('random_bytes')) {
            $rnd = random_bytes($secretLength);
        } elseif (function_exists('mcrypt_create_iv')) {
            $rnd = mcrypt_create_iv($secretLength, MCRYPT_DEV_URANDOM);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $rnd = openssl_random_pseudo_bytes($secretLength, $cryptoStrong);
            if (!$cryptoStrong) {
                $rnd = false;
            }
        }

        if ($rnd !== false) {
            for ($i = 0; $i < $secretLength; ++$i) {
                $secret .= $validChars[ord($rnd[$i]) & 31];
            }
        } else {
            throw new Exception('No source of secure random');
        }

        return $secret;
    }

    /**
     * @param string $secret
     * @param int    $timeSlice
     *
     * @return string
     */
    public function getCode(string $secret, int $timeSlice = null): string
    {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }

        $secretKey = $this->base32Decode($secret);
        $time = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $timeSlice);
        $hm = hash_hmac('SHA1', $time, $secretKey, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashPart = substr($hm, $offset, 4);

        $value = unpack('N', $hashPart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF;

        $modulo = pow(10, $this->codeLength);

        return str_pad($value % $modulo, $this->codeLength, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $name
     * @param string $secret
     * @param string $issuer
     *
     * @return string
     */
    public function getQrCodeData(string $name, string $secret, string $issuer = null): string
    {
        $content = "otpauth://totp/{$name}?secret={$secret}";
        if (isset($issuer)) {
            $content = "{$content}&issuer={$issuer}";
        }

        return $content;
    }

    /**
     * @param string $content
     * @param array  $params
     *
     * @return string
     */
    public function getQrCodeGoogleUrl(string $content, array $params = []): string
    {
        $width = intval($params['width'] ?? 0) ?: 200;
        $height = intval($params['height'] ?? 0) ?: 200;

        $level = $params['level'] ?? 'M';
        $level = in_array($level, ['L', 'M', 'Q', 'H']) ? $level : 'M';

        return "https://api.qrserver.com/v1/create-qr-code/?data={$content}&size={$width}x{$height}&ecc={$level}";
    }

    /**
     * @param string $secret
     * @param string $code
     * @param int    $discrepancy
     * @param int    $currentTimeSlice
     *
     * @return bool
     */
    public function verifyCode(string $secret, string $code, int $discrepancy = 1, int $currentTimeSlice = null): bool
    {
        if ($currentTimeSlice === null) {
            $currentTimeSlice = floor(time() / 30);
        }

        if (strlen($code) != 6) {
            return false;
        }

        for ($i = -$discrepancy; $i <= $discrepancy; ++$i) {
            $calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
            if ($this->timingSafeEquals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $length
     *
     * @return $this
     */
    public function setCodeLength(int $length): GoogleAuthenticator
    {
        $this->codeLength = $length;

        return $this;
    }

    /**
     * @param string $secret
     *
     * @return bool|string
     */
    protected function base32Decode(string $secret)
    {
        if (empty($secret)) {
            return '';
        }

        $base32chars = $this->getBase32LookupTable();
        $base32charsFlipped = array_flip($base32chars);
        $paddingCharCount = substr_count($secret, $base32chars[32]);
        $allowedValues = [6, 4, 3, 1, 0];

        if (!in_array($paddingCharCount, $allowedValues)) {
            return false;
        }

        for ($i = 0; $i < 4; ++$i) {
            if ($paddingCharCount == $allowedValues[$i] &&
                substr($secret, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i])) {
                return false;
            }
        }

        $secret = str_replace('=', '', $secret);
        $secret = str_split($secret);
        $binaryString = '';

        for ($i = 0; $i < count($secret); $i = $i + 8) {
            $x = '';
            if (!in_array($secret[$i], $base32chars)) {
                return false;
            }
            for ($j = 0; $j < 8; ++$j) {
                $x .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for ($z = 0; $z < count($eightBits); ++$z) {
                $binaryString .= (($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48) ? $y : '';
            }
        }

        return $binaryString;
    }

    /**
     * @return array
     */
    protected function getBase32LookupTable()
    {
        return [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H', //  7
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P', // 15
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X', // 23
            'Y',
            'Z',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7', // 31
            '=', // padding char
        ];
    }

    /**
     * @param string $safeString
     * @param string $userString
     *
     * @return bool
     */
    private function timingSafeEquals(string $safeString, string $userString): bool
    {
        if (function_exists('hash_equals')) {
            return hash_equals($safeString, $userString);
        }
        $safeLen = strlen($safeString);
        $userLen = strlen($userString);
        if ($userLen != $safeLen) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < $userLen; ++$i) {
            $result |= (ord($safeString[$i]) ^ ord($userString[$i]));
        }

        return $result === 0;
    }
}