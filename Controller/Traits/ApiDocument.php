<?php

namespace Leon\BswBundle\Controller\Traits;

use App\Kernel;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Validator\Validator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Exception;

/**
 * @property AbstractFOSRestController $container
 * @property Kernel                    $kernel
 * @property TranslatorInterface       $translator
 */
trait ApiDocument
{
    /**
     * @inheritdoc
     * @return array
     */
    public function apiDocFlag()
    {
        return [
            'AUTH'  => 'Must authorization',
            'USER'  => 'Should authorization',
            'AJAX'  => 'Should be ajax request',
            'TOKEN' => 'Should update token',
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function apiDocOutputPage()
    {
        return [
            Abs::PG_CURRENT_PAGE => ['type' => 'int'],
            Abs::PG_PAGE_SIZE    => ['type' => 'int'],
            Abs::PG_TOTAL_PAGE   => ['type' => 'int'],
            Abs::PG_TOTAL_ITEM   => ['type' => 'int'],
            Abs::PG_ITEMS        => ['type' => 'object[]'],
        ];
    }

    /**
     * List class bill
     *
     * @param array  $extraPath
     * @param string $module
     *
     * @return array
     * @throws
     */
    public function classBill(array $extraPath, string $module): array
    {
        $paths = array_merge(
            [
                Abs::BSW_BUNDLE => [
                    'bundle'    => true,
                    'namespace' => 'Leon\BswBundle\Module\{module}\Entity',
                    'path'      => '{path}/Module/{module}/Entity',
                ],
                'CurrentApp'    => [
                    'bundle'    => false,
                    'namespace' => 'App\Module\{module}',
                    'path'      => '{path}/src/Module/{module}',
                ],
            ],
            $extraPath,
            $this->parameter('module_extra_path', [])
        );

        $pathsHandling = [];

        foreach ($paths as $key => $item) {
            if (!isset($item['bundle']) || !isset($item['namespace']) || !isset($item['path'])) {
                throw new Exception("Keys bundle/namespace/path must in config `module_extra_path` items");
            }

            if ($item['bundle']) {
                $p = $this->kernel->getBundle($key)->getPath();
            } else {
                $p = $this->kernel->getProjectDir();
            }

            $namespace = str_replace(['{path}', '{module}'], [$p, $module], $item['namespace']);
            $path = str_replace(['{path}', '{module}'], [$p, $module], $item['path']);

            if (file_exists($path)) {
                $namespace = '\\' . trim($namespace, '\\') . '\\';
                $pathsHandling[$namespace] = rtrim($path, '/') . '/';
            }
        }

        $classBill = [];
        foreach ($pathsHandling as $ns => $path) {
            Helper::directoryIterator(
                $path,
                $classBill,
                function ($file) use ($ns) {
                    if (strpos($file, '.php') === false) {
                        return false;
                    }

                    $class = pathinfo($file, PATHINFO_FILENAME);
                    $class = "{$ns}{$class}";

                    if (!class_exists($class)) {
                        return false;
                    }

                    return $class;
                }
            );
        }

        return $classBill;
    }

    /**
     * Api error bill
     *
     * @param string $lang
     * @param array  $paths
     *
     * @return array
     */
    public function apiErrorBill(string $lang, array $paths = []): array
    {
        return $this->caching(
            function () use ($lang, $paths) {

                $bill = [];
                $errorBill = $this->classBill($paths, 'Error');

                foreach ($errorBill as $error) {

                    /**
                     * @var Error $e
                     */
                    $e = new $error();

                    $code = $e->code4logic();
                    if (isset($bill[$code])) {
                        throw new Exception("Error code {$code} has repeat in {$error}");
                    }

                    $bill[] = [
                        'class'       => $error,
                        'code'        => $code,
                        'tiny'        => $this->messageLang($e->tiny(), [], $lang),
                        'description' => $this->messageLang($e->description(), [], $lang),
                    ];
                }

                ksort($bill);

                return $bill;
            }
        );
    }

    /**
     * Api validator bill
     *
     * @param string $lang
     * @param array  $paths
     *
     * @return array
     */
    public function apiValidatorBill(string $lang, array $paths = []): array
    {
        return $this->caching(
            function () use ($lang, $paths) {

                $bill = [];
                $validatorBill = $this->classBill($paths, 'Validator');

                foreach ($validatorBill as $validator) {

                    /**
                     * @var Validator $v
                     */
                    $v = new $validator(null, [], $this->translator, $lang);

                    $rule = Helper::camelToUnder(Helper::clsName($validator));
                    $field = $this->fieldLang('Field', [], $lang);

                    $bill[] = [
                        'class'       => $validator,
                        'rule'        => $rule,
                        'description' => $this->messageLang($v->description(), [], $lang),
                        'message'     => $this->messageLang($v->message(), ['{{ field }}' => $field], $lang),
                    ];
                }

                return $bill;
            }
        );
    }
}