<?php

namespace Leon\BswBundle\Controller;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorException;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Error\Entity\ErrorSession;
use Leon\BswBundle\Module\Error\Entity\ErrorSignature;
use Leon\BswBundle\Controller\Traits as CT;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Leon\BswBundle\Module\Error\Error;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Exception;

abstract class BswApiController extends AbstractFOSRestController
{
    use CT\Foundation,
        CT\ApiResponse;

    /**
     * @var string
     */
    protected $appType = Abs::APP_TYPE_API;

    /**
     * @var bool given sign dynamic
     */
    protected $signDynamic = false;

    /**
     * @var bool is development environment
     */
    protected $development = false;

    /**
     * Bootstrap
     */
    protected function bootstrap()
    {
        foreach (['time', 'sign-close', 'sign-debug'] as $field) {
            $this->header->{$field} = intval($this->header->{$field});
        }

        // for development
        if ($this->debug && !empty($this->header->postman)) {
            $this->development = true;
        }
    }

    /**
     * Valid args
     *
     * @param int  $type
     * @param bool $showAllError
     *
     * @return object|Response
     * @throws
     */
    final protected function valid(int $type = Abs::V_LOGIN, bool $showAllError = false)
    {
        $this->iNeedCost(Abs::BEGIN_VALID);

        /**
         * sign assist
         */

        $this->signDynamic = $this->header->{'sign-dynamic'};
        if (!empty($this->signDynamic)) {
            [$dynamic] = $this->TOTPToken('sign');
            $this->signDynamic = ($this->signDynamic === md5($dynamic));
        }

        /**
         * validator
         */

        $caller = Helper::backtrace(1, ['class', 'function']);
        $annotation = $this->getInputAnnotation($caller['class'], $caller['function']);

        [$error, $args, $sign, $validator] = $this->parametersValidator($annotation);

        /**
         * show error
         */

        if (!empty($error)) {

            if ($showAllError) {
                $message = array_merge(...array_values($error));
                $message = implode(Abs::ENTER, $message);
                $errorCls = ErrorParameter::class;
            } else {
                $message = current(current($error));
                $errorCls = key($error);
            }

            $this->iNeedCost(Abs::END_VALID);

            return $this->failed(new $errorCls, $message);
        }

        foreach ($validator as $field => $item) {
            $result = call_user_func_array([$this, $item['validator']], [$item['value'], $args]);
            if ($result instanceof Response) {
                $this->iNeedCost(Abs::END_VALID);

                return $result;
            }
        }

        $signNeed = Helper::bitFlagAssert($type, Abs::V_SIGN);
        $signClose = ($this->development || $this->signDynamic) && !empty($this->header->{'sign-close'});
        $signDebug = ($this->development || $this->signDynamic) && !empty($this->header->{'sign-debug'});

        /**
         * signature
         */

        if ($signNeed && !$signClose) {

            $oldSign = $this->apiOldSign($args);
            $newSign = $this->apiNewSign($sign, $signDebug);

            if ($oldSign !== $newSign) {

                $message = $this->messageLang('Signature verification failed');
                $this->logger->error($message, [compact('oldSign', 'newSign')]);
                $this->dispatchMethod(Abs::FN_SIGN_FAILED, null, [$this->route]);

                if ($this->development || $this->signDynamic) {
                    $message .= " (Signature: {$newSign})";
                }

                $this->iNeedCost(Abs::END_VALID);

                return $this->failed(new ErrorSignature(), $message);
            }
        }

        /**
         * should auth
         */

        if (Helper::bitFlagAssert($type, Abs::V_SHOULD_AUTH)) {

            $isAuth = $this->apiShouldAuth($args);

            /**
             * auth failed
             */

            if ($isAuth instanceof Error) {

                if (Helper::bitFlagAssert($type, Abs::V_MUST_AUTH)) {

                    $this->logger->warning($this->messageLang($isAuth->description()));
                    $this->iNeedCost(Abs::END_VALID);

                    return $this->failed($isAuth);
                }

            } else {

                $this->usr = (object)$isAuth;

                /**
                 * strict authorization
                 */

                if ($this->usrStrict && Helper::bitFlagAssert($type, Abs::V_STRICT_AUTH)) {
                    $strictHandling = $this->dispatchMethod(Abs::FN_STRICT_AUTH);

                    if ($strictHandling !== true) {

                        $error = ($strictHandling instanceof Error) ? $strictHandling : new ErrorSession();
                        $this->logger->warning($this->messageLang($error->description()));
                        $this->iNeedCost(Abs::END_VALID);

                        return $this->failed($error);
                    }
                }
            }
        }

        $this->iNeedCost(Abs::END_VALID);

        return (object)$args;
    }

    /**
     * Get old sign
     *
     * @param array $args
     *
     * @return string
     */
    abstract protected function apiOldSign(array $args): string;

    /**
     * Get new sign
     *
     * @param array $args
     * @param bool  $debug
     *
     * @return string
     */
    abstract protected function apiNewSign(array $args, bool $debug = false): string;

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error
     */
    abstract protected function apiShouldAuth(array $args);

    /**
     * Converts an Exception to a Response
     *
     * @param Request             $request
     * @param Exception|Throwable $exception
     *
     * @return Response
     * @throws
     */
    public function showExceptionAction(Request $request, $exception): Response
    {
        if ($request->getRequestFormat() == Abs::FORMAT_HTML) {
            if ($exception instanceof Throwable) {
                throw $exception;
            }
            $this->logger->error('Exception trace -->', $exception->getTrace());
            throw new Exception($exception->getMessage());
        }

        $message = $this->errorHandler(
            "{$exception->getMessage()} in {$exception->getFile()} line {$exception->getLine()}",
            $exception->getTrace()
        );

        // default http code
        $code4http = Response::HTTP_INTERNAL_SERVER_ERROR;

        // http exception
        if ($exception instanceof HttpExceptionInterface) {
            $code4http = $exception->getStatusCode();
        }

        return $this->response(ErrorException::CODE, $code4http, $message);
    }
}