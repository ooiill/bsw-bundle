<?php

namespace Leon\BswBundle\Controller;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorAccess;
use Leon\BswBundle\Module\Error\Entity\ErrorAjaxRequest;
use Leon\BswBundle\Module\Error\Entity\ErrorAuthorization;
use Leon\BswBundle\Module\Error\Entity\ErrorException;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Error\Entity\ErrorSession;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Controller\Traits as CT;
use Leon\BswBundle\Module\Scene\Crumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Leon\BswBundle\Module\Bsw as BswModule;
use Exception;
use Throwable;

abstract class BswWebController extends AbstractController
{
    use CT\Foundation,
        CT\WebAccess,
        CT\WebArgs,
        CT\WebCrumbs,
        CT\WebResponse,
        CT\WebSeo,
        CT\WebSession,
        CT\WebFlash,
        CT\WebSource;

    /**
     * @var string
     */
    protected $version = '2.5.2';

    /**
     * @var string
     */
    protected $appType = Abs::APP_TYPE_WEB;

    /**
     * @var bool
     */
    protected $ajax;

    /**
     * @var bool
     */
    protected $mobile;

    /**
     * @var bool
     */
    protected $iframe;

    /**
     * @var string
     */
    protected $skUser = 'user-session-key';

    /**
     * @var string
     */
    protected $skCaptcha = 'captcha-session-key';

    /**
     * @var array
     */
    public $langMap = ['cn' => 'cn', 'hk' => 'hk', 'en' => 'en'];

    /**
     * @var array
     */
    public $langFieldMap = ['cn' => 'cn', 'en' => 'en'];

    /**
     * Bootstrap
     */
    protected function bootstrap()
    {
        $this->ajax = $this->request()->isXmlHttpRequest();
        $this->mobile = Helper::isMobile();
        $this->iframe = !!$this->getArgs('iframe');

        // history for last time
        $args = $this->getArgs();

        // don't history when iframe
        if (($args['iframe'] ?? null) !== null) {
            return;
        }

        // don't history when export
        if (($args['scene'] ?? null) === Abs::TAG_EXPORT) {
            return;
        }

        // don't history when configure exclude route
        $exclude = $this->parameters('route_exclude_history', false);
        if (is_null($this->route) || in_array($this->route, $exclude)) {
            return;
        }

        $this->sessionArraySet(Abs::TAG_HISTORY, $this->route, $args);
    }

    /**
     * Create redirect url
     *
     * @param string $url
     * @param array  $args
     *
     * @return string
     */
    public function redirectUrl(string $url = null, array $args = [])
    {
        if ($url && Helper::isUrlAlready($url)) {
            return $url;
        }

        // from crumbs
        $crumbs = count($this->crumbs) - 2;
        if (!$url && ($crumb = $this->crumbs[$crumbs] ?? null)) {

            /**
             * @var Crumb $crumb
             */
            $url = $crumb->getRoute();
        }

        $access = array_filter($this->access);

        // from default route
        if (!$url && isset($access[$this->cnf->route_default])) {
            $url = $this->cnf->route_default;
        }

        // prevent route to self
        if (!$url) {
            $access = array_filter($this->access);
            foreach ($access as $route => $assert) {
                if (strpos($route, Abs::TAG_PREVIEW) !== false && strpos($route, Abs::TAG_EXPORT) === false) {
                    $url = $route;
                    break;
                }
            }
            if (empty($url)) {
                $url = $this->cnf->route_login;
            }
        }

        // args
        if (!empty($url)) {
            $args = (array)$this->sessionArrayGet(Abs::TAG_HISTORY, $url, true);
        }

        return $this->url($url, $args);
    }

    /**
     * Get history route by index
     *
     * @param int $index
     *
     * @return string|null
     */
    public function getHistoryRoute(int $index = -1): ?string
    {
        $history = array_keys($this->session->get(Abs::TAG_HISTORY));
        $route = array_slice($history, $index, 1);

        if (empty($route)) {
            return null;
        }

        return current($route);
    }

