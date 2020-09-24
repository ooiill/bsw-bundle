<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Request as SfRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;

/**
 * @property AbstractController $container
 * @property LoggerInterface    $logger
 */
trait Request
{
    /**
     * Args for $_POST
     *
     * @param null $appoint
     * @param bool $filterHtml
     *
     * @return mixed
     */
    public function postArgs($appoint = null, bool $filterHtml = true)
    {
        return $this->args(Abs::REQ_POST, $appoint, $filterHtml);
    }

    /**
     * Args for $_GET
     *
     * @param null $appoint
     * @param bool $filterHtml
     *
     * @return mixed
     */
    public function getArgs($appoint = null, bool $filterHtml = true)
    {
        return $this->args(Abs::REQ_GET, $appoint, $filterHtml);
    }

    /**
     * Args for symfony route
     *
     * @param null $appoint
     * @param bool $filterHtml
     *
     * @return mixed
     */
    public function routeArgs($appoint = null, bool $filterHtml = true)
    {
        return $this->args(Abs::REQ_SYMFONY, $appoint, $filterHtml);
    }

    /**
     * Args for $_HEAD
     *
     * @param null $appoint
     * @param bool $filterHtml
     *
     * @return mixed
     */
    public function headArgs($appoint = null, bool $filterHtml = true)
    {
        return $this->args(Abs::REQ_HEAD, $appoint, $filterHtml);
    }

    /**
     * Args for $_GET, $_POST and $_HEAD
     *
     * @param null $appoint
     * @param bool $filterHtml
     *
     * @return mixed
     */
    public function allArgs($appoint = null, bool $filterHtml = true)
    {
        return $this->args(Abs::REQ_ALL, $appoint, $filterHtml);
    }

    /**
     * Get params from request
     *
     * @param string $type
     * @param mixed  $appoint
     * @param bool   $filterHtml
     *
     * @return mixed
     */
    public function args(string $type = Abs::REQ_ALL, $appoint = null, bool $filterHtml = true)
    {
        static $args = [], $argsClean = [];

        if (!isset($args[$type]) || !isset($argsClean[$type])) {

            $input = file_get_contents("php://input", 'r');
            $input = json_decode($input, true) ?? [];

            /**
             * @var $request SfRequest
             */
            $request = $this->request();
            $header = function () use ($request) {
                $header = $request->headers->all();
                foreach ($header as $key => &$item) {
                    $item = current($item);
                }

                return $header;
            };

            switch ($type) {

                case Abs::REQ_POST:
                case Abs::REQ_PATCH:
                    $args[$type] = array_merge(
                        $input,
                        $request->request->all(),
                    );
                    break;

                case Abs::REQ_GET:
                case Abs::REQ_DELETE:
                    $args[$type] = array_merge(
                        $input,
                        $request->query->all()
                    );
                    break;

                case Abs::REQ_SYMFONY:
                    $args[$type] = $request->attributes->get('_route_params');
                    break;

                case Abs::REQ_HEAD:
                    $args[$type] = $header();
                    break;

                case Abs::REQ_ALL:
                    $args[$type] = array_merge(
                        $input,
                        $request->request->all(),
                        $request->query->all(),
                        $request->attributes->get('_route_params'),
                        $header()
                    );
                    break;

                default:
                    $args[$type] = [];
                    break;
            }

            $argsClean[$type] = Html::cleanArrayHtml($args[$type]);
        }

        return Helper::arrayAppoint($filterHtml ? $argsClean[$type] : $args[$type], $appoint);
    }

    /**
     * Get request record
     *
     * @param bool $jsonEncode
     *
     * @return array|string
     */
    public function requestRecord(bool $jsonEncode = false)
    {
        /**
         * @var $request SfRequest
         */
        $request = $this->request();

        $data = [
            'CHECK'       => [
                'route'  => $this->route,
                'method' => $request->getRealMethod(),
                'uri'    => $this->host() . $request->getRequestUri(),
                'ip'     => $this->getClientIp(),
                'locale' => $this->header->lang,
            ],
            'SERVER'      => $request->server->all(),
            'FILES'       => $request->files->all(),
            'COOKIE'      => $request->cookies->all(),
            Abs::REQ_GET  => $this->getArgs(),
            Abs::REQ_POST => $this->postArgs(),
            Abs::REQ_HEAD => $this->headArgs(),
        ];

        return $jsonEncode ? Helper::jsonStringify($data) : $data;
    }

    /**
     * Logger -> classify
     *
     * @param string $classify
     * @param string $message
     * @param array  $args
     */
    public function logClassify(string $classify, string $message, array $args = [])
    {
        $requestArgs = $this->requestRecord();
        $this->logger->{$classify}($message, $args);

        $this->logger->debug('Args $_CHECK', $requestArgs['CHECK']);
        $this->logger->debug('Args $_FILES', $requestArgs['FILES']);
        $this->logger->debug('Args $_GET', $requestArgs[Abs::REQ_GET]);
        $this->logger->debug('Args $_POST', $requestArgs[Abs::REQ_POST]);
        $this->logger->debug('Args $_HEAD', $requestArgs[Abs::REQ_HEAD]);
    }

    /**
     * Logger warning
     *
     * @param string $message
     * @param array  $args
     */
    public function logWarning(string $message, array $args = [])
    {
        $this->logClassify('warning', $message, $args);
    }

    /**
     * Logger error
     *
     * @param string $message
     * @param array  $args
     */
    public function logError(string $message, array $args = [])
    {
        $this->logClassify('error', $message, $args);
    }

    /**
     * List route key value pair
     *
     * @param bool $sortByKey
     * @param bool $labelUseUri
     *
     * @return array
     */
    public function routeKVP(bool $sortByKey = null, bool $labelUseUri = false): array
    {
        $route = $this->getRouteCollection();
        $routeList = array_column($route, $labelUseUri ? 'uri' : 'route', 'route');

        if ($sortByKey === true) {
            ksort($routeList);
        } elseif ($sortByKey === false) {
            asort($routeList);
        }

        return $routeList;
    }
}