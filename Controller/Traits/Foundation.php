<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Annotation\Entity\Input;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Component\MysqlDoc;
use Leon\BswBundle\Component\Pinyin;
use Leon\BswBundle\Component\Reflection;
use Leon\BswBundle\Entity\BswConfig;
use Leon\BswBundle\Entity\BswToken;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Entity\Enum;
use Leon\BswBundle\Module\Error\Entity\ErrorDevice;
use Leon\BswBundle\Module\Error\Entity\ErrorException;
use Leon\BswBundle\Module\Error\Entity\ErrorOAuthExpiredToken;
use Leon\BswBundle\Module\Error\Entity\ErrorOAuthInvalidToken;
use Leon\BswBundle\Module\Error\Entity\ErrorOAuthMalformedToken;
use Leon\BswBundle\Module\Error\Entity\ErrorOAuthNotFoundToken;
use Leon\BswBundle\Module\Error\Entity\ErrorOS;
use Leon\BswBundle\Module\Error\Entity\ErrorUA;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Exception\FileNotExistsException;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Filter\Filter;
use Leon\BswBundle\Module\Form\Form;
use Leon\BswBundle\Module\Traits as MT;
use Leon\BswBundle\Module\Hook\Dispatcher as HookerDispatcher;
use Leon\BswBundle\Module\Validator\Dispatcher as ValidatorDispatcher;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Controller\Traits as CT;
use Leon\BswBundle\Repository\BswTokenRepository;
use Leon\BswBundle\Repository\FoundationRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application as CmdApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request as SfRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Route as RoutingRoute;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\Query\Expr;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use Predis\Client;
use ReflectionClass;
use Exception;

/**
 * @property AbstractController $container
 */
trait Foundation
{
    use MT\Init,
        MT\Magic,
        MT\Message,
        MT\Variable;

    use CT\Annotation,
        CT\ApiDocument,
        CT\Breakpoint,
        CT\Database,
        CT\DisCache,
        CT\EnterpriseWx,
        CT\Excel,
        CT\FormRules,
        CT\IpRegion,
        CT\Request,
        CT\Sns,
        CT\Telegram,
        CT\Third,
        CT\Upload;

    //
    // ↓↓ For component ↓↓
    //

    /**
     * @var Session|SessionInterface
     */
    protected $session;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Expr
     */
    protected $expr;

    /**
     * @var Response
     */
    protected $response;

    //
    // ↓↓ For variable ↓↓
    //

    /**
     * @var string
     */
    protected $env;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var array
     */
    protected $cnf;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    public static $abs = Abs::class;

    /**
     * @var string
     */
    public static $enum = Enum::class;

    //
    // ↓↓ For logic ↓↓
    //

    /**
     * @var object
     */
    protected $usr;

    /**
     * @var bool
     */
    protected $usrStrict = true;

    /**
     * @var bool
     */
    protected $validatorUseLabel = true;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var object
     */
    protected $header;

    /**
     * @var array
     */
    protected $headerMap = [
        'time',
        'sign',
        'lang',
        'token',
        'sign-dynamic',
        'sign-close',
        'sign-debug',
        'postman-token' => 'postman',
    ];

    /**
     * Foundation constructor.
     *
     * @param ContainerInterface  $container
     * @param SessionInterface    $session
     * @param TranslatorInterface $translator
     * @param AdapterInterface    $cache
     * @param LoggerInterface     $logger
     */
    public function __construct(
        ContainerInterface $container,
        SessionInterface $session,
        TranslatorInterface $translator,
        AdapterInterface $cache,
        LoggerInterface $logger
    ) {
        if (!$this->container) {
            $this->container = $container;
        }

        $this->session = $session;
        $this->session->start();

        $this->beforeInit();

        $this->kernel = $this->container->get('kernel');
        $this->translator = $translator;
        $this->redis = $this->container->get('snc_redis.default');
        $this->cache = $cache;
        $this->logger = $logger;
        $this->expr = new Expr();
        $this->response = new Response();

        $this->env = $this->kernel->getEnvironment();
        $this->debug = $this->kernel->isDebug();

        $config = $this->parameters('cnf', false);
        $config = $this->dispatchMethod(Abs::FN_EXTRA_CONFIG, $config, [$config]);
        $this->cnf = (object)$config;

        $this->iNeedCost(Abs::BEGIN_CONSTRUCT);

        $this->route = $this->request()->get('_route');
        $this->controller = $this->request()->get('_controller');
        $this->uuid = $this->getArgs('uuid') ?? ('_' . Helper::generateToken(16, 36));

        $args = $this->headArgs();
        $args = Helper::arrayPull($args, $this->headerMap, false, '');
        $this->header = (object)$args;
        $this->header->lang = $this->request()->getLocale();

        $this->iNeedCost(Abs::END_CONSTRUCT);
        $this->iNeedCost(Abs::BEGIN_INIT);

        $this->logger->debug("-->> begin: $this->route");
        $this->init();

        $this->iNeedCost(Abs::END_INIT);
        $this->iNeedCost(Abs::BEGIN_REQUEST);
    }

    /**
     * Get user field
     *
     * @param string $key
     *
     * @return mixed
     */
    public function usr(string $key)
    {
        return $this->usr->{$this->cnf->{$key}} ?? null;
    }

    /**
     * Logger process
     *
     * @param string $scene
     */
    public function iNeedLogger(string $scene)
    {
        /**
         * development environment
         */

        $message = "{$scene} with route {$this->route}";
        if ($this->debug) {
            $this->logWarning($message);

            return;
        }

        /**
         * production environment and no debug uuid
         */

        $uuid = $this->cnf->debug_uuid ?? time();

        /**
         * production environment and debug uuid
         */

        $userId = ($this->usr('usr_uid') === $uuid);
        $deviceId = (($this->header->device ?? null) === $uuid);

        if ($userId || $deviceId) {
            $this->logError("{$message} for (UUID: {$uuid})");
        } else {
            $this->logWarning($message);
        }
    }

