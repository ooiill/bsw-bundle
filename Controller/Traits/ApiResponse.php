<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use InvalidArgumentException;
use Exception;

/**
 * @property AbstractFOSRestController $container
 * @property TranslatorInterface       $translator
 * @property LoggerInterface           $logger
 */
trait ApiResponse
{
    /**
     * @var int
     */
    protected $codeOkForLogic = 0;

    /**
     * @var bool
     */
    protected $strongSetType = true;

    /**
     * @var bool
     */
    protected $langErrorTiny = true;

    /**
     * @var bool
     */
    protected $responseEncrypt = true;

    /**
     * @var array
     */
    public $acmeController = [];

    /**
     * @inheritdoc
     *
     * @param int $codeOk
     *
     * @return array
     */
    public function responseKeys(int $codeOk = 0): array
    {
        return [
            'args'       => [
                '{{ ok }}' => $codeOk,
            ],
            'code4logic' => [
                'key'   => 'error',
                'demo'  => $codeOk,
                'notes' => 'Logic code, {{ ok }} is success and others means failed',
            ],
            'code4http'  => [
                'key'   => 'code',
                'demo'  => Response::HTTP_OK,
                'notes' => 'Http code',
            ],
            'message'    => [
                'key'   => 'message',
                'demo'  => '""',
                'notes' => 'Logic code {{ ok }} means error, otherwise success message',
            ],
            'data'       => [
                'key'   => 'sets',
                'demo'  => null,
                'notes' => 'Data when logic code is {{ ok }}',
            ],
        ];
    }

    /**
     * Format response original
     *
     * @param array $data
     * @param int   $code4http
     *
     * @return Response
     */
    public function original(array $data, int $code4http = Response::HTTP_OK): Response
    {
        $this->logger->debug('Response data as follow', $data);
        if ($this->responseEncrypt) {
            $data = $this->dispatchMethod(Abs::FN_BEFORE_RESPONSE, $data, [$data]);
        }

        $view = $this->view($data, $code4http);
        $view = $this->handleView($view);

        /**
         * @var Response $view
         */
        if (is_string($content = $view->getContent())) {
            $view->setContent(trim($content, '"'));
        }

        $this->logger->debug("-->> end: $this->route");

        $this->iNeedCost(Abs::END_REQUEST);
        $this->iNeedLogger(Abs::END_REQUEST);

        return $view;
    }

    /**
     * Strong type for sets with reflect
     *
     * @param $value
     *
     * @return mixed
     */
    public function strongSetTypeByReflect($value)
    {
        if (!empty($value) || !$this->strongSetType) {
            return $value;
        }

        $backtrace = Helper::backtrace(-1, ['class', 'function']);
        $routes = $this->getRouteCollection();

        foreach ($backtrace as $item) {

            [$class, $method] = [$item['class'] ?? null, $item['function']];
            $key = "{$class}::{$method}";

            if ($class != static::class) {
                continue;
            }

            if (in_array($class, $this->acmeController)) {
                continue;
            }

            $type = $routes[$key]['tutorial'] ?? 'object';
            switch (strtolower($type)) {

                case 'array':
                case 'array[]':
                case 'object[]':
                    $this->logger->debug("Strong set type to array in route {$key}");
                    $value = (array)$value;
                    break;

                case 'object':
                    $this->logger->debug("Strong set type to object in route {$key}");
                    $value = (object)$value;
                    break;
            }
        }

        return $value;
    }

    /**
     * Format response
     *
     * @param int    $code4logic
     * @param int    $code4http
     * @param string $message
     * @param array  $data
     *
     * @return Response
     * @throws
     */
    public function response(int $code4logic, int $code4http, string $message = null, array $data = []): Response
    {
        $response = [];
        if (strpos($message, Abs::FLAG_SQL_ERROR) !== false) {
            throw new Exception($message);
        }

        $arguments = [$code4logic, $code4http, $message, $data];
        $result = $this->dispatchMethod(Abs::FN_BEFORE_RESPONSE_CODE, $arguments, $arguments);
        [$code4logic, $code4http, $message, $data] = $result;

        $responseKeys = $this->responseKeys();
        unset($responseKeys['args']);

        foreach ($responseKeys as $key => $item) {
            if (empty($item)) {
                continue;
            }

            $value = $$key ?? null;
            if ($key == 'data') {
                $value = $this->strongSetTypeByReflect($value);
            }

            $response[$item['key']] = $value;
        }

        return $this->original($response, $code4http);
    }

    /**
     * Okay with params (auto lang)
     *
     * @param array|object $data
     * @param string       $message
     * @param array        $args
     *
     * @return Response
     * @throws
     */
    public function okay($data, string $message = '', array $args = []): Response
    {
        if (is_object($data)) {
            $data = Helper::entityToArray($data);
        }

        if ($message) {
            $message = $this->messageLang($message, $args);
        }

        return $this->response($this->codeOkForLogic, Response::HTTP_OK, $message, $data);
    }

    /**
     * Success with message (auto lang)
     *
     * @param string $message
     * @param array  $args
     *
     * @return Response
     * @throws
     */
    public function success(string $message, array $args = []): Response
    {
        if ($message) {
            $message = $this->messageLang($message, $args);
        }

        return $this->response($this->codeOkForLogic, Response::HTTP_OK, $message);
    }

    /**
     * Failed with message (auto lang)
     *
     * @param int|Error $code
     * @param string    $message
     * @param array     $args
     *
     * @return Response
     * @throws
     */
    public function failed($code, string $message = '', array $args = []): Response
    {
        [$code4http, $code4logic, $tiny, $detail] = [Response::HTTP_OK, $code, null, null];

        // instance of Error
        if ($code instanceof Error) {
            [$code4http, $code4logic, $tiny, $detail] = $code->all();
        }

        $message && $tiny = $detail = $message;

        // lang for tiny
        if (($tiny && $this->langErrorTiny) || $message) {
            $tiny = $this->messageLang($tiny, $args);
        }

        // error type
        if (!is_int($code4logic)) {
            throw new InvalidArgumentException('Args `code4logic` must be integer');
        }

        // logger description
        if ($detail) {
            $detail = $this->messageLang($detail, $args);
            $this->logger->warning("Response failed, {$detail}");
        }

        return $this->response($code4logic, $code4http, $tiny);
    }
}