    /**
     * Sek cookie
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expire
     *
     * @return void
     */
    public function setCookie(string $key, $value, int $expire = Abs::TIME_HOUR)
    {
        $cookie = new Cookie($key, $value, time() + $expire);
        $this->response->headers->setCookie($cookie);
    }

    /**
     * Response url map
     *
     * @param int $code
     *
     * @return string
     */
    public function responseUrlMap(int $code): ?string
    {
        $reference = $this->reference();

        if ($code == ErrorAccess::CODE) {
            if ($this->route == $this->cnf->route_default) {
                return null;
            }
            if (strpos($reference, 'login') !== false) {
                return null;
            }
        }

        $map = [
            ErrorAjaxRequest::CODE   => $reference,
            ErrorParameter::CODE     => $reference,
            ErrorAuthorization::CODE => $this->cnf->route_login,
            ErrorAccess::CODE        => $reference,
            ErrorSession::CODE       => $this->cnf->route_login,
        ];

        return $map[$code] ?? null;
    }

    /**
     * Response success (auto ajax)
     *
     * @param string $message
     * @param array  $args
     * @param string $url
     *
     * @return Response
     */
    public function responseSuccess(string $message, array $args = [], string $url = null): Response
    {
        if ($this->ajax) {
            return $this->successAjax($message, $args);
        }

        [$params, $trans] = $this->resolveArgs($args);

        $message = (new Message())
            ->setMessage($this->messageLang($message, $trans))
            ->setRoute($this->redirectUrl($url))
            ->setArgs($params)
            ->setClassify(Abs::TAG_CLASSIFY_SUCCESS);

        return $this->responseMessage($message);
    }

    /**
     * Response error (auto ajax)
     *
     * @param int|Error $code
     * @param string    $message
     * @param array     $args
     * @param string    $url
     *
     * @return Response
     */
    public function responseError($code, string $message = '', array $args = [], string $url = null): Response
    {
        [$code4logic, $tiny, $detail] = [$code, null, null];

        // instance of Error
        if ($code instanceof Error) {
            [$_, $code4logic, $tiny, $detail] = $code->all();
        }

        if ($this->ajax) {
            return $this->failedAjax($code, $message, $args);
        }

        $message && $tiny = $detail = $message;

        // lang for tiny
        [$params, $trans] = $this->resolveArgs($args);
        if (($tiny && $this->langErrorTiny) || $message) {
            $tiny = $this->messageLang($tiny, $trans);
        }

        // logger description
        if ($detail) {
            $detail = $this->messageLang($detail, $trans);
            $this->logger->warning("Response error, [{$code4logic}] {$tiny}, {$detail}");
        }

        // fallback url
        if ($code4logic == ErrorAuthorization::CODE) {
            $get = $this->getArgs();
            if (empty($get['iframe']) && ($get['scene'] ?? null) !== Abs::TAG_EXPORT) {
                $this->addFlash(Abs::TAG_FALLBACK, $this->currentUrl());
            }
        }

        $message = (new Message())
            ->setMessage("[{$code4logic}] {$tiny}")
            ->setCode($code4logic)
            ->setRoute($url)
            ->setArgs($params)
            ->setClassify(Abs::TAG_CLASSIFY_ERROR);

        return $this->responseMessage($message);
    }

    /**
     * Response message (just latest message)
     *
     * @param Message $message
     *
     * @return Response
     * @throws
     */
    public function responseMessage(Message $message): Response
    {
        [$params, $trans] = $this->resolveArgs($message->getArgs());

        $content = $this->messageLang($message->getMessage(), $trans);
        $this->appendMessage($content, $message->getDuration(), $message->getClassify(), $message->getType());

        // redirect url
        $url = $message->getRoute();
        if ($code = $message->getCode()) {
            $url = $this->responseUrlMap($code) ?? $url;
        }

        return $this->redirect($this->redirectUrl($url, $params));
    }

