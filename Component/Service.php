<?php

namespace Leon\BswBundle\Component;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDebugExit;
use Leon\BswBundle\Module\Exception\CurlException;
use Leon\BswBundle\Module\Exception\ServiceException;

class Service
{
    /**
     * @var string
     */
    protected $method = Abs::REQ_GET;

    /**
     * @var string
     */
    protected $contentType = Abs::CONTENT_TYPE_FORM;

    /**
     * @var string
     */
    protected $scheme = Abs::SCHEME_HTTP;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port = 80;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $query = [];

    /**
     * @var array
     */
    protected $header = [];

    /**
     * @var int
     */
    protected $timeout = 30000;

    /**
     * @var bool
     */
    protected $async = false;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @param string $method
     *
     * @return Service|static
     * @throws
     */
    public function method(string $method): Service
    {
        $this->method = $method;

        return $this->methodChecker();
    }

    /**
     * @return Service|static
     * @throws
     */
    protected function methodChecker(): Service
    {
        $methods = [
            Abs::REQ_GET,
            Abs::REQ_POST,
            Abs::REQ_DELETE,
            Abs::REQ_HEAD,
        ];

        if (!in_array($this->method, $methods)) {
            throw new ServiceException('Method must in ' . implode('/', $methods));
        }

        return $this;
    }

    /**
     * @param string $type
     *
     * @return Service|static
     * @throws
     */
    public function contentType(string $type): Service
    {
        $this->contentType = $type;

        return $this->contentTypeChecker();
    }

    /**
     * @return Service|static
     * @throws
     */
    protected function contentTypeChecker(): Service
    {
        $types = [
            Abs::CONTENT_TYPE_FORM,
            Abs::CONTENT_TYPE_JSON,
            '',
        ];

        if (!in_array($this->contentType, $types)) {
            throw new ServiceException('Content type must in ' . implode('/', $types));
        }

        return $this;
    }

    /**
     * @param string $scheme
     *
     * @return Service|static
     * @throws
     */
    public function scheme(string $scheme): Service
    {
        $this->scheme = $scheme;
        if ($scheme == Abs::SCHEME_HTTPS) {
            $this->port = 443;
        }

        return $this->schemeChecker();
    }

    /**
     * @return Service|static
     * @throws
     */
    protected function schemeChecker(): Service
    {
        $scheme = [
            Abs::SCHEME_HTTP,
            Abs::SCHEME_HTTPS,
        ];

        if (!in_array($this->scheme, $scheme)) {
            throw new ServiceException('Scheme must in ' . implode('/', $scheme));
        }

        return $this;
    }

    /**
     * @param string $host
     *
     * @return Service|static
     * @throws
     */
    public function host(string $host): Service
    {
        $items = parse_url($host);

        if (isset($items['scheme'])) {
            $this->scheme($items['scheme']);
        }

        if (isset($items['port'])) {
            $this->port($items['port']);
        }

        if (isset($items['host'])) {
            $this->host = trim($items['host'], '/ ');
        }

        return $this->hostChecker();
    }

    /**
     * @param bool $strict
     *
     * @return Service|static
     * @throws
     */
    protected function hostChecker(bool $strict = false): Service
    {
        if (empty($this->host)) {
            throw new ServiceException('Host must be configured');
        }

        if ($strict && strpos($this->host, ':')) {
            throw new ServiceException('Host contain special char `:`');
        }

        return $this;
    }

    /**
     * @param int $port
     *
     * @return Service|static
     * @throws
     */
    public function port(int $port): Service
    {
        $this->port = $port;

        return $this->portChecker();
    }

    /**
     * @return Service|static
     * @throws
     */
    protected function portChecker(): Service
    {
        if ($this->port < 1 || $this->port > 65535) {
            throw new ServiceException('Port must between 1 and 65535');
        }

        return $this;
    }