    /**
     * Logger cost
     *
     * @param string $scene
     */
    public function iNeedCost(string $scene)
    {
        if (!$this->cnf->debug_cost) {
            return;
        }

        [$logger, $cost] = Helper::cost($scene);

        // logger
        $this->logger->debug($logger);

        // logger latest
        if ($scene != Abs::END_REQUEST) {
            return;
        }

        $date = date(Abs::FMT_DAY_SIMPLE);
        $key = "request_cost:{$date}";

        $this->logger->debug("-->> total cost {$cost} in request {$this->route}");

        if (!$this->redis) {
            return;
        }

        $exists = $this->redis->exists($key);

        // times
        $timesKey = "{$this->route}_times";
        $times = $this->redis->hget($key, $timesKey) ?? 0;
        $this->redis->hincrby($key, $timesKey, 1);

        // cost
        $costKey = "{$this->route}_cost";
        $avgCost = $this->redis->hget($key, $costKey);
        $avgCost = intval(($times * $avgCost + $cost) / ++$times);

        $this->redis->hset($key, $costKey, $avgCost);
        if (!$exists) {
            $this->redis->expire($key, Abs::TIME_DAY * 2);
        }
    }

    /**
     * Caching
     *
     * @param callable $callback
     * @param string   $key
     * @param int      $time
     * @param bool     $useCache
     *
     * @return mixed
     * @throws
     */
    public function caching(callable $callback, string $key = null, int $time = null, $useCache = null)
    {
        $rebuilding = function () use ($callback) {
            return call_user_func_array($callback, [$this]);
        };

        if (!($useCache ?? ($this->cnf->cache_enabled ?? false))) {
            return $rebuilding();
        }

        if (empty($key)) {
            $caller = Helper::backtrace(1);
            $key = "{$caller['class']}::{$caller['function']}(" . Helper::jsonStringify($caller['args'] ?? []) . ")";
        }

        // $this->logger->debug("Using cache, ({$key})");
        $target = $this->cache->getItem(md5($key));
        $time = $time ?? intval($this->cnf->cache_default_expires ?? 3600);
        if ($time > 0) {
            $target->expiresAfter($time);
        }

        if (!$target->isHit()) {
            // $this->logger->warning("Cache misses so rebuilding now, ({$key})");
            $this->cache->save($target->set($rebuilding()));
        }

        return $target->get();
    }

    /**
     * Get params
     *
     * @param string $name
     * @param mixed  $default
     * @param bool   $inController
     *
     * @return mixed
     */
    public function parameter(string $name, $default = null, bool $inController = true)
    {
        static $config = [];

        if (isset($config[$name])) {
            return $config[$name];
        }

        try {
            return $config[$name] = ($inController ? $this : $this->container)->getParameter($name);
        } catch (Exception $e) {
            return $default;
        }
    }

