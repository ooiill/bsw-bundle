<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use Exception;

/**
 * @property AbstractController  $container
 * @property TranslatorInterface $translator
 * @property LoggerInterface     $logger
 */
trait WebResponse
{
    /**
     * @var int
     */
    protected $codeOkForLogic = 0;

    /**
     * @var bool
     */
    protected $langErrorTiny = true;

    /**
     * @inheritdoc
     *
     * @param int $codeOk
     *
     * @return array
     */
    public function responseKeysAjax(int $codeOk = 0): array
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
                'demo'  => HttpResponse::HTTP_OK,
                'notes' => 'Http code',
            ],
            'message'    => [
                'key'   => 'message',
                'demo'  => '""',
                'notes' => 'Logic code {{ ok }} means error, otherwise success message',
            ],
            'classify'   => [
                'key'   => 'classify',
                'demo'  => '"warning"',
                'notes' => 'Message classify for frontend',
            ],
            'type'       => [
                'key'   => 'type',
                'demo'  => '"message"',
                'notes' => 'Popup type for frontend',
            ],
            'duration'   => [
                'key'   => 'duration',
                'demo'  => '3',
                'notes' => 'Second for message keep',
            ],
            'data'       => [
                'key'   => 'sets',
                'demo'  => null,
                'notes' => 'Data when logic code is {{ ok }}',
            ],
        ];
    }

    /**
     * Resolve args for query params and trans args
     *
     * @param array $args
     *
     * @return array
     */
    public function resolveArgs(array $args): array
    {
        $params = $trans = [];
        foreach ($args as $key => $val) {
            if (strpos($key, '{{') === 0) {
                $trans[$key] = $val;
            } else {
                $params[$key] = $val;
            }
        }

        return [$params, $trans];
    }

    /**
     * Format response - ajax
     *
     * @param int    $code4logic
     * @param int    $code4http
     * @param string $message
     * @param array  $data
     * @param string $classify
     * @param string $type
     * @param int    $duration
     *
     * @return JsonResponse
     * @throws
     */
    public function responseAjax(
        int $code4logic,
        int $code4http,
        string $message = null,
        array $data = [],
        string $classify = Abs::TAG_CLASSIFY_INFO,
        string $type = Abs::TAG_TYPE_MESSAGE,
        ?int $duration = null
    ): JsonResponse {

        if (!$this->debug && strpos($message, Abs::FLAG_SQL_ERROR) !== false) {
            throw new Exception($message);
        }

        $response = [];
        $responseKeys = $this->responseKeysAjax();
        unset($responseKeys['args']);

        foreach ($responseKeys as $key => $item) {
            if (empty($item)) {
                continue;
            }
            $value = $$key ?? null;
            $response[$item['key']] = $value;
        }

        // $this->logger->debug('Response data as follow', $response);
        $this->logger->debug("-->> end: {$this->route}");

        $this->iNeedCost(Abs::END_REQUEST);
        $this->iNeedLogger(Abs::END_REQUEST);

        return new JsonResponse($response, $code4http);
    }

    /**
     * Okay with params (auto lang) - ajax
     *
     * @param array|object $data
     * @param string       $message
     * @param array        $args
     * @param int          $duration
     *
     * @return JsonResponse
     * @throws
     */
    public function okayAjax($data, string $message = '', array $args = [], ?int $duration = null): JsonResponse
    {
        if (is_object($data)) {
            $data = Helper::entityToArray($data);
        }

        if ($message) {
            $message = $this->messageLang($message, $args);
        }

        return $this->responseAjax(
            $this->codeOkForLogic,
            HttpResponse::HTTP_OK,
            $message,
            $data,
            Abs::TAG_CLASSIFY_SUCCESS,
            Abs::TAG_TYPE_MESSAGE,
            $duration
        );
    }

    /**
     * Success with message (auto lang) - ajax
     *
     * @param string $message
     * @param array  $args
     * @param int    $duration
     *
     * @return JsonResponse
     * @throws
     */
    public function successAjax(string $message, array $args = [], ?int $duration = null): JsonResponse
    {
        [$data, $trans] = $this->resolveArgs($args);

        if ($message) {
            $message = $this->messageLang($message, $trans);
        }

        return $this->responseAjax(
            $this->codeOkForLogic,
            HttpResponse::HTTP_OK,
            $message,
            $data,
            Abs::TAG_CLASSIFY_SUCCESS,
            Abs::TAG_TYPE_MESSAGE,
            $duration
        );
    }

    /**
     * Success with message (auto lang) - ajax
     *
     * @param int|Error $code
     * @param string    $message
     * @param array     $args
     * @param int       $duration
     *
     * @return JsonResponse
     * @throws
     */
    public function failedAjax($code, string $message = '', array $args = [], ?int $duration = null): JsonResponse
    {
        [$data, $trans] = $this->resolveArgs($args);
        [$code4http, $code4logic, $tiny, $detail] = [HttpResponse::HTTP_OK, $code, null, null];

        // instance of Error
        if ($code instanceof Error) {
            [$code4http, $code4logic, $tiny, $detail] = $code->all();
        }

        $message && $tiny = $detail = $message;

        // lang for tiny
        if (($tiny && $this->langErrorTiny) || $message) {
            $tiny = $this->messageLang($tiny, $trans);
        }

        // error type
        if (!is_int($code4logic)) {
            throw new InvalidArgumentException('Args `code4logic` must be integer');
        }

        // logger description
        if ($detail) {
            $detail = $this->messageLang($detail, $trans);
            $this->logger->warning("Ajax response failed, {$detail}");
        }

        return $this->responseAjax(
            $code4logic,
            $code4http,
            $tiny,
            $data,
            Abs::TAG_CLASSIFY_ERROR,
            Abs::TAG_TYPE_MESSAGE,
            $duration
        );
    }
}