    /**
     * @param string $path
     *
     * @return Service|static
     */
    public function path(string $path): Service
    {
        $this->path = trim($path, '/ ');

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Service|static
     */
    public function header(array $args): Service
    {
        $this->header = $args;

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Service|static
     */
    public function args(array $args): Service
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Service|static
     */
    public function query(array $args): Service
    {
        $this->query = $args;

        return $this;
    }

    /**
     * @param int $millisecond
     *
     * @return Service|static
     * @throws
     */
    public function timeout(int $millisecond): Service
    {
        $this->timeout = $millisecond;

        return $this->timeoutChecker();
    }

    /**
     * @return Service|static
     * @throws
     */
    protected function timeoutChecker(): Service
    {
        if ($this->timeout < 0 || $this->timeout > 300000) {
            throw new ServiceException('Timeout must between 1 and 30');
        }

        return $this;
    }

    /**
     * @param bool $async
     *
     * @return Service|static
     */
    public function async(bool $async = true): Service
    {
        $this->async = $async;

        return $this;
    }

    /**
     * @param bool $debug
     *
     * @return Service|static
     */
    public function debug(bool $debug = true): Service
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * @return mixed
     * @throws
     */
    protected function curl()
    {
        $options = [];

        // https
        if ($this->scheme == Abs::SCHEME_HTTPS) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = false;
        }

        // timeout
        $options[CURLOPT_NOSIGNAL] = true;
        $options[CURLOPT_TIMEOUT_MS] = $this->async ? 100 : $this->timeout;

        // enabled show header
        $options[CURLOPT_HEADER] = false;

        // enabled auto show return info
        $options[CURLOPT_RETURNTRANSFER] = true;

        // connect
        $options[CURLOPT_FRESH_CONNECT] = true;
        $options[CURLOPT_FORBID_REUSE] = true;

        // url
        $portString = ":{$this->port}";
        if ($this->scheme == Abs::SCHEME_HTTP && $this->port == 80) {
            $portString = null;
        } elseif ($this->scheme == Abs::SCHEME_HTTPS && $this->port == 443) {
            $portString = null;
        }

        $this->url = "{$this->scheme}://{$this->host}{$portString}/{$this->path}";
        if ($this->query) {
            $this->url = Helper::httpBuildQuery($this->query, $this->url);
        }

        if (in_array($this->method, [Abs::REQ_GET, Abs::REQ_DELETE])) {
            $this->url = Helper::httpBuildQuery($this->args, $this->url);
        }

        // content type
        if ($this->contentType) {
            $options[CURLOPT_HTTPHEADER] = ["Content-Type: {$this->contentType}"];
        }

        $options[CURLOPT_URL] = $this->url;

        // method
        if ($this->method == Abs::REQ_HEAD) { // HEAD

            $options[CURLOPT_NOBODY] = true;
            $options[CURLOPT_HEADER] = true;

        } elseif ($this->method == Abs::REQ_POST) { // POST

            $options[CURLOPT_POST] = true;
            if ($this->args) {
                if ($this->contentType == Abs::CONTENT_TYPE_FORM) {
                    $options[CURLOPT_POSTFIELDS] = http_build_query($this->args);
                } elseif ($this->contentType == Abs::CONTENT_TYPE_JSON) {
                    $options[CURLOPT_POSTFIELDS] = Helper::jsonStringify($this->args);
                } else {
                    $options[CURLOPT_POSTFIELDS] = $this->args;
                }
            }
        }

        $options[CURLOPT_CUSTOMREQUEST] = $this->method;

        // header
        $header = [];
        foreach ($this->header as $key => $value) {
            array_push($header, "{$key}: {$value}");
        }
        $options[CURLOPT_HTTPHEADER] = $header;

        // debug
        if ($this->debug) {
            dump($options);
            exit(ErrorDebugExit::CODE);
        }

        // request
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $content = curl_exec($curl);

        if ($content === false) {
            $error = curl_error($curl);
            throw new CurlException("cURL error when request {$this->url}, {$error}");
        }

        return $content;
    }

    /**
     * @return mixed
     * @throws
     */
    public function request()
    {
        $part = parse_url($this->host);
        foreach (['scheme', 'host', 'port'] as $item) {
            !empty($part[$item]) && $this->{$item}($part[$item]);
        }

        $resultHandling = $this
            ->methodChecker()
            ->schemeChecker()
            ->hostChecker()
            ->portChecker()
            ->timeoutChecker()
            ->curl();

        if (!$result = Helper::jsonArray($resultHandling)) {
            throw new ServiceException($resultHandling);
        }

        return $result;
    }
}