    /**
     * Get parameter in order
     *
     * @param array  $names
     * @param bool   $inController
     * @param string $assert
     *
     * @return mixed
     */
    public function parameterInOrder(array $names, bool $inController, string $assert)
    {
        foreach ($names as $name) {
            $value = $this->parameter($name, null, $inController);
            switch ($assert) {
                case Abs::ASSERT_EMPTY:
                    $assert = empty($value);
                    break;
                case Abs::ASSERT_ISSET:
                    $assert = is_null($value);
                    break;
            }

            if ($assert !== true) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Get parameter in order by empty
     *
     * @param array $names
     * @param bool  $inController
     *
     * @return mixed
     */
    public function parameterInOrderByEmpty(array $names, bool $inController = true)
    {
        return $this->parameterInOrder($names, $inController, Abs::ASSERT_EMPTY);
    }

    /**
     * Get parameter in order by isnull
     *
     * @param array $names
     * @param bool  $inController
     *
     * @return mixed
     */
    public function parameterInOrderByIsset(array $names, bool $inController = true)
    {
        return $this->parameterInOrder($names, $inController, Abs::ASSERT_ISSET);
    }

    /**
     * Get params merge bsw bundle
     *
     * @param string $name
     * @param bool   $inController
     * @param string $bundle
     *
     * @return mixed
     * @throws
     */
    public function parameters(string $name, bool $inController = true, string $bundle = 'bsw')
    {
        $params = $this->parameter($name, null, $inController);
        if (is_scalar($params)) {
            return $params;
        }

        $skinParams = [];
        $skin = $this->parameter('skin', null, $inController);
        if ($skin) {
            $skinParams = $this->parameter("{$skin}_{$name}", [], $inController);
        }

        // null or array
        $bundleParams = [];
        if ($bundle) {
            $bundleParams = $this->parameter("{$bundle}_{$name}", [], $inController);
        }

        return Helper::merge($bundleParams, (array)$params, $skinParams);
    }

    /**
     * Validator
     *
     * @param string       $field
     * @param mixed        $value
     * @param array|string $rules
     * @param array        $option
     *
     * @return mixed|false
     * @throws
     */
    public function validator(string $field, $value, $rules, array $option = [])
    {
        $original = $this->annotation(Input::class, true);
        $option = array_merge($option, ['field' => $field, 'rules' => $rules]);
        $items = $original->converter([new Input($option)]);

        [$error, $args] = $this->parametersValidator(current($items), [$field => $value]);
        if (empty($error)) {
            return $args[$field];
        }

        $this->push(current(current($error)), Abs::TAG_VALIDATOR);

        return false;
    }

    /**
     * Parameters validator
     *
     * @param array $items
     * @param array $values
     *
     * @return array
     * @throws
     */
    public function parametersValidator(array $items, array $values = null): array
    {
        $errorList = $argsList = $signList = $validatorList = [];
        $valuesClean = $values ? Html::cleanArrayHtml($values) : [];

        $extraArgs = $this->dispatchMethod(Abs::FN_VALIDATOR_ARGS, []);
        $dispatcher = new ValidatorDispatcher($this->translator, $this->header->lang);

        foreach ($items as $item) {

            if (isset($values)) {
                $target = $item->html ? $values : $valuesClean;
                $value = $target[$item->field] ?? null;
            } else {
                $value = $this->args($item->method ?: Abs::REQ_ALL, $item->field, !$item->html);
            }

            if ($item->sign == Abs::AUTO) {
                $item->sign = is_null($value) ? false : true;
            }

            if ($this->validatorUseLabel) {
                $label = Helper::stringToLabel($item->label ?? $item->field);
                $label = $item->trans ? $this->fieldLang($label) : $label;
            } else {
                $label = $item->field;
            }

            $extraArgs[Abs::RULES_FLAG_HANDLER] = $item->rulesArgsHandler;
            $result = $dispatcher->execute($item->field, $item->rules, $value, $extraArgs, $item->sign, $label);

            if (!empty($result->error)) {
                $classHandling = $item->error;
                if (!isset($errorList[$classHandling])) {
                    $errorList[$classHandling] = [];
                }
                $errorList[$classHandling] = array_merge($errorList[$classHandling], $result->error);
            }

            if ($result->args !== false) {
                $argsList[$item->field] = $result->args;
                $extraArgs[$item->field] = $result->args;
            }

            if ($result->sign !== false) {
                $signList[$item->field] = $result->sign;
            }

            if (
                isset($argsList[$item->field]) &&
                ($item->validator && method_exists($this, $item->validator)) &&
                !(isset($item->rules[Abs::VALIDATION_IF_SET]) && empty($value))
            ) {
                $validatorList[$item->field] = [
                    'value'     => $argsList[$item->field],
                    'validator' => $item->validator,
                ];
            }
        }

        return [$errorList, $argsList, $signList, $validatorList];
    }

    /**
     * Get current request
     *
     * @return SfRequest
     */
    public function request(): SfRequest
    {
        return $this->container->get('request_stack')->getCurrentRequest() ?: new SfRequest();
    }

    /**
     * @param int       $indexInForwarded
     * @param SfRequest $request
     *
     * @return string
     */
    public function getClientIp(int $indexInForwarded = null, SfRequest $request = null): ?string
    {
        if (!$request) {
            $request = $this->request();
        }

        $default = $request->getClientIp();
        $forwarded = $request->server->get('HTTP_X_FORWARDED_FOR');
        $forwarded = empty($forwarded) ? [] : Helper::stringToArray($forwarded, true, true, 'trim');

        if (count($forwarded) <= 1) {
            return $request->server->get('HTTP_X_REAL_IP', $default);
        }

        if (is_null($indexInForwarded)) {
            $indexInForwarded = $this->cnf->ip_index_in_forwarded ?? -2;
        }

        if ($indexInForwarded < 0) {
            $indexInForwarded = count($forwarded) + $indexInForwarded;
        }

        return empty($forwarded[$indexInForwarded]) ? $default : $forwarded[$indexInForwarded];
    }

    /**
     * Get host
     *
     * @param string $host
     * @param bool   $schemeNeed
     * @param bool   $autoPortNeed
     *
     * @return string
     */
    public function host(string $host = null, bool $schemeNeed = true, bool $autoPortNeed = true): string
    {
        $request = $this->request();

        if (empty($host) && $this->cnf->proxy_pass) {
            $host = $this->cnf->proxy_pass;
            $schemeNeed = true;
            $autoPortNeed = false;
        }

        if (empty($host)) {
            if (empty($this->cnf->host)) {
                $host = $request->getHost();
            } else {
                $host = $this->cnf->host;
            }
        }

        if (!$schemeNeed) {
            $host = str_replace(['http://', 'https://'], null, $host);
            $host = '//' . ltrim($host, '/');
        } else {
            if (strpos($host, 'http') !== 0) {
                $scheme = "{$request->getScheme()}://";
                $host = ltrim($host, '/');
                $host = "{$scheme}{$host}";
            }
        }

        $port = null;
        if ($autoPortNeed && !parse_url($host, PHP_URL_PORT)) {
            $port = $request->getPort();
            $port = (in_array($port, [null, 80, 443]) ? null : ":{$port}");
        }

        return "{$host}{$port}";
    }

    /**
     * Get current url
     *
     * @return string
     */
    public function currentUrl(): string
    {
        return $this->host() . $this->request()->getRequestUri();
    }

    /**
     * Get url
     *
     * @param string $route
     * @param array  $params
     * @param bool   $abs
     *
     * @return string
     */
    public function url(string $route, array $params = [], bool $abs = true): string
    {
        $referenceType = $abs ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH;
        $url = $this->generateUrl($route, $params, $referenceType);

        if ($abs && $this->cnf->proxy_pass) {
            $items = explode('/', $url);
            $errorHost = "{$items[0]}//{$items[2]}";
            $url = str_replace($errorHost, $this->host(), $url);
        }

        return $url;
    }

    /**
     * Get url by safe mode
     *
     * @param string|null $route
     * @param array       $params
     * @param string|null $scene
     * @param bool        $abs
     *
     * @return string|null
     */
    public function urlSafe(string $route, array $params = [], ?string $scene = null, bool $abs = false): ?string
    {
        try {
            $url = $this->url($route, $params, $abs);
        } catch (Exception $e) {
            $url = null;
            $scene = $scene ? "[{$scene}] " : null;
            $this->logger->warning("{$scene}Create url error: {$e->getMessage()}");
        }

        return $url;
    }

    /**
     * Perfect url
     *
     * @param string $url
     *
     * @return string
     */
    public function perfectUrl(string $url)
    {
        if (empty($url)) {
            return $this->host();
        }

        $url = trim($url, '/');
        if (!Helper::isUrlAlready($url)) {
            $request = $this->request();
            $url = "{$request->getScheme()}://{$url}";
        }

        return "{$url}/";
    }

    /**
     * Get page reference (pre page)
     *
     * @return string
     */
    public function reference()
    {
        $reference = $this->request()->server->get('HTTP_REFERER');

        if (!$reference || strpos($reference, $this->host()) === false) {
            return $this->url($this->cnf->route_default);
        }

        return $reference;
    }

    /**
     * Get app name tag (just en)
     *
     * @param string $split
     * @param bool   $lower
     *
     * @return string
     */
    public function app(?string $split = '_', bool $lower = true): string
    {
        $app = $this->cnf->app_name;
        if (Helper::utf8Chinese($app)) {
            $app = ucwords(Pinyin::getPinyin($app), ' ');
        }
        if ($lower) {
            $app = strtolower($app);
        }

        return str_replace(' ', $split, $app);
    }

    /**
     * Get origin Module, Class and Method
     *
     * @return array
     */
    public function getOriginMCM(): array
    {
        [$class, $method] = explode('::', $this->controller);
        [$class, $module] = array_reverse(explode('\\', $class));

        return [$module, $class, $method];
    }

    /**
     * Get Module, Class and Method
     *
     * @param string $split
     *
     * @return array
     */
    public function getMCM(string $split = null): array
    {
        [$module, $class, $method] = $this->getOriginMCM();

        // handler
        $method = Helper::camelToUnder($method, $split);
        $class = Helper::camelToUnder($class, $split);
        $module = Helper::camelToUnder($module, $split);

        // controller
        $controller = ($module == 'controller') ? $class : $module;
        $controller = str_replace("{$split}controller", null, $controller);

        // remove tag
        $find = ["get{$split}", "post{$split}", "delete{$split}", "{$split}action"];
        $method = str_replace($find, null, $method);
        $class = str_replace("{$split}controller", null, $class);

        return [$controller, $method, $class, $module];
    }

    /**
     * Get route collection
     *
     * @param bool $keyByClass
     *
     * @return array
     * @throws
     */
    public function getRouteCollection(bool $keyByClass = false)
    {
        return $this->caching(
            function () use ($keyByClass) {

                /**
                 * @var Router $route
                 */
                $route = $this->container->get('router');

                $routeArr = [];
                $ref = new Reflection();

                /**
                 * Get docs
                 *
                 * @param string $class
                 * @param string $method
                 *
                 * @return array
                 */
                $getDoc = function (string $class, string $method) use ($ref) {

                    static $clsDocs = [];
                    static $fnDocs = [];

                    if (!isset($clsDocs[$class])) {
                        $clsDocs[$class] = $ref->getClsDoc($class);
                    }

                    $fn = "{$class}::{$method}";
                    if (!isset($fnDocs[$method])) {
                        $fnDocs[$fn] = $ref->getFnDoc($class, $method);
                    }

                    return [
                        'desc_cls'         => $clsDocs[$class]['info'] ?? null,
                        'desc_fn'          => $fnDocs[$fn]['info'] ?? null,
                        'license'          => $fnDocs[$fn]['license'] ?? [],
                        'license-request'  => $fnDocs[$fn]['license-request'] ?? [],
                        'license-response' => $fnDocs[$fn]['license-response'] ?? [],
                        'property'         => $fnDocs[$fn]['property'] ?? [],
                        'param'            => $fnDocs[$fn]['param'] ?? [],
                        'variable'         => $fnDocs[$fn]['var'] ?? [],
                        'tutorial'         => $fnDocs[$fn]['tutorial'] ?? null,
                    ];
                };

                foreach ($route->getRouteCollection() as $key => $item) {

                    if (strpos($key, '_') === 0) {
                        continue;
                    }

                    /**
                     * @var RoutingRoute $item
                     */
                    $controller = $item->getDefault('_controller');
                    [$class, $method] = explode('::', $controller);

                    $itemHandling = array_merge(
                        [
                            'route'    => $key,
                            'uri'      => $item->getPath(),
                            'http'     => $item->getMethods(),
                            'app'      => Helper::cutString($class, ['\\^-2']),
                            'class'    => $class,
                            'method'   => $method,
                            'path'     => "{$class}::{$method}",
                            'instance' => $item,
                        ],
                        $getDoc($class, $method)
                    );

                    if ($keyByClass) {
                        $routeArr[$class][$method] = $itemHandling;
                    } else {
                        $routeArr[$controller] = $itemHandling;
                    }
                }

                return $routeArr;
            }
        );
    }

    /**
     * Get component instance
     *
     * @param string $class
     * @param bool   $single
     *
     * @return object
     * @throws
     */
    public function component(string $class, bool $single = true)
    {
        static $instance = [];

        if (isset($instance[$class]) && $single) {
            return $instance[$class];
        }

        $component = $this->parameters('component') ?? [];
        if (!isset($component[$class])) {
            throw new InvalidArgumentException("Component config for `{$class}` is not defined");
        }

        $component = $component[$class];
        if (!class_exists($class)) {
            if (!isset($component['class'])) {
                throw new InvalidArgumentException("Component class `{$class}` is not defined");
            }
            $class = $component['class'];
        }

        if (!isset($component['arguments']) || !is_array($component['arguments'])) {
            throw new InvalidArgumentException("Component arguments not setting or not array `{$class}`");
        }

        $reflection = new ReflectionClass($class);
        $parameters = $reflection->getConstructor()->getParameters();
        $arguments = $component['arguments'];

        if (Helper::typeofArray($arguments, Abs::T_ARRAY_INDEX)) {
            return $instance[$class] = $reflection->newInstanceArgs($arguments);
        }

        $args = [];
        foreach ($parameters as $i) {
            $args[$i->name] = $arguments[$i->name] ?? ($i->isDefaultValueAvailable() ? $i->getDefaultValue() : null);
        }

        return $instance[$class] = $reflection->newInstanceArgs($args);
    }

    /**
     * @param array        $hooks
     * @param object|array $item
     * @param bool         $persistence
     * @param callable     $before
     * @param callable     $after
     * @param array        $extraArgs
     *
     * @return object|array
     * @throws
     */
    public function hooker(
        array $hooks,
        $item,
        bool $persistence = false,
        callable $before = null,
        callable $after = null,
        array $extraArgs = []
    ) {

        if (empty($item)) {
            return $item;
        }

        $more = is_array($item) && Helper::typeofArray($item, Abs::T_ARRAY_INDEX);
        if (!$more) {
            $item = [$item];
        }

        $hooker = new HookerDispatcher();

        $extraArgsHandling = $this->dispatchMethod(Abs::FN_HOOKER_ARGS, []);
        $extraArgs = Helper::merge($extraArgsHandling, $extraArgs);

        $item = $hooker
            ->setHooks($hooks)
            ->setBeforeHandler($before)
            ->setAfterHandler($after)
            ->executeAny($item, $persistence, $extraArgs);

        return $more ? $item : current($item);
    }

    /**
     * Create filter
     *
     * @param string|Filter $filter
     * @param               $value
     *
     * @return array
     */
    public function createFilter($filter, $value)
    {
        if (!Helper::extendClass($filter, Filter::class)) {
            throw new InvalidArgumentException("Filter should extend class " . Filter::class);
        }

        return [
            'filter' => is_object($filter) ? $filter : new $filter(),
            'value'  => $value,
        ];
    }

    /**
     * Encrypt password
     *
     * @param string $password
     * @param string $salt
     *
     * @return string
     */
    public function password(string $password, string $salt = null): string
    {
        $salt = $salt ?? $this->parameter('salt');

        if ($password) {
            $password = md5(strrev($salt)) . md5($password) . md5($salt);
            $password = md5($password);
        }

        return $password;
    }

    /**
     * Latest lang
     *
     * @param array  $map
     * @param string $defaultLocal
     *
     * @return string
     */
    public function langLatest(array $map = [], string $defaultLocal = null): string
    {
        $local = Enum::LANG_TO_LOCALE[$this->header->lang] ?? 'en';

        if (isset($map[$local])) {
            return $map[$local];
        }

        if ($defaultLocal && isset($map[$defaultLocal])) {
            return $map[$defaultLocal];
        }

        return $local;
    }

    /**
     * Fields with lang
     *
     * @param array      $fields
     * @param bool       $camel
     * @param array|null $map
     *
     * @return array
     */
    public function langFields(array $fields, bool $camel = true, ?array $map = null): array
    {
        $lang = $this->langLatest($map ?? $this->langFieldMap, 'en');

        return Helper::arrayMap(
            $fields,
            function ($item) use ($lang, $camel) {
                $field = "{$lang}_{$item}";

                return $camel ? Helper::underToCamel($field) : $field;
            }
        );
    }

    /**
     * Filter for lang
     *
     * @param string $alias
     * @param bool   $zeroNeed
     * @param array  $map
     *
     * @return array
     */
    public function langFilter(string $alias, bool $zeroNeed = true, array $map = Enum::LANG): array
    {
        $index = $map[$this->header->lang] ?? 0;

        if ($zeroNeed) {
            $index = array_unique([0, $index]);
            $index = (count($index) === 1 ? current($index) : $index);
        }

        if (is_array($index)) {
            $filter = ['where' => [$this->expr->in("{$alias}.lang", $index)]];
        } else {
            $filter = [
                'where' => [$this->expr->eq("{$alias}.lang", ':lang')],
                'args'  => ['lang' => [$index]],
            ];
        }

        return $filter;
    }

    /**
     * Get location with lang
     *
     * @param string $ip
     * @param bool   $returnArray
     * @param array  $map
     *
     * @return string|array
     */
    public function locationWithLang(
        string $ip,
        bool $returnArray = false,
        array $map = ['cn' => 'CN', 'en' => 'EN']
    ) {

        $lang = $this->langLatest($map, 'en');
        $location = $this->ip2regionIPDB($ip, 'ip2region.ipip.ipdb', $lang)['location'];
        $location = array_filter(explode('|', $location));

        return $returnArray ? $location : implode(' ', $location);
    }

    /**
     * Lang for enum
     *
     * @param string|null $label
     * @param array       $args
     * @param string      $locale
     *
     * @return string|null
     */
    public function enumLangSimple(?string $label, array $args = [], string $locale = null): ?string
    {
        if (empty($label)) {
            return $label;
        }

        return $this->translator->trans($label, $args, 'enum', $locale);
    }

    /**
     * Lang the enum
     *
     * @param array  $enum
     * @param bool   $encode
     * @param array  $args
     * @param string $locale
     *
     * @return array|string
     */
    public function enumLang(array $enum, bool $encode = false, array $args = [], string $locale = null)
    {
        foreach ($enum as $key => $label) {
            if (is_array($label)) {
                $enum[$key] = $this->enumLang($label, false, $args, $locale);
            } elseif (gettype($label) == Abs::T_STRING) {
                $enum[$key] = $this->translator->trans($label, $args, 'enum', $locale);
            }
        }

        return $encode ? Helper::jsonStringify($enum) : $enum;
    }

    /**
     * Lang for field
     *
     * @param string|null $label
     * @param array       $args
     * @param string      $locale
     *
     * @return string|null
     */
    public function fieldLang(?string $label, array $args = [], string $locale = null): ?string
    {
        if (empty($label)) {
            return $label;
        }

        return $this->translator->trans($label, $args, 'fields', $locale);
    }

    /**
     * Lang for message
     *
     * @param string|null $label
     * @param array       $args
     * @param string      $locale
     *
     * @return string|null
     */
    public function messageLang(?string $label, array $args = [], string $locale = null): ?string
    {
        if (empty($label)) {
            return $label;
        }

        return $this->translator->trans($label, $args, 'messages', $locale);
    }

    /**
     * Lang for twig
     *
     * @param string|null $label
     * @param array       $args
     * @param string      $locale
     *
     * @return string|null
     */
    public function twigLang(?string $label, array $args = [], string $locale = null): ?string
    {
        if (empty($label)) {
            return $label;
        }

        return $this->translator->trans($label, $args, 'twig', $locale);
    }

    /**
     * Lang for seo
     *
     * @param string|null $label
     * @param array       $args
     * @param string      $locale
     *
     * @return string|null
     */
    public function seoLang(?string $label, array $args = [], string $locale = null): ?string
    {
        if (empty($label)) {
            return $label;
        }

        return $this->translator->trans($label, $args, 'seo', $locale);
    }

    /**
     * Get db config
     *
     * @param string $key
     *
     * @return array
     */
    public function getDbConfig(string $key): array
    {
        $args = function (string $key) {
            return $this->parameter($key, null, false);
        };

        return $this->caching(
            function () use (&$args) {

                /**
                 * @var FoundationRepository $repo
                 */
                $repo = $this->repo(BswConfig::class);

                return $repo->kvp(['value'], 'key');
            },
            $key,
            $args('config_cache_default_expires'),
            $args('config_cache_enabled')
        );
    }

    /**
     * Mysql scheme document
     *
     * @param string $table
     * @param string $doctrine
     *
     * @return array
     */
    public function mysqlSchemeDocument(string $table = null, string $doctrine = null): array
    {
        $pdo = $this->pdo($doctrine ?? Abs::DOCTRINE_DEFAULT);
        $database = $pdo->getDatabase();
        $document = (new MysqlDoc())->create($pdo, [$database]);
        $document = $document[$database] ?? [];

        return $table ? ($document[$table] ?? []) : $document;
    }

    /**
     * Manual list for pagination
     *
     * @param array $list
     * @param array $query
     *
     * @return array
     * @throws
     */
    public function manualListForPagination(array $list, array $query): array
    {
        if (!$query['paging']) {
            return $list;
        }

        $query = array_merge($query, Helper::pageArgs($query, Abs::PAGE_DEFAULT_SIZE));
        $hint = $query['hint'] ?? count($list);
        $total = intval($hint);

        $query['limit'] = $query['limit'] ?: $total;

        return [
            Abs::PG_CURRENT_PAGE => $query['page'],
            Abs::PG_PAGE_SIZE    => $query['limit'],
            Abs::PG_TOTAL_PAGE   => $query['limit'] ? ceil($total / $query['limit']) : 1,
            Abs::PG_TOTAL_ITEM   => $total,
            Abs::PG_ITEMS        => array_slice($list, $query['offset'], $query['limit']),
        ];
    }

    /**
     * Valid device args
     *
     * @param int $type
     *
     * @return true|Response
     * @throws
     */
    public function validDevice(int $type = Abs::VD_ALL)
    {
        if (Helper::bitFlagAssert($type, Abs::VD_OS) && empty($this->header->os)) {
            return $this->failed(new ErrorOS());
        }

        if (Helper::bitFlagAssert($type, Abs::VD_UA) && empty($this->header->ua)) {
            return $this->failed(new ErrorUA());
        }

        if (Helper::bitFlagAssert($type, Abs::VD_DEVICE) && empty($this->header->device)) {
            return $this->failed(new ErrorDevice());
        }

        return true;
    }

    /**
     * Get correct region by special province
     *
     * @param array $location
     * @param array $specialProvince
     *
     * @return string
     */
    public function getCorrectRegion(array $location, array $specialProvince = ['香港', '澳门', '台湾']): string
    {
        $region = $location['country'];
        if ($specialProvince && in_array($location['province'], $specialProvince)) {
            $region = $location['province'];
        }

        return $region;
    }

    /**
     * Get file path (in order)
     *
     * @param string $fileName
     * @param string $dirName
     * @param string $bundle
     *
     * @return string
     * @throws
     */
    public function getFilePathInOrder(string $fileName, string $dirName = 'mixed', string $bundle = Abs::BSW_BUNDLE)
    {
        $dirName = trim($dirName, '/');
        $path = $this->kernel->getProjectDir();
        $file = "{$path}/{$dirName}/{$fileName}";

        if (file_exists($file)) {
            return $file;
        }

        $path = $this->kernel->getBundle($bundle)->getPath();
        $file = "{$path}/Resources/{$dirName}/{$fileName}";

        if (file_exists($file)) {
            return $file;
        }

        throw new FileNotExistsException("File {$file} is not exists");
    }

    /**
     * Get file path
     *
     * @param string $fileName
     * @param string $dirName
     * @param string $bundle
     *
     * @return string
     * @throws
     */
    public function getFilePath(string $fileName, string $dirName = 'mixed', ?string $bundle = Abs::BSW_BUNDLE)
    {
        $dirName = trim($dirName, '/');

        if ($bundle) {
            $path = $this->kernel->getBundle($bundle)->getPath();
            $file = "{$path}/Resources/{$dirName}/{$fileName}";
        } else {
            $path = $this->kernel->getProjectDir();
            $file = "{$path}/{$dirName}/{$fileName}";
        }

        if (file_exists($file)) {
            return $file;
        }

        throw new FileNotExistsException("File {$file} is not exists");
    }

    /**
     * Get path (in order)
     *
     * @param string $dirName
     * @param string $bundle
     *
     * @return string
     * @throws
     */
    public function getPathInOrder(string $dirName = 'mixed', string $bundle = Abs::BSW_BUNDLE)
    {
        $dirName = trim($dirName, '/');
        $path = $this->kernel->getProjectDir();
        $dir = "{$path}/{$dirName}";

        if (is_dir($dir)) {
            return $dir;
        }

        $path = $this->kernel->getBundle($bundle)->getPath();
        $dir = "{$path}/Resources/{$dirName}";

        if (is_dir($dir)) {
            return $dir;
        }

        throw new FileNotExistsException("Directory {$dir} is not exists");
    }

    /**
     * Get path
     *
     * @param string $dirName
     * @param string $bundle
     *
     * @return string
     * @throws
     */
    public function getPath(string $dirName = 'mixed', ?string $bundle = Abs::BSW_BUNDLE)
    {
        $dirName = trim($dirName, '/');

        if ($bundle) {
            $path = $this->kernel->getBundle($bundle)->getPath();
            $dir = "{$path}/Resources/{$dirName}";
        } else {
            $path = $this->kernel->getProjectDir();
            $dir = "{$path}/{$dirName}";
        }

        if (is_dir($dir)) {
            return $dir;
        }

        throw new FileNotExistsException("Directory {$dir} is not exists");
    }

    /**
     * Attachment handler for preview
     *
     * @param object|array $item
     * @param string       $key
     * @param array        $pull
     * @param bool         $unsetPullKeys
     *
     * @return array|object
     */
    public function attachmentPreviewHandler(
        $item,
        string $key = 'file_url',
        array $pull = ['deep', 'filename'],
        bool $unsetPullKeys = true
    ) {
        if (empty($item)) {
            return $item;
        }

        $isObject = is_object($item);
        if ($isObject) {
            $item = Helper::entityToArray($item);
        }

        $args = Helper::arrayPull($item, $pull, $unsetPullKeys);
        $path = Helper::joinString('/', ...array_values($args));

        if (!empty($path) && strpos($path, 'http') !== 0) {
            $path = $this->perfectUrl($this->cnf->host_file) . $path;
        }

        $item[$key] = $path;

        return $isObject ? (object)$item : $item;
    }

    /**
     * Call an command
     *
     * @param string          $command
     * @param array           $condition
     * @param OutputInterface $output
     *
     * @return mixed
     * @throws
     */
    public function commandCaller(string $command, array $condition = [], OutputInterface $output = null)
    {
        $application = new CmdApplication($this->kernel);
        $application->setAutoExit(false);
        $condition = Helper::arrayMapKey(
            $condition,
            function ($k) {
                return strpos($k, '--') === 0 ? $k : "--{$k}";
            }
        );

        if ($output) {
            return $application->find($command)->run(new ArrayInput($condition), $output);
        }

        $output = new BufferedOutput();
        $application->find($command)->run(new ArrayInput($condition), $output);

        return $output->fetch();
    }

    /**
     * Create scene token
     *
     * @param int   $scene
     * @param int   $userId
     * @param array $params
     * @param int   $effectiveTimes
     * @param int   $time
     *
     * @return false|int
     * @throws
     */
    public function createSceneToken(
        int $scene = 1,
        int $userId = 0,
        array $params = [],
        int $effectiveTimes = 1,
        int $time = Abs::TIME_MINUTE * 3
    ) {
        /**
         * @var BswTokenRepository $tokenRepo
         */
        $tokenRepo = $this->repo(BswToken::class);

        $token = Helper::generateToken();
        $result = $tokenRepo->newly(
            [
                'userId'         => $userId,
                'scene'          => $scene,
                'token'          => $token,
                'effectiveTimes' => $effectiveTimes,
                'expiresTime'    => time() + $time,
                'params'         => Helper::jsonStringify($params),
            ]
        );

        return $result ? $token : false;
    }

    /**
     * Check scene token
     *
     * @param string $token
     * @param int    $scene
     * @param int    $userId
     *
     * @return Error|object
     * @throws
     */
    public function checkSceneToken(string $token, int $scene = 0, ?int $userId = null)
    {
        /**
         * @var BswTokenRepository $tokenRepo
         */
        $tokenRepo = $this->repo(BswToken::class);
        $record = $tokenRepo->findOneBy(['token' => $token]);

        if (empty($record)) {
            return new ErrorOAuthNotFoundToken();
        }

        if (!$record->state) {
            return new ErrorOAuthMalformedToken();
        }

        if ($record->scene !== $scene) {
            return new ErrorOAuthMalformedToken();
        }

        if (isset($userId) && $record->userId !== $userId) {
            return new ErrorOAuthMalformedToken();
        }

        if ($record->effectiveTimes < 1) {
            return new ErrorOAuthInvalidToken();
        }

        if ($record->expiresTime < time()) {
            return new ErrorOAuthExpiredToken();
        }

        $times = $record->effectiveTimes > 1 ? ($record->effectiveTimes - 1) : 0;
        $tokenRepo->modify(
            ['id' => $record->id],
            [
                'effectiveTimes' => $times,
                'state'          => $times ? Abs::NORMAL : Abs::CLOSE,
            ]
        );

        return $record;
    }

    /**
     * Message to response
     *
     * @param Message $message
     *
     * @return Response
     */
    public function messageToResponse(Message $message): Response
    {
        if (!$this->ajax) {
            return $this->responseMessage($message);
        }

        $codeMap = [
            Abs::TAG_CLASSIFY_SUCCESS => $this->codeOkForLogic,
            Abs::TAG_CLASSIFY_ERROR   => new ErrorException(),
        ];

        $classify = $message->getClassify();
        $message->setCode($codeMap[$classify] ?? $this->codeOkForLogic);

        return $this->responseMessageWithAjax($message);
    }

    /**
     * Get config with locate
     *
     * @param string $name
     * @param bool   $inController
     * @param mixed  $default
     * @param bool   $strict
     *
     * @return mixed
     */
    public function cnfWithLocate(string $name, bool $inController = true, $default = null, bool $strict = false)
    {
        $newName = $name;
        if (!empty($this->header->lang)) {
            $newName = "{$this->header->lang}_{$name}";
        }

        if ($strict) {
            $locate = $this->cnf->{$newName} ?? $default;
        } else {
            $locate = $this->cnf->{$newName} ?? $this->cnf->{$name} ?? $default;
        }

        $defaultLocate = $this->parameter('locale', null, $inController);
        $nameHandling = "{$defaultLocate}_{$name}";
        if (!isset($this->cnf->{$nameHandling}) && isset($this->cnf->{$name})) {
            $this->cnf->{$nameHandling} = $this->cnf->{$name};
        }
        $this->cnf->{$name} = $locate;

        return $locate;
    }

    /**
     * Handler of form rules
     *
     * @param Form $form
     *
     * @return Form
     */
    public function formRulesHandler(Form $form): Form
    {
        $rules = $form->getFormRulesArray();
        foreach ($rules as $k => &$rule) {
            if (!is_array($rule) || !$rule['message']) {
                unset($rules[$k]);
            } else {
                $args = ['{{ field }}' => $this->fieldLang($form->getLabel())];
                $args = array_merge($args, $rule['args'] ?? []);
                $rule['message'] = $this->messageLang($rule['message'], $args);
            }
        }

        return $form->setFormRules($rules);
    }

    /**
     * Use tpl
     *
     * @param string $tpl
     * @param string $content
     *
     * @return string
     */
    public function useTpl(string $tpl, string $content): string
    {
        return str_replace(['{value}', '{#value}', '{:value}'], [$content, $content, $content], $tpl);
    }

    /**
     * Render handler
     *
     * @param string $render
     *
     * @return string
     */
    public function renderHandler(string $render): string
    {
        if (Helper::strEndWith($render, Abs::HTML_SUFFIX)) {
            $render = $this->caching(
                function () use ($render) {
                    return $this->renderPart($render);
                }
            );
        }

        return $render;
    }

    /**
     * @param string $tpl
     * @param string $field
     * @param array  $var
     * @param string $container
     *
     * @return string
     * @throws
     */
    public function parseSlot(string $tpl, string $field, array $var = [], string $container = null): string
    {
        static $constants;

        /**
         * constants variable
         */

        if (!isset($constants)) {
            $constantsHandling = (new ReflectionClass(Abs::class))->getConstants();
            $beginWith = [
                'NIL',
                'DIRTY',
                'NOT_SET',
                'NOT_FILE',
                'SECRET',
                'UNKNOWN',
                'UNALLOCATED',
                'COMMON',
                'TPL_',
                'SLOT_',
            ];
            foreach ($constantsHandling as $key => $value) {
                foreach ($beginWith as $target) {
                    if (strpos($key, $target) === 0) {
                        $constants["Abs::{$key}"] = $value;
                    }
                }
            }
        }

        /**
         * custom variables
         */

        $variables = array_merge(
            $constants,
            [
                'uuid'   => "__{$field}",
                ':value' => 'value',
                '@value' => '{{ value }}',
                '#value' => '{{ value === null ? "{Abs::NIL}" : value }}',
                'value'  => '{{ value }}', // may be covered, then set @value
                'title'  => $var['title'] ?? null,
                'field'  => Helper::camelToUnder($field, '-'),
            ],
            $var
        );

        /**
         * out container tpl
         */

        $template = $tpl;
        if ($container) {
            $template = str_replace('{tpl}', $template, $container);
        }

        /**
         * parse
         */

        foreach ($variables as $key => $value) {
            $find = "{{$key}}";
            if (strpos($value, $find) !== false) {
                throw new ModuleException(
                    "Slot variable doesn't seem right, is looks like replace `{$find}` use `{$value}`"
                );
            }
            $template = str_replace($find, $value, $template);
        }

        if ($tpl == $template) {
            return $template;
        }

        return $this->parseSlot($template, $field, $var);
    }

    /**
     * Handle error with diff env
     *
     * @param Exception|string $error
     * @param array            $trace
     *
     * @return string
     */
    public function errorHandler($error, array $trace = []): ?string
    {
        if ($error instanceof Exception) {
            $error = "{$error->getMessage()} in {$error->getFile()} line {$error->getLine()}";
        }

        if (!is_string($error)) {
            return null;
        }

        if ($this->debug) {
            $this->logger->error("Unforeseen error, {$error}", $trace);

            return $error;
        }

        $code = Helper::strPadLeftLength(rand(1, 9999), 4);
        $code = date('md') . $code;

        $this->logger->error("Unforeseen error, [{$code}] {$error}", $trace);

        return $this->messageLang('[{{ code }}] Unforeseen error', ['{{ code }}' => $code]);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (in_array(
            $name,
            [
                'session',
                'kernel',
                'translator',
                'redis',
                'cache',
                'logger',
                'expr',
                'response',
                'header',
                'env',
                'debug',
                'router',
                'controller',
            ]
        )) {
            return $this->{$name};
        }

        throw new InvalidArgumentException("Property " . static::class . "::{$name} is not defined");
    }
}