    /**
     * Response message with ajax (just latest message)
     *
     * @param Message $message
     *
     * @return Response
     * @throws
     */
    public function responseMessageWithAjax(Message $message): Response
    {
        [$params, $trans] = $this->resolveArgs($message->getArgs());

        $content = $message->getMessage();
        $content = $content ? $this->messageLang($content, $trans) : null;
        $url = $message->getRoute();
        $data = $message->getSets();

        if (isset($url)) {

            if ($content) {
                $this->appendMessage($content, $message->getDuration(), $message->getClassify(), $message->getType());
                $content = null;
            }

            $data = array_merge(
                $data,
                [
                    'href' => $this->redirectUrl($url ?: null, $params),
                ]
            );

            if ($click = $message->getClick()) {
                $data = array_merge(
                    $data,
                    [
                        'function' => $click,
                        'location' => $data['href'],
                    ],
                    $params
                );
            }
        }

        $code = $message->getCode();
        [$code4http, $code4logic] = [Response::HTTP_OK, $code, null, null];

        // instance of Error
        if ($code instanceof Error) {
            [$code4http, $code4logic] = $code->all();
        }

        return $this->responseAjax(
            $code4logic,
            $code4http,
            $content,
            $data,
            $message->getClassify(),
            $message->getType(),
            $message->getDuration()
        );
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
    final protected function valid(int $type = Abs::VW_LOGIN_AS, bool $showAllError = false)
    {
        $this->iNeedCost(Abs::BEGIN_VALID);

        /**
         * ajax
         */

        if (Helper::bitFlagAssert($type, Abs::V_AJAX) && !$this->ajax) {
            $this->iNeedCost(Abs::END_VALID);

            return $this->responseError(new ErrorAjaxRequest());
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

            return $this->responseError(new $errorCls, $message);
        }

        foreach ($validator as $field => $item) {
            $result = call_user_func_array([$this, $item['validator']], [$item['value'], $args]);
            if ($result instanceof Response) {
                $this->iNeedCost(Abs::END_VALID);

                return $result;
            }
        }

        /**
         * should auth
         */

        if (Helper::bitFlagAssert($type, Abs::V_SHOULD_AUTH)) {

            $isAuth = $this->webShouldAuth($args);

            /**
             * auth failed
             */

            if ($isAuth instanceof Error) {

                if (Helper::bitFlagAssert($type, Abs::V_MUST_AUTH)) {

                    $this->logger->warning($this->messageLang($isAuth->description()));
                    $this->iNeedCost(Abs::END_VALID);

                    return $this->responseError($isAuth);
                }

            } elseif ($isAuth instanceof Response) {

                return $isAuth;

            } else {

                $this->usr = (object)$isAuth;

                /**
                 * strict authorization
                 */

                if ($this->usrStrict && Helper::bitFlagAssert($type, Abs::V_STRICT_AUTH)) {
                    $strictHanding = $this->dispatchMethod(Abs::FN_STRICT_AUTH);

                    if ($strictHanding !== true) {

                        $error = ($strictHanding instanceof Error) ? $strictHanding : new ErrorSession();
                        $this->logger->warning($this->messageLang($error->description()));
                        $this->iNeedCost(Abs::END_VALID);

                        return $this->responseError($error);
                    }
                }

                $route = $this->route;
                if ($this->getArgs(Abs::TAG_SCENE) === Abs::TAG_EXPORT) {
                    $route .= Abs::FLAG_ROUTE_EXPORT;
                }

                /**
                 * access
                 */

                $this->access = $this->accessBuilder($this->usr);
                $access = $this->routeIsAccess([$route]);

                // access denied
                if (Helper::bitFlagAssert($type, Abs::V_ACCESS) && $access !== true) {

                    $error = new ErrorAccess();
                    $this->logger->warning($this->messageLang($error->description()));
                    $this->iNeedCost(Abs::END_VALID);

                    return $this->responseError($error);
                }
            }
        }

        $this->iNeedCost(Abs::END_VALID);
        $this->dispatchMethod(Abs::FN_BEFORE_LOGIC);

        return (object)$args;
    }

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error|Response
     */
    abstract protected function webShouldAuth(array $args);

    /**
     * Get route of all
     *
     * @param bool $value
     *
     * @return array
     */
    public function getRouteOfAll(bool $value = true): array
    {
        $routes = array_column($this->getRouteCollection(), 'route');
        $routes = Helper::arrayValuesSetTo($routes, $value, true);

        return $routes;
    }

    /**
     * Get access of all
     *
     * @param bool  $keyByClass
     * @param array $menuAssist
     *
     * @return array
     */
    public function getAccessOfAll(bool $keyByClass = false, ?array $menuAssist = null): array
    {
        $accessList = [];
        $route = $this->getRouteCollection(true);

        foreach ($route as $class => $item) {
            [$classify, $access] = $this->getAccessControlAnnotation($class);
            $classify = Helper::stringToLabel($classify);
            foreach ($access as $method => &$target) {
                if (!isset($item[$method])) {
                    continue;
                }

                $route = $item[$method]['route'];
                $target = Helper::objectToArray($target);
                $target['info'] = $item[$method]['desc_fn'];

                if ($keyByClass) {
                    if ($target['export']) {
                        $accessList[$classify][$route . Abs::FLAG_ROUTE_EXPORT] = $target;
                        $target['export'] = false;
                    }
                    $accessList[$classify][$route] = $target;
                } else {
                    $target['classify'] = $classify;
                    if ($target['export']) {
                        $accessList[$route . Abs::FLAG_ROUTE_EXPORT] = $target;
                        $target['export'] = false;
                    }
                    $accessList[$route] = $target;
                }
            }
        }

        if (!$keyByClass) {
            return $accessList;
        }

        $accessListHandling = [];
        $masterMenuDetail = $menuAssist['masterMenuDetailForRender'] ?? [];
        $slaveMenuDetail = $menuAssist['slaveMenuDetailForRender'] ?? [];
        $allMenuDetail = array_merge($masterMenuDetail, $slaveMenuDetail);

        foreach ($accessList as $classInfo => $items) {
            $id = md5($classInfo);
            foreach ($items as $route => $item) {
                if (!isset($accessListHandling[$id])) {
                    $accessListHandling[$id] = [
                        'info'  => $this->twigLang($classInfo),
                        'items' => [],
                    ];
                }
                if ($item['export']) {
                    $item['info'] .= ' export';
                }
                $item['info'] = $allMenuDetail[$route]['info'] ?? $this->twigLang($item['info']);
                $accessListHandling[$id]['items'][$route] = $item;
            }
        }

        return array_values($accessListHandling);
    }

    /**
     * Get args for scaffold view
     *
     * @param array $extra
     * @param bool  $forView
     *
     * @return array
     */
    public function displayArgsScaffold(array $extra = [], bool $forView = false): array
    {
        $configure = $this->parameters('configure');
        [$cls, $fn] = $this->getMCM('-');
        $getArgs = $this->getArgs();

        $scaffold = [
            'cnf'        => $this->cnf,
            'logic'      => $this->logic,
            'usr'        => $this->usr,
            'env'        => $this->env,
            'debug'      => $this->debug,
            'route'      => $this->route,
            'lang'       => $this->header->lang,
            'get'        => $getArgs,
            'url'        => $this->urlSafe($this->route, $getArgs, 'Scaffold', true),
            'ctrl'       => $this->controller,
            'cls'        => $cls,
            'fn'         => $fn,
            'access'     => $this->access,
            'ajax'       => $this->ajax,
            'mobile'     => $this->mobile,
            'iframe'     => $this->iframe,
            'abs'        => static::$abs,
            'enum'       => static::$enum,
            'uuid'       => $this->uuid,
            'configure'  => $configure ? Helper::jsonStringify($configure) : null,
            'app'        => $this->appType,
            'api'        => [],
            'version'    => $this->debug ? mt_rand() : $this->parameter('version'),
            'expr'       => $this->expr,
            'translator' => $this->translator,
            'logger'     => $this->logger,
        ];

        if ($this->appType == Abs::APP_TYPE_BACKEND) {
            $scaffold['api'] = [
                'login'         => $this->url($this->cnf->route_login_handler),
                'upload'        => $this->url($this->cnf->route_upload),
                'export'        => $this->url($this->cnf->route_export),
                'language'      => $this->url($this->cnf->route_language),
                'third-message' => $this->url($this->cnf->route_third_message),
            ];
        }

        if ($forView) {
            $scaffold = array_merge(
                $scaffold,
                [
                    'cnf' => Helper::keyUnderToCamel((array)$scaffold['cnf']),
                ]
            );
        }

        return array_merge($scaffold, $extra);
    }

    /**
     * Render module
     *
     * @param array       $moduleList
     * @param string|null $view
     * @param array       $routeArgs
     * @param bool        $responseWhenMessage
     * @param bool        $simpleMode
     *
     * @return Response|Message|array
     * @throws
     */
    protected function showModule(
        array $moduleList,
        ?string $view,
        array $routeArgs = [],
        bool $responseWhenMessage = true,
        bool $simpleMode = false
    ) {
        if (empty($routeArgs['scene'])) {
            $routeArgs['scene'] = Abs::TAG_UNKNOWN;
        }

        foreach ($moduleList as $module => $extraArgs) {
            if (!is_array($extraArgs)) {
                throw new ModuleException('The extra args must be array for ' . $module);
            }
            if (!isset($extraArgs['sort']) || !is_numeric($extraArgs['sort'])) {
                throw new ModuleException('The extra args must include `sort` and be integer type for ' . $module);
            }
        }

        $dispatcher = new BswModule\Dispatcher($this);
        $moduleList = Helper::sortArray($moduleList, 'sort');

        $acmeArgs = $this->displayArgsScaffold();
        $globalArgs = Helper::dig($acmeArgs, 'logic');
        $globalArgs = Helper::merge($this->parameters('module_input_args') ?? [], $globalArgs);
        $logicArgs = ['logic' => Helper::merge($globalArgs, $routeArgs)];

        $logicArgsAjax = [];
        $beforeOutput = [];
        $logic = &$logicArgs['logic'];

        foreach ($moduleList as $module => $extraArgs) {

            $extraArgs = array_merge((array)($globalArgs[$module] ?? []), $extraArgs);
            [$name, $twig, $input, $output] = $dispatcher->execute(
                $module,
                $globalArgs,
                $acmeArgs,
                $routeArgs,
                $extraArgs,
                $beforeOutput
            );

            $beforeOutput = array_merge($beforeOutput, $output);
            $acmeArgs['moduleArgs'][$name] = compact('input', 'output');

            /**
             * @var Message $message
             */
            if ($message = $output['message'] ?? null) {
                $messageHandler = Helper::dig($logic, 'messageHandler');
                if (is_callable($messageHandler)) {
                    $message = $messageHandler($message);
                    Helper::callReturnType($message, Message::class, 'Message handler');
                }

                return $responseWhenMessage ? $this->messageToResponse($message) : $message;
            }

            if (!$name || $simpleMode) {
                continue;
            }

            /**
             * twig args
             */
            $logicArgs[$name] = $output;
            $logicArgsAjax[$name] = $output;
            if (property_exists($this, 'bsw')) {
                $this->bsw[$name] = $output;
            }

            if (!$twig) {
                continue;
            }

            /**
             * twig html
             */
            $html = $this->renderPart($twig, array_merge($logicArgs, [$name => $output]));

            $name = str_replace('-', '_', $name);
            $name = Helper::underToCamel("{$name}_html");
            $logicArgs[$name] = $html;
            $logicArgsAjax[$name] = $html;
        }

        if ($simpleMode) {
            throw new Exception('Latest module should return `Message instance` when simple mode');
        }

        /**
         * After module handler
         */
        $afterModule = Helper::dig($logic, 'afterModule') ?? [];
        Helper::callReturnType($afterModule, Abs::T_ARRAY, 'Handler after module');

        foreach ($afterModule as $key => $handler) {
            if (is_callable($handler)) {
                $logic[$key] = call_user_func_array($handler, [$logic, $logicArgs]);
            }
        }

        if (!$this->ajax) {
            return $this->show($logicArgs, $view);
        }

        $content = $this->show($logicArgs, $view);
        $logicArgsAjax = array_merge($logicArgs, $logicArgsAjax, ['content' => $content]);

        return $this->okayAjax($logicArgsAjax);
    }

    /**
     * Twig path
     *
     * @param string $twig
     * @param bool   $bswForce
     *
     * @return string
     */
    protected function twigPath(string $twig, bool $bswForce = false): string
    {
        if ($bswForce) {
            $twig = '@' . Abs::BSW . '/' . $twig;
        }

        if (!Helper::strEndWith($twig, Abs::TPL_SUFFIX)) {
            $twig .= Abs::TPL_SUFFIX;
        }

        return $twig;
    }

    /**
     * Render blank
     *
     * @param array       $args
     * @param array       $moduleList
     * @param string|null $view
     *
     * @return Response|array
     * @throws
     */
    protected function showPage(array $args = [], array $moduleList = [], ?string $view = null): Response
    {
        $moduleList = Helper::merge(
            [
                BswModule\Modal\Module::class  => ['sort' => Abs::MODULE_MODAL_SORT],
                BswModule\Drawer\Module::class => ['sort' => Abs::MODULE_DRAWER_SORT],
                BswModule\Result\Module::class => ['sort' => Abs::MODULE_RESULT_SORT],
            ],
            $moduleList,
        );

        return $this->showModule($moduleList, $view, $args);
    }

    /**
     * View string handler
     *
     * @param array       $scaffold
     * @param string|null $view
     *
     * @return string
     */
    public function viewHandler(array $scaffold, ?string $view = null): string
    {
        $suffix = Abs::TPL_SUFFIX;

        if (!$view) {
            $suffix = Abs::HTML_SUFFIX . $suffix;
            // view handler
            if (method_exists($this, $fn = Abs::FN_BLANK_VIEW)) {
                $view = $this->{$fn}($suffix);
            } else {
                $view = "{$scaffold['cls']}/{$scaffold['fn']}{$suffix}";
            }
        } elseif (Helper::strEndWith($view, '#')) {
            // just it
            $view = rtrim($view, '#');
        } elseif (strpos($view, $suffix) === false) {
            // append suffix
            $view .= $suffix;
        }

        return $view;
    }

    /**
     * Render template
     *
     * @param array  $args
     * @param string $view
     *
     * @return Response|string
     */
    public function show(array $args = [], string $view = null)
    {
        $scaffold = $this->displayArgsScaffold(
            [
                'seo'     => $this->seo(),
                'src'     => $this->source(),
                'message' => $this->latestFlash(Abs::TAG_MESSAGE),
                'modal'   => $this->latestFlash(Abs::TAG_MODAL),
                'result'  => $this->latestFlash(Abs::TAG_RESULT),
            ],
            true
        );

        $view = $this->viewHandler($scaffold, $view);

        // arguments
        $params = array_merge($args, ['scaffold' => $scaffold]);

        // params before display
        $params = $this->dispatchMethod(Abs::FN_BEFORE_RENDER, $params, [$params]);

        // for debug args
        $this->breakpointDebug(Abs::BK_RENDER_ARGS, $view, $params);
        $this->logger->debug("-->> end: $this->route");

        $this->iNeedCost(Abs::END_REQUEST);
        $this->iNeedLogger(Abs::END_REQUEST);

        if ($this->ajax) {
            return $this->renderView($view, $params);
        }

        return $this->render($view, $params, $this->response);
    }

    /**
     * Get render template
     *
     * @param string $view
     * @param array  $parameters
     *
     * @return string
     */
    public function renderPart(string $view, array $parameters = []): string
    {
        $parameters['scaffold'] = $this->displayArgsScaffold([], true);
        $view = $this->viewHandler($parameters['scaffold'], $view);

        return $this->renderView($view, $parameters);
    }

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
        if (!$this->ajax) {
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

        return $this->responseAjax(
            ErrorException::CODE,
            $code4http,
            $message,
            [],
            Abs::TAG_CLASSIFY_ERROR,
            Abs::TAG_TYPE_CONFIRM,
            0
        );
    }
}
