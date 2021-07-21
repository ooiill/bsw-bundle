<?php

namespace Leon\BswBundle\Component;

use Doctrine\DBAL\Types\Types;
use Leon\BswBundle\Module\Entity\Abs;
use BadFunctionCallException;
use InvalidArgumentException;
use Leon\BswBundle\Module\Error\Entity\ErrorDebugExit;
use ZipArchive;
use Exception;
use DateTime;
use finfo;

class Helper
{
    /**
     * Regex for variable
     *
     * @param string $add
     *
     * @return string
     */
    public static function reg4var(string $add = null): string
    {
        return "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff{$add}]+)\}/";
    }

    /**
     * Special for char
     *
     * @param string $add
     *
     * @return string
     */
    public static function special4char(string $add = null): string
    {
        return "`-=[];'\,.//~!@#$%^&*()_+{}:\"|<>?·【】；’、，。、！￥…（）—：“《》？{$add}";
    }

    /**
     * Dict for decimal
     *
     * @param string $add
     *
     * @return string
     */
    public static function dict4dec(string $add = null): string
    {
        return "AWi{$add}2QFN3VqUC4xPDazgXEOut1feMLdTbHK9sZrRJv5j7pcy8SkmYl60oBwIGnh";
    }

    /**
     * String length
     *
     * @param string $content
     * @param string $encoding
     *
     * @return int
     */
    public static function strLen(?string $content, string $encoding = 'utf-8'): int
    {
        return mb_strlen($content, $encoding);
    }

    /**
     * String length by bytes
     *
     * @param string $content
     *
     * @return array
     */
    public static function strLenByBytes(string $content): array
    {
        $length = [];
        $target = [];
        preg_replace_callback(
            '/./u',
            function ($match) use (&$length, &$target) {
                $bytes = strlen($match[0]);
                if (!isset($length[$bytes])) {
                    $length[$bytes] = 0;
                    $target[$bytes] = null;
                }
                $length[$bytes] += 1;
                $target[$bytes] .= $match[0];
            },
            $content
        );

        return [$length, $target];
    }

    /**
     * Singleton
     *
     * @param callable $logicHandler
     * @param bool     $forceNew
     * @param array    $params
     *
     * @return mixed
     */
    public static function singleton(callable $logicHandler, bool $forceNew = false, array $params = null)
    {
        static $container = [];

        if (is_null($params)) {
            $params = self::backtrace(1, ['function', 'args']);
        }

        $key = md5(self::jsonStringify($params));

        if (!array_key_exists($key, $container) || $forceNew) {
            $container[$key] = call_user_func_array($logicHandler, [$params]);
        }

        return $container[$key];
    }

    /**
     * Dump for object, array, string
     *
     * @param mixed  $var
     * @param bool   $exit
     * @param bool   $strict
     * @param bool   $echo
     * @param string $tag
     *
     * @return mixed
     */
    public static function dump($var, bool $exit = true, bool $strict = false, bool $echo = true, string $tag = 'pre')
    {
        $startTag = $tag ? "<{$tag}>" : null;
        $endTag = $tag ? "</{$tag}>" : null;

        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = $startTag . htmlspecialchars($output, ENT_QUOTES) . $endTag;
            } else {
                $output = $startTag . print_r($var, true) . $endTag;
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = $startTag . htmlspecialchars($output, ENT_QUOTES) . $endTag;
            }
        }

        if (!$echo) {
            return $output;
        }

        echo($output);
        $exit && exit(ErrorDebugExit::CODE);

        return null;
    }

    /**
     * Number format for money
     *
     * @param mixed  $number
     * @param string $tpl
     * @param int    $decimals
     *
     * @return string
     */
    public static function money($number, string $tpl = '￥%s', int $decimals = 2): string
    {
        return sprintf($tpl, self::numberFormat($number, $decimals));
    }

    /**
     * Array map value for handle items
     *
     * @param array           $target
     * @param string|callable $handler
     *
     * @return array
     */
    public static function arrayMap(array $target, $handler): array
    {
        foreach ($target as $key => &$item) {
            if (is_callable($handler)) {
                $item = call_user_func_array($handler, [$item, $key]);
            } else {
                if (!is_scalar($item)) {
                    continue;
                }
                $itemHandling = sprintf($handler, $item);
                $item = str_replace(['{key}', '{value}'], [$key, $item], $itemHandling);
            }
        }

        return $target;
    }

    /**
     * Array map key for handle items
     *
     * @param array           $target
     * @param string|callable $handler
     *
     * @return array
     */
    public static function arrayMapKey(array $target, $handler): array
    {
        $targetHandling = [];
        foreach ($target as $key => $item) {
            if (is_callable($handler)) {
                $key = call_user_func_array($handler, [$key, $item]);
            } else {
                if (!is_scalar($item)) {
                    continue;
                }
                $keyHandling = sprintf($handler, $key);
                $key = str_replace(['{key}', '{value}'], [$key, $item], $keyHandling);
            }
            $targetHandling[$key] = $item;
        }

        return $targetHandling;
    }

    /**
     * Pull multiple work for more-dimensional array
     *
     * @param array $target
     * @param array $pull
     * @param bool  $popTarget
     * @param mixed $default
     *
     * @return array
     */
    public static function arrayPull(array &$target, array $pull, bool $popTarget = false, $default = null): array
    {
        $targetHandling = [];
        foreach ($pull as $oldKey => $newKey) {
            $oldKey = is_numeric($oldKey) ? $newKey : $oldKey;

            if (is_null($value = $target[$oldKey] ?? null) && is_null($default)) {
                if ($popTarget && array_key_exists($oldKey, $target)) {
                    unset($target[$oldKey]);
                }
                continue;
            }

            $targetHandling[$newKey] = $value ?? $default;
            if ($popTarget) {
                unset($target[$oldKey]);
            }
        }

        return $targetHandling;
    }

    /**
     * Pull one and unset it
     *
     * @param array  $target
     * @param string $key
     *
     * @return mixed|null
     */
    public static function dig(array &$target, string $key)
    {
        $item = $target[$key] ?? null;
        unset($target[$key]);

        return $item;
    }

    /**
     * Change array keys
     *
     * @param array $target
     * @param array $map
     * @param bool  $strict
     *
     * @return array
     */
    public static function changeArrayKeys(array $target, array $map, bool $strict = true): array
    {
        foreach ($map as $from => $to) {
            if ($strict && !key_exists($from, $target)) {
                continue;
            }
            $target[$to] = self::dig($target, $from);
        }

        return $target;
    }

    /**
     * Pop items and unset they
     *
     * @param array $target
     * @param array $items
     *
     * @return array
     */
    public static function arrayPop(array &$target, array $items): array
    {
        $value = [];
        foreach ($items as $item) {
            $value[$item] = self::dig($target, $item);
        }

        return $value;
    }

    /**
     * Remove items and unset they by keys
     *
     * @param array $target
     * @param array $items
     *
     * @return array
     */
    public static function arrayRemove(array $target, array $items): array
    {
        foreach ($items as $item) {
            self::dig($target, $item);
        }

        return $target;
    }

    /**
     * Remove items and unset they by values
     *
     * @param array $target
     * @param array $values
     *
     * @return array
     */
    public static function arrayRemoveByValues(array $target, array $values): array
    {
        foreach ($values as $value) {
            $index = array_search($value, $target);
            if ($index !== false) {
                self::dig($target, $index);
            }
        }

        return $target;
    }

    /**
     * Strengthen for array_column
     *
     * @param array  $target
     * @param mixed  $valueKeys
     * @param string $keyKey
     * @param bool   $keyKeyStrict
     *
     * @return array
     */
    public static function arrayColumn(
        array $target,
        $valueKeys,
        string $keyKey = null,
        bool $keyKeyStrict = false
    ): array {

        if (!is_null($keyKey) && is_string($valueKeys)) {
            return array_column($target, $valueKeys, $keyKey);
        }

        $targetHandling = [];
        foreach ($target as $key => $item) {
            $keyHandling = $keyKey ? ($item[$keyKey] ?? ($keyKeyStrict ? null : $key)) : $key;
            if (is_null($keyHandling)) {
                continue;
            }
            if (is_string($valueKeys) || is_numeric($valueKeys)) {
                $targetHandling[$keyHandling] = $item[$valueKeys] ?? null;
            } elseif (is_array($valueKeys)) {
                $targetHandling[$keyHandling] = self::arrayPull($item, $valueKeys);
            } elseif ($valueKeys === true) {
                $targetHandling[$keyHandling] = $item;
            } elseif ($valueKeys === false) {
                self::arrayPop($item, [$keyKey]);
                $targetHandling[$keyHandling] = $item;
            }
        }

        return $targetHandling;
    }

    /**
     * Reverse array_column
     *
     * @param array  $target
     * @param string $keyKey
     * @param string $valueKey
     * @param string $indexKey
     * @param array  $extra
     *
     * @return array
     */
    public static function reverseArrayColumn(
        array $target,
        string $keyKey = 'index',
        string $valueKey = 'value',
        string $indexKey = 'index',
        array $extra = []
    ): array {

        $targetHandling = [];
        $index = 0;
        foreach ($target as $key => $value) {
            $index += 1;
            $itemIndex = $indexKey ? [$indexKey => $index] : [];
            $itemKey = [$keyKey => $key];
            $itemValue = is_array($value) ? $value : [$valueKey => $value];

            $targetHandling[] = array_merge($itemIndex, $itemKey, $itemValue, $extra);
        }

        return $targetHandling;
    }

    /**
     * Cover default with manual
     *
     * @param array $default
     * @param array $manual
     * @param bool  $obligateNull
     *
     * @return array
     */
    public static function manualCoverDefault(array $default, array $manual, bool $obligateNull = false): array
    {
        $real = array_intersect(array_keys($default), array_keys($manual));
        $real = self::arrayPull($manual, $real);

        if ($obligateNull) {
            return array_merge(self::arrayValuesSetTo($default, null), $real);
        }

        return $real;
    }

    /**
     * Function backtrace
     *
     * @param int    $index
     * @param mixed  $fields
     * @param string $key
     *
     * @return mixed
     */
    public static function backtrace(int $index = 1, $fields = true, string $key = null)
    {
        // exclude self
        $index += 1;

        $backTrance = debug_backtrace();
        if (!empty($fields)) {
            $backTrance = self::arrayColumn($backTrance, $fields, $key);
        }

        if ($index < 1) {
            return $backTrance;
        }

        return $backTrance[$index] ?? false;
    }

    /**
     * helloWorld to hello_world
     *
     * @param string $str
     * @param string $split
     *
     * @return string
     */
    public static function camelToUnder(string $str, string $split = '_'): string
    {
        return strtolower(trim(preg_replace("/[A-Z]/", "{$split}\\0", $str), $split));
    }

    /**
     * hello666 to hello_666
     *
     * @param string $str
     * @param string $split
     *
     * @return string
     */
    public static function mixedToUnder(string $str, string $split = '_'): string
    {
        $matches = [];
        preg_match_all("/(\D+|\d+)/", $str, $matches);

        return join($split, $matches[1]);
    }

    /**
     * hello_world to helloWorld
     *
     * @param string       $str
     * @param bool         $small
     * @param string|array $split
     *
     * @return string
     */
    public static function underToCamel(string $str, bool $small = true, $split = '_'): string
    {
        $split = (array)$split;
        $str = str_replace($split, array_fill(0, count($split), self::enSpace()), $str);
        $str = ucwords($str);
        $str = str_replace(self::enSpace(), null, $str);

        return $small ? lcfirst($str) : $str;
    }

    /**
     * Array key helloWorld to hello_world
     *
     * @param array  $source
     * @param string $split
     *
     * @return array
     */
    public static function keyCamelToUnder(array $source, string $split = '_'): array
    {
        $sourceHandling = [];
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $value = self::keyCamelToUnder($value, $split);
            }
            $sourceHandling[self::camelToUnder($key, $split)] = $value;
        }

        return $sourceHandling;
    }

    /**
     * Array value helloWorld to hello_world
     *
     * @param array  $source
     * @param string $split
     *
     * @return array
     */
    public static function valueCamelToUnder(array $source, string $split = '_'): array
    {
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $source[$key] = self::valueCamelToUnder($value, $split);
            } else {
                $source[$key] = self::camelToUnder($value, $split);
            }
        }

        return $source;
    }

    /**
     * Array key hello_world to helloWorld
     *
     * @param array        $source
     * @param bool         $small
     * @param string|array $split
     *
     * @return array
     */
    public static function keyUnderToCamel(array $source, bool $small = true, $split = '_'): array
    {
        $sourceHandling = [];
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $value = self::keyUnderToCamel($value, $small, $split);
            }
            $sourceHandling[self::underToCamel($key, $small, $split)] = $value;
        }

        return $sourceHandling;
    }

    /**
     * Array value hello_world to helloWorld
     *
     * @param array  $source
     * @param bool   $small
     * @param string $split
     *
     * @return array
     */
    public static function valueUnderToCamel(array $source, bool $small = true, string $split = '_'): array
    {
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $source[$key] = self::valueUnderToCamel($value, $small, $split);
            } else {
                $source[$key] = self::underToCamel($value, $small, $split);
            }
        }

        return $source;
    }

    /**
     * GBK double byte
     *
     * @param string $str
     *
     * @return bool
     */
    public static function gbkDoubleByte(string $str): bool
    {
        return preg_match('/[\x{00}-\x{ff}]/u', $str) > 0;
    }

    /**
     * GBK ASCII
     *
     * @param string $str
     *
     * @return bool
     */
    public static function gbkAscii(string $str): bool
    {
        return preg_match('/[\x{20}-\x{7f}]/u', $str) > 0;
    }

    /**
     * GB2312 chinese
     *
     * @param string $str
     *
     * @return bool
     */
    public static function gb2312Chinese(string $str): bool
    {
        return preg_match('/[\x{a1}-\x{ff}]/u', $str) > 0;
    }

    /**
     * GBK chinese
     *
     * @param string $str
     *
     * @return bool
     */
    public static function gbkChinese(string $str): bool
    {
        return preg_match('/[\x{80}-\x{ff}]/u', $str) > 0;
    }

    /**
     * UTF8 chinese
     *
     * @param string $str
     *
     * @return bool
     */
    public static function utf8Chinese(string $str): bool
    {
        return preg_match('/[\x{4e00}-\x{9fa5}]/u', $str) > 0;
    }

    /**
     * hello_world to Hello world
     *
     * @param string $str
     * @param string $replace
     *
     * @return string
     */
    public static function stringToLabel(string $str, string $replace = '-_'): string
    {
        if (self::utf8Chinese($str)) {
            return $str;
        }

        $str = self::camelToUnder($str, $replace[0] ?? ' ');
        $str = str_replace(self::split($replace), self::enSpace(), $str);
        $str = ucfirst(strtolower($str));

        return $str;
    }

    /**
     * Get namespace without class name
     *
     * @param string $namespace
     * @param string $replace
     *
     * @return string
     */
    public static function nsName(string $namespace, string $replace = null): string
    {
        $cls = explode('\\', $namespace);
        array_pop($cls);

        return str_replace($replace, null, implode('\\', $cls));
    }

    /**
     * Get class name without namespace
     *
     * @param string $namespace
     * @param string $replace
     *
     * @return string
     */
    public static function clsName(string $namespace, string $replace = null): string
    {
        $cls = explode('\\', $namespace);

        return str_replace($replace, null, end($cls));
    }

    /**
     * Merge items to object
     *
     * @param array ...$items
     *
     * @return mixed
     */
    public static function objects(...$items)
    {
        foreach ($items as &$item) {
            $item = (array)$item;
        }

        return (object)array_merge(...$items);
    }

    /**
     * Directory iterator
     *
     * @param string    $path
     * @param array    &$tree
     * @param callable  $fileCall
     * @param callable  $dirCall
     *
     * @return void
     */
    public static function directoryIterator(
        string $path,
        array &$tree,
        callable $fileCall = null,
        callable $dirCall = null
    ) {
        if (!is_dir($path) || !($handler = opendir($path))) {
            return;
        }

        while (false !== ($item = readdir($handler))) {

            if ($item == '.' || $item == '..') {
                continue;
            }

            $filePath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($filePath)) {

                $result = $filePath;
                is_callable($dirCall) && $result = call_user_func_array($dirCall, [$filePath, $item, $path]);

                if (!$result) {
                    continue;
                }

                if (is_array($result) && isset($result['key']) && isset($result['value'])) {
                    $item = $result['key'];
                    $result = $result['value'];
                }

                $tree[$item] = $tree[$item] ?? [];
                self::directoryIterator($result, $tree[$item], $fileCall, $dirCall);

            } else {

                $result = $filePath;
                is_callable($fileCall) && $result = call_user_func_array($fileCall, [$filePath, $item, $path]);

                if (!$result) {
                    continue;
                }

                if (is_array($result) && isset($result['key']) && isset($result['value'])) {
                    $tree[$result['key']] = $result['value'];
                } elseif ($result) {
                    $tree[] = $result;
                }
            }
        }

        closedir($handler);
    }

    /**
     * Get directory files's md5 collect
     *
     * @param string $path
     *
     * @return array
     */
    public static function getDirectoryMd5s(string $path): array
    {
        $md5Tree = [];
        Helper::directoryIterator(
            $path,
            $md5Tree,
            function ($file) {
                return md5_file($file);
            }
        );

        return $md5Tree;
    }

    /**
     * Zip directory
     *
     * @param string $directory
     * @param string $zipFilePath
     *
     * @return mixed
     */
    public static function archiveDirectory(string $directory, string $zipFilePath = null)
    {
        $zip = new ZipArchive();

        $dirInfo = pathinfo($directory);
        $zipFilePath = $zipFilePath ?: $dirInfo['dirname'] . DIRECTORY_SEPARATOR . $dirInfo['basename'] . '.zip';

        if (true !== $zip->open($zipFilePath, ZipArchive::CREATE)) {
            return 'create zip file failed';
        }

        if (is_array($directory)) {
            foreach ($directory as $localName => $file) {
                $zip->addFile($file, is_numeric($localName) ? null : $localName);
            }
        } else {
            $tree = [];
            self::directoryIterator(
                $directory,
                $tree,
                function ($file) use ($directory, $zip) {
                    $localName = str_replace($directory, null, $file);
                    $localName = DIRECTORY_SEPARATOR . ltrim($localName, DIRECTORY_SEPARATOR);
                    $zip->addFile($file, $localName);
                }
            );
        }

        return $zip->close();
    }

    /**
     * Get the tree by array
     *
     * @param array  $items
     * @param string $pk
     * @param string $parentKey
     * @param string $childrenKey
     *
     * @return array
     */
    public static function tree(
        array $items,
        string $pk = Abs::PK,
        string $parentKey = Abs::TAG_PARENT,
        string $childrenKey = Abs::TAG_CHILDREN
    ): array {
        if (empty($items)) {
            return [];
        }

        $tree = [];
        $items = self::arrayColumn($items, true, $pk);

        foreach ($items as $item) {
            if (!empty($items[$item[$parentKey]])) {
                $items[$item[$parentKey]][$childrenKey][] = &$items[$item[$pk]];
            } else {
                $tree[] = &$items[$item[$pk]];
            }
        }

        return $tree;
    }

    /**
     * Get the tree parent by array
     *
     * @param array  $items
     * @param string $pk
     * @param string $parentKey
     *
     * @return array
     */
    public static function treeParentMap(array $items, string $pk = Abs::PK, string $parentKey = Abs::TAG_PARENT): array
    {
        if (empty($items)) {
            return [];
        }

        $tree = [];
        $items = self::arrayColumn($items, $parentKey, $pk);

        /**
         * Get parent
         *
         * @param mixed $children
         * @param array $parent
         *
         * @return array
         */
        $getParent = function ($children, array $parent = []) use ($items, &$getParent): array {
            if (is_null($value = $items[$children] ?? null)) {
                return $parent;
            }
            $parent[$children] = count($parent) + 1;

            return $getParent($value, $parent);
        };

        foreach ($items as $children => $parent) {
            $tree[$children] = $getParent($parent);
        }

        return $tree;
    }

    /**
     * Get the tree children by array
     *
     * @param array  $items
     * @param string $pk
     * @param string $parentKey
     * @param string $childrenKey
     *
     * @return array
     */
    public static function treeChildrenMap(
        array $items,
        string $pk = Abs::PK,
        string $parentKey = Abs::TAG_PARENT,
        string $childrenKey = Abs::TAG_CHILDREN
    ): array {
        if (empty($items)) {
            return [];
        }

        $tree = [];
        $items = self::tree($items, $pk, $parentKey, $childrenKey);

        /**
         * Get children
         *
         * @param array $items
         * @param int   $zIndex
         * @param array $zIndexKey
         * @param array $tree
         */
        $getChildren = function (array $items, int $zIndex, array $zIndexKey, array &$tree) use (
            &$getChildren,
            $pk,
            $childrenKey
        ) {
            foreach ($items as $v) {
                foreach ($zIndexKey as $k) {
                    $tree[$k][$v[$pk]] = $zIndex;
                }
                if (!empty($v[$childrenKey])) {
                    $zIndexKeyHandling = array_merge($zIndexKey, [$v[$pk]]);
                    $getChildren($v[$childrenKey], $zIndex + 1, $zIndexKeyHandling, $tree);
                }
            }
        };

        $top = 0;
        $getChildren($items, 0, [$top], $tree);
        $all = Helper::dig($tree, $top);

        foreach ($all as $m => $n) {
            $all[$m] = $n + 1;
        }

        foreach ($tree as $key => &$item) {
            foreach ($item as $m => $n) {
                $item[$m] = $n - $all[$key] + 1;
            }
        }

        return [$top => $all] + $tree;
    }

    /**
     * Merges more arrays into one recursively
     *
     * @param array ...$items
     *
     * @return array
     * @license modify from yii2framework
     */
    public static function merge(...$items): array
    {
        $target = array_shift($items);

        if (empty($items)) {
            return (array)$target;
        }

        foreach ($items as $next) {
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $target)) {
                        $target[] = $v;
                    } else {
                        $target[$k] = $v;
                    }
                } elseif (is_array($v) && isset($target[$k]) && is_array($target[$k])) {
                    $target[$k] = self::merge($target[$k], $v);
                } else {
                    $target[$k] = $v;
                }
            }
        }

        return $target;
    }

    /**
     * Merges more arrays with weak into one recursively
     *
     * @param bool  $assocOnly
     * @param bool  $transpose
     * @param bool  $lowerNull
     * @param array ...$items
     *
     * @return array
     */
    public static function mergeWeak(bool $assocOnly, bool $transpose, bool $lowerNull = true, ...$items): array
    {
        $target = array_shift($items);

        if (empty($items)) {
            return $target;
        }

        foreach ($items as $next) {
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $target)) {
                        $target[] = $v;
                    } else {
                        $target[$k] = $v;
                    }
                } elseif (is_array($v) && isset($target[$k]) && is_array($target[$k])) {
                    if ($assocOnly && self::typeofArray($v, Abs::T_ARRAY_INDEX)) {
                        $target[$k] = $v;
                    } else {
                        $itemsHandling = [$target[$k], $v];
                        $transpose && $itemsHandling = array_reverse($itemsHandling);
                        $target[$k] = self::mergeWeak($assocOnly, $transpose, $lowerNull, ...$itemsHandling);
                    }
                } else {
                    if (!$lowerNull || ($lowerNull && !is_null($v))) {
                        $target[$k] = $v;
                    }
                }
            }
        }

        return $target;
    }

    /**
     * Merge the more-dimensional
     *
     * @param array $target
     * @param array $from
     *
     * @return array
     */
    public static function mergeTheSecond(array $target, array $from): array
    {
        foreach ($from as $key => $item) {
            $targetItem = $target[$key] ?? [];
            $targetItem = $targetItem ? ((array)$targetItem) : [];

            if (is_array($item)) {
                $target[$key] = array_merge($targetItem, $item);
            } else {
                $target[$key] = $item;
            }
        }

        return $target;
    }

    /**
     * Print json code with format
     *
     * @param mixed  $json
     * @param int    $tabBySpace
     * @param string $split
     * @param string $prefix
     *
     * @return string
     */
    public static function formatPrintJson(
        $json,
        int $tabBySpace = 2,
        string $split = ':',
        string $prefix = null
    ): ?string {

        if (empty($json)) {
            return null;
        }
        if (is_string($json)) {
            $json = self::parseJsonString($json);
        }
        $json = self::jsonStringify($json);

        $result = null;
        $pos = 0;
        $quotaCount = 0;
        $strLen = strlen($json);
        $indentStr = self::enSpace($tabBySpace);
        $newLine = PHP_EOL . $prefix;
        $prevChar = null;
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);
            $nextChar = substr($json, $i + 1, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $quotaCount += 1;
                $outOfQuotes = !$outOfQuotes;
            } else {

                $aScene = ($char == '}' && $prevChar != '{');
                $bScene = ($char == ']' && $prevChar != '[');

                if (($aScene || $bScene) && $outOfQuotes) {
                    $quotaCount = 0;
                    $result .= $newLine;
                    $pos--;
                    for ($j = 0; $j < $pos; $j++) {
                        $result .= $indentStr;
                    }
                }
            }

            // Add the character to the result string.
            if ($char == ':' && ($quotaCount % 2 == 0)) {
                $result .= $split;
            } else {
                $result .= $char;
            }

            $aScene = ($char == '{' && $nextChar != '}');
            $bScene = ($char == '[' && $nextChar != ']');

            if (($char == ',' || $aScene || $bScene) && $outOfQuotes) {
                $quotaCount = 0;
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
            $prevChar = $char;
        }

        return "{$prefix}{$result}";
    }

    /**
     * Print array
     *
     * @param array  $source
     * @param string $boundary
     * @param string $indicate
     * @param string $split
     * @param string $highOrder
     *
     * @return string
     */
    public static function printArray(
        array $source,
        string $boundary = '[%s]',
        string $indicate = ':',
        string $split = ',',
        string $highOrder = null
    ): string {

        $print = [];
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $value = $highOrder ?: self::printArray($value, $boundary, $indicate, $split, $highOrder);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $key = $indicate ? "{$key}{$indicate}" : null;
            array_push($print, "{$key}{$value}");
        }

        $print = implode($split, $print);
        if (!$boundary) {
            return $print;
        }

        return sprintf($boundary, $print);
    }

    /**
     * Print php array to string
     *
     * @param array $item
     *
     * @return string
     */
    public static function printPhpArray(array $item): string
    {
        $stringify = var_export($item, true);

        $patterns = [
            "/NULL/"                                       => 'null',
            "/\'([\\\a-zA-Z0-9]+)(::)([\\\a-zA-Z0-9]+)\'/" => '$1$2$3',
        ];

        $stringify = preg_replace(array_keys($patterns), array_values($patterns), $stringify);

        $stringify = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $stringify);
        $stringify = preg_split("/\r\n|\n|\r/", $stringify);
        $stringify = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $stringify);
        $stringify = join(PHP_EOL, array_filter(["["] + $stringify));

        return $stringify;
    }

    /**
     * Get current url
     *
     * @param bool $useServerName
     *
     * @return string
     */
    public static function currentUrl(bool $useServerName = false): string
    {
        $scheme = 'http';
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            $scheme = 'https';
        }

        if ($useServerName) {
            $url = "{$scheme}://{$_SERVER['SERVER_NAME']}";
            if (!in_array($_SERVER['SERVER_PORT'], [null, 80, 443])) {
                $url .= ":{$_SERVER['SERVER_PORT']}";
            }
        } else {
            $url = "{$scheme}://{$_SERVER['HTTP_HOST']}";
        }

        $url .= $_SERVER['REQUEST_URI'];

        return $url;
    }

    /**
     * Parse url to items
     *
     * @param string $url
     *
     * @return array
     */
    public static function getUrlItems(string $url = null): array
    {
        $url = $url ?: self::currentUrl();
        $items = parse_url($url);
        $items['port'] = $items['port'] ?? '80';

        $port = $items['port'] == '80' ? null : ":{$items['port']}";
        $path = $items['path'] ?? null;

        $items['base_url'] = "{$items['scheme']}://{$items['host']}{$port}{$path}";

        $items['params'] = [];
        if (isset($items['query'])) {
            parse_str($items['query'], $items['params']);
        }

        return $items;
    }

    /**
     * Add params for url
     *
     * @param array  $setParams
     * @param string $url
     *
     * @return string
     */
    public static function addParamsForUrl(array $setParams, string $url = null): string
    {
        $url = $url ?: self::currentUrl();
        $items = self::getUrlItems($url);

        $setParams = self::merge($items['params'], $setParams);
        $url = trim("{$items['base_url']}?" . http_build_query($setParams), '?');

        return $url;
    }

    /**
     * Strip the param of the url
     *
     * @param array  $unsetParams
     * @param string $url
     *
     * @return string
     */
    public static function unsetParamsForUrl(array $unsetParams, string $url = null): string
    {
        $url = $url ?: self::currentUrl();
        $items = self::getUrlItems($url);

        foreach ($unsetParams as $val) {
            unset($items['params'][$val]);
        }

        $url = trim("{$items['base_url']}?" . http_build_query($items['params']), '?');

        return $url;
    }

    /**
     * Get anchor
     *
     * @param null|string $url
     *
     * @return string
     */
    public static function getAnchor(string $url = null): string
    {
        $url = $url ?? self::currentUrl();
        if (strpos($url, '#') !== false) {
            return trim(explode("#", $url)[1], "# ");
        }
        $args = self::getUrlItems($url);

        return $args['params']['anchor'] ?? '';
    }

    /**
     * Set anchor
     *
     * @param string      $anchor
     * @param null|string $url
     *
     * @return string
     */
    public static function setAnchor(string $anchor, string $url = null): string
    {
        if (strpos($url, '#') !== false) {
            $url = explode("#", $url)[0];
        }

        return self::addParamsForUrl(['anchor' => $anchor], $url);
    }

    /**
     * Build url query
     *
     * @param array  $params
     * @param string $url
     *
     * @return string|null
     */
    public static function httpBuildQuery(array $params = null, string $url = null)
    {
        if (empty($params)) {
            return $url;
        }

        $query = http_build_query($params);
        $url .= strpos($url, '?') === false ? "?{$query}" : "&{$query}";

        return trim($url, '&?');
    }

    /**
     * Build url query in order
     *
     * @param array  $params
     * @param string $url
     *
     * @return string|null
     */
    public static function httpBuildQueryOrderly(array $params = null, string $url = null)
    {
        if (empty($params)) {
            return $url;
        }

        $query = null;
        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                $query .= rtrim($query, '&') . $value;
            } else {
                $query .= ("{$key}={$value}&");
            }
        }

        $query = rtrim($query, '&');
        $url .= (strpos($url, '?') !== false) ? "&{$query}" : "?{$query}";

        return trim($url, '&?');
    }

    /**
     * cURL
     *
     * @param string   $url
     * @param string   $method
     * @param array    $params
     * @param callable $optionHandler
     * @param string   $contentType
     * @param bool     $async
     * @param int      $httpCode
     *
     * @return mixed
     * @throws
     */
    public static function cURL(
        string $url,
        string $method = Abs::REQ_GET,
        array $params = null,
        callable $optionHandler = null,
        string $contentType = Abs::CONTENT_TYPE_FORM,
        bool $async = false,
        int &$httpCode = 200
    ) {
        $options = [];

        // https
        if (strpos($url, 'https') === 0) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = false;
        }

        // enabled sync
        if ($async) {
            $options[CURLOPT_NOSIGNAL] = true;
            $options[CURLOPT_TIMEOUT_MS] = 100;
        }

        // enabled show header
        $options[CURLOPT_HEADER] = false;

        if ($method === Abs::REQ_HEAD) {
            $options[CURLOPT_NOBODY] = true;
            $options[CURLOPT_HEADER] = true;
        }

        // enabled auto show return info
        $options[CURLOPT_RETURNTRANSFER] = true;

        // connect
        $options[CURLOPT_FRESH_CONNECT] = true;
        $options[CURLOPT_FORBID_REUSE] = true;

        // method
        $options[CURLOPT_CUSTOMREQUEST] = $method;

        // url
        if (in_array($method, [Abs::REQ_GET, Abs::REQ_DELETE])) {
            $options[CURLOPT_URL] = self::httpBuildQuery($params, $url);
        } else {
            $options[CURLOPT_URL] = $url;
        }

        if ($contentType) {
            $options[CURLOPT_HTTPHEADER] = ["Content-Type: {$contentType}"];
        }

        // use method POST
        if (strtoupper($method === Abs::REQ_POST)) {
            $options[CURLOPT_POST] = true;
            if (!empty($params)) {
                if ($contentType == Abs::CONTENT_TYPE_FORM) {
                    $options[CURLOPT_POSTFIELDS] = http_build_query($params);
                } elseif ($contentType == Abs::CONTENT_TYPE_JSON) {
                    $options[CURLOPT_POSTFIELDS] = self::jsonStringify($params);
                } else {
                    $options[CURLOPT_POSTFIELDS] = $params;
                }
            }
        }

        // init
        $curl = curl_init();

        // callback
        if ($optionHandler) {
            $options = call_user_func_array($optionHandler, [$options]);
        }

        curl_setopt_array($curl, $options);
        $content = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($content === false) {
            throw new Exception("cURL error for url {$url} with " . curl_error($curl));
        }

        return $content;
    }

    /**
     * New item difference to old
     *
     * @param array $old
     * @param array $new
     * @param bool  $strict
     *
     * @return array
     */
    public static function newDifferenceOld(array $old, array $new, bool $strict = true): array
    {
        if (!$strict) {
            $old = self::numericValues($old);
            $new = self::numericValues($new);
        }

        $old = array_map('serialize', $old);
        $new = array_map('serialize', $new);

        $intersect = array_intersect($new, $old);
        $add = array_values(array_diff($new, $intersect));
        $del = array_values(array_diff($old, $intersect));

        return [
            array_map('unserialize', $add),
            array_map('unserialize', $del),
        ];
    }

    /**
     * New item difference to old with assoc
     *
     * @param array $new
     * @param array $old
     * @param bool  $strict
     *
     * @return array
     */
    public static function newDifferenceOldWithAssoc(array $old, array $new, bool $strict = true): array
    {
        if (!$strict) {
            $old = self::numericValues($old);
            $new = self::numericValues($new);
        }

        $old = array_map('serialize', $old);
        $new = array_map('serialize', $new);

        $intersect = array_intersect_assoc($new, $old);
        $add = array_diff_assoc($new, $intersect);
        $del = array_diff_assoc($old, $intersect);

        return [
            array_map('unserialize', $add),
            array_map('unserialize', $del),
        ];
    }

    /**
     * Create sign
     *
     * @param array  $param
     * @param string $salt
     * @param string $timeKey
     * @param string $splitSalt
     * @param string $mode
     *
     * @return array
     */
    public static function createSign(
        array $param,
        string $salt,
        string $timeKey = 'time',
        string $splitSalt = '#',
        string $mode = Abs::SORT_DESC
    ): array {

        if (!isset($param[$timeKey])) {
            $param[$timeKey] = self::milliTime();
        }

        $param = http_build_query($param);
        parse_str($param, $params);
        $mode = strtoupper($mode);
        $mode === Abs::SORT_DESC ? krsort($params) : ksort($params);

        $signStr = self::jsonStringify($params) . $splitSalt . $salt;

        return [$params, strtolower(sha1(md5($signStr))), $signStr];
    }

    /**
     * Validation sign
     *
     * @param array  $param
     * @param string $salt
     * @param string $oldSignMd5
     * @param string $timeKey
     * @param string $splitSalt
     *
     * @return bool
     */
    public static function validateSign(
        array $param,
        string $salt,
        string $oldSignMd5,
        string $timeKey = 'time',
        string $splitSalt = '#'
    ): bool {
        [$param, $newSignMd5] = self::createSign($param, $salt, $timeKey, $splitSalt);

        return strcmp($oldSignMd5, $newSignMd5) === 0;
    }

    /**
     * Create signature
     *
     * @param array  $param
     * @param string $salt
     * @param string $timeKey
     * @param string $splitKvp
     * @param string $splitArgs
     * @param string $splitSalt
     * @param string $mode
     *
     * @return array
     */
    public static function createSignature(
        array $param,
        string $salt,
        string $timeKey = 'time',
        string $splitKvp = ' is ',
        string $splitArgs = ' and ',
        string $splitSalt = ' & ',
        string $mode = Abs::SORT_DESC
    ): array {
        if (!isset($param[$timeKey])) {
            $param[$timeKey] = self::milliTime();
        }
        $param[$timeKey] = intval($param[$timeKey]);

        $sign = [];
        $mode = strtoupper($mode);
        $mode === Abs::SORT_DESC ? krsort($param) : ksort($param);
        foreach ($param as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            array_push($sign, $key . $splitKvp . $value);
        }

        $signStr = implode($splitArgs, $sign) . $splitSalt . $salt;

        return [$param, strtolower(md5($signStr)), $signStr];
    }

    /**
     * Validation signature
     *
     * @param array  $param
     * @param string $salt
     * @param string $oldSignMd5
     * @param string $timeKey
     * @param string $splitKvp
     * @param string $splitArgs
     * @param string $splitSalt
     *
     * @return bool
     */
    public static function validateSignature(
        array $param,
        string $salt,
        string $oldSignMd5,
        string $timeKey = 'time',
        string $splitKvp = ' is ',
        string $splitArgs = ' and ',
        string $splitSalt = ' & '
    ): bool {
        [$param, $newSignMd5] = self::createSignature(
            $param,
            $salt,
            $timeKey,
            $splitKvp,
            $splitArgs,
            $splitSalt
        );

        return strcmp($oldSignMd5, $newSignMd5) === 0;
    }

    /**
     * Safe base64_encode
     *
     * @param string $target
     *
     * @return string
     */
    public static function safeBase64Encode(string $target): string
    {
        $target = base64_encode($target);
        $target = str_replace(['+', '/', '='], ['-', '_', '.'], $target);

        return $target;
    }

    /**
     * Safe base64_decode
     *
     * @param string $target
     *
     * @return string
     */
    public static function safeBase64Decode(string $target): string
    {
        $target = str_replace(['-', '_', '.'], ['+', '/', '='], $target);
        $target = base64_decode($target);

        return $target;
    }

    /**
     * Transformation image to base64
     *
     * @param string $filePath
     *
     * @return string
     */
    public static function imageToBase64(string $filePath): string
    {
        $image = fread(fopen($filePath, 'r'), filesize($filePath));

        $mime = getimagesize($filePath)['mime'];
        $base64 = chunk_split(base64_encode($image));

        return "data:{$mime};base64,{$base64}";
    }

    /**
     * Save base64 to image
     *
     * @param string $base64
     * @param string $filePath
     *
     * @return mixed
     */
    public static function base64ToImage(string $base64, string $filePath)
    {
        $base64 = preg_replace('/^(data:\s*image\/(\w+);base64,)/', null, $base64);
        $base64 = base64_decode($base64);

        return file_put_contents($filePath, $base64);
    }

    /**
     * Base64 encode for js
     *
     * @param string $content
     *
     * @return string
     */
    public static function base64EncodeForJs(string $content): string
    {
        return base64_encode(rawurlencode($content)); // js: decodeURIComponent(atob('xxx'))
    }

    /**
     * Base64 encode for js （array）
     *
     * @param array $target
     *
     * @return array
     */
    public static function arrayBase64EncodeForJs(array $target): array
    {
        foreach ($target as &$item) {
            if (is_array($item)) {
                $item = self::arrayBase64EncodeForJs($item);
            } elseif (is_string($item)) {
                $item = self::base64EncodeForJs($item);
            }
        }

        return $target;
    }

    /**
     * Cal the size of the thumb
     *
     * @param int $thumbW
     * @param int $thumbH
     * @param int $originalW
     * @param int $originalH
     *
     * @return array
     */
    public static function calThumb(int $thumbW, int $thumbH, int $originalW, int $originalH): array
    {
        $thumbRadio = $thumbW / $thumbH;
        $imgRadio = $originalW / $originalH;

        $left = $top = 0;

        if ($thumbRadio > $imgRadio) {
            $height = $thumbH;
            $width = $originalW * ($thumbH / $originalH);
            $left = ($thumbW - $width) / 2;
        } else {
            $width = $thumbW;
            $height = $originalH * ($thumbW / $originalW);
            $top = ($thumbH - $height) / 2;
        }

        return array_map('intval', compact('width', 'height', 'left', 'top'));
    }

    /**
     * Get the suffix
     *
     * @param string $filename
     * @param bool   $point
     *
     * @return string
     */
    public static function getSuffix(string $filename, bool $point = false): string
    {
        $filename = parse_url($filename, PHP_URL_PATH);
        $suffix = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return $point ? ($suffix ? ".{$suffix}" : '') : $suffix;
    }

    /**
     * Rename file
     *
     * @param string $source
     * @param mixed  $name
     *
     * @return false|string
     */
    public static function renameFile(string $source, $name)
    {
        if (strpos($name, '/') !== false) {
            return rename($source, $name) ? $name : false;
        }

        $item = pathinfo($source);
        $name = is_int($name) ? str_pad($name, 3, 0, STR_PAD_LEFT) : $name;
        $name = "{$item['dirname']}/{$name}" . ($item['extension'] ? ".{$item['extension']}" : '');

        return rename($source, $name) ? $name : false;
    }

    /**
     * Handler comma string to array
     *
     * @param string $string
     * @param bool   $unique
     * @param bool   $filter
     * @param string $handler
     * @param string $separator
     *
     * @return array
     */
    public static function stringToArray(
        string $string,
        bool $unique = true,
        bool $filter = true,
        ?string $handler = 'trim',
        string $separator = ','
    ): array {

        if (($index = strpos(Abs::CHAR_DIST_EN, $separator)) !== -1) {
            $dist = self::split(Abs::CHAR_DIST_CN);
            $string = str_replace($dist[$index], $separator, $string);
        }

        $result = explode($separator, $string);
        $result = $unique ? array_unique($result) : $result;
        $result = $filter ? array_filter($result) : $result;

        if ($handler && function_exists($handler)) {
            return array_map($handler, $result);
        }

        return $result;
    }

    /**
     * String Cn to En
     *
     * @param string $target
     *
     * @return string
     */
    public static function stringCnToEn(string $target): string
    {
        $cn = self::split(Abs::CHAR_DIST_CN);
        $en = self::split(Abs::CHAR_DIST_EN);

        return str_replace($cn, $en, $target);
    }

    /**
     * String En to Cn
     *
     * @param string $target
     *
     * @return string
     */
    public static function stringEnToCn(string $target): string
    {
        $cn = self::split(Abs::CHAR_DIST_CN);
        $en = self::split(Abs::CHAR_DIST_EN);

        return str_replace($en, $cn, $target);
    }

    /**
     * String pad to left to fixed length
     *
     * @param string $target
     * @param int    $length
     * @param string $pad
     *
     * @return string
     */
    public static function strPadLeftLength(string $target, int $length, string $pad = '0'): string
    {
        $target = substr($target, -$length);
        $target = str_pad($target, $length, $pad, STR_PAD_LEFT);

        return $target;
    }

    /**
     * String pad to right to fixed length
     *
     * @param string $target
     * @param int    $length
     * @param string $pad
     *
     * @return string
     */
    public static function strPadRightLength(string $target, int $length, string $pad = '0'): string
    {
        $target = substr($target, -$length);
        $target = str_pad($target, $length, $pad, STR_PAD_RIGHT);

        return $target;
    }

    /**
     * Generate number multiple
     *
     * @param int $begin
     * @param int $end
     * @param int $limit
     *
     * @return array
     */
    public static function generateNumberMultiple(int $begin, int $end, int $limit): array
    {
        $randArr = range($begin, $end);
        shuffle($randArr);

        return array_slice($randArr, 0, $limit);
    }

    /**
     * Create a uuid
     *
     * @param string $hyphen
     *
     * @return string
     */
    public static function gUid($hyphen = null): string
    {
        $id = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = $hyphen ?: chr(45);

        return substr($id, 5, 3)
            . substr($id, 0, 5) . $hyphen
            . substr($id, 10, 2)
            . substr($id, 8, 2) . $hyphen
            . substr($id, 14, 2)
            . substr($id, 12, 2) . $hyphen
            . substr($id, 16, 4) . $hyphen
            . substr($id, 20, 12);
    }

    /**
     * Generate order number
     *
     * @param int $platform
     * @param int $method
     * @param int $uid
     *
     * @return string
     */
    public static function generateOrderNumber(int $platform, int $method, int $uid): string
    {
        return self::strPadLeftLength($platform, 1)
            . self::strPadLeftLength($method, 2)
            . self::strPadLeftLength($uid, 5)
            . self::strPadLeftLength(self::milliTime(true), 3)
            . date('Hd')
            . self::strPadLeftLength(rand(0, 99999), 5);
    }

    /**
     * Generate unique
     *
     * @param string $custom
     *
     * @return string
     */
    public static function generateUnique(string $custom = null): string
    {
        return str_replace('.', null, uniqid(getmypid() . $custom . mt_rand(), true));
    }

    /**
     * Generate ticket - digit 18
     *
     * @param string $channel
     * @param int    $uid
     * @param string $custom
     *
     * @return string
     */
    public static function generateTicket(string $channel, int $uid, string $custom = null): string
    {
        $uuid = self::generateUnique($custom);

        return self::strPadLeftLength($channel, 2)
            . substr($uuid, -5)
            . self::strPadLeftLength(strrev($uid), 7)
            . substr($uuid, 1, 4);
    }

    /**
     * Generate token
     *
     * @param int    $fromBase
     * @param int    $toBase
     * @param string $custom
     *
     * @return string
     */
    public static function generateToken(int $fromBase = 18, int $toBase = 36, string $custom = null): string
    {
        return base_convert(self::generateUnique($custom), $fromBase, $toBase);
    }

    /**
     * Generate token fixed digit
     *
     * @param int    $length
     * @param string $custom
     *
     * @return string
     */
    public static function generateFixedToken(int $length = 18, string $custom = null): string
    {
        $token = strrev(self::generateToken(18, 36, $custom));
        $token = substr($token, 0, $length);

        return $token;
    }

    /**
     * Generate token multiple
     *
     * @param int    $digit
     * @param int    $total
     * @param string $custom
     *
     * @return array
     */
    public static function generateTokenMultiple(int $digit, int $total, string $custom = null): array
    {
        $count = 0;
        $box = [];

        if ($digit > 26) {
            return $box;
        }

        while ($count < $total) {
            $code = self::generateToken(18, 36, $custom);
            $code = strtoupper(substr($code, 0, $digit));

            if (strlen($code) == $digit && !isset($box[$code])) {
                $box[$code] = true;
                $count++;
            }
        }

        return array_keys($box);
    }

    /**
     * Generates an unique access token.
     *
     * @param int $length
     *
     * @return string
     * @throws
     */
    public static function generateAccessToken(int $length = 40): string
    {
        $half = $length / 2;

        if (function_exists('random_bytes')) {
            $randomData = random_bytes($half);
            if ($randomData !== false && strlen($randomData) === $half) {
                return bin2hex($randomData);
            }
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $randomData = openssl_random_pseudo_bytes($half);
            if ($randomData !== false && strlen($randomData) === $half) {
                return bin2hex($randomData);
            }
        }

        // Last resort which you probably should just get rid of:
        $randomData = null
            . mt_rand()
            . microtime(true)
            . mt_rand()
            . uniqid(mt_rand(), true)
            . mt_rand();

        return substr(hash('sha512', $randomData), 0, $length);
    }

    /**
     * Handler first and last
     *
     * @param array  $target
     * @param string $firstKey
     * @param string $lastKey
     */
    public static function sendToBothEnds(array &$target, string $firstKey = null, string $lastKey = null)
    {
        if (!empty($firstKey) && isset($target[$firstKey])) {
            $first = $target[$firstKey] ?? null;
            unset($target[$firstKey]);
            $target = array_merge([$firstKey => $first], $target);
        }

        if (!empty($lastKey) && isset($target[$lastKey])) {
            $last = $target[$lastKey] ?? null;
            unset($target[$lastKey]);
            $target = array_merge($target, [$lastKey => $last]);
        }
    }

    /**
     * Multiple to one-dimensional
     *
     * @param array $items
     *
     * @return array
     */
    public static function multipleToOne(array $items): array
    {
        $arr = [];
        foreach ($items as $key => $val) {
            if (is_array($val)) {
                $arr = array_merge($arr, self::multipleToOne($val));
            } else {
                $arr[] = $val;
            }
        }

        return $arr;
    }

    /**
     * Is date string
     *
     * @param string $date
     *
     * @return bool
     */
    public static function isDateString(string $date): bool
    {
        return strtotime($date) !== false;
    }

    /**
     * Is int numeric
     *
     * @param mixed $num
     *
     * @return bool
     */
    public static function isIntNumeric($num): bool
    {
        return is_numeric($num) && intval($num) == $num;
    }

    /**
     * Is float numeric
     *
     * @param mixed $num
     *
     * @return bool
     */
    public static function isFloatNumeric($num): bool
    {
        return is_numeric($num) && !self::isIntNumeric($num);
    }

    /**
     * Numeric value
     *
     * @param mixed $num
     * @param mixed $default
     *
     * @return float|int
     */
    public static function numericValue($num, $default = null)
    {
        if (!is_numeric($num)) {
            return $default ?? $num;
        }

        if (self::isIntNumeric($num)) {
            return intval($num);
        }

        if (self::isNumberBetween($num, PHP_INT_MIN, PHP_INT_MAX)) {
            return floatval($num);
        }

        return $num;
    }

    /**
     * Numeric values
     *
     * @param array $params
     *
     * @return array
     */
    public static function numericValues(array $params): array
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $params[$key] = self::numericValues($value);
            } else {
                $params[$key] = self::numericValue($value);
            }
        }

        return $params;
    }

    /**
     * String values
     *
     * @param array $params
     *
     * @return array
     */
    public static function stringValues(array $params): array
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $params[$key] = self::stringValues($value);
            } elseif (is_numeric($value)) {
                $params[$key] = strval($value);
            }
        }

        return $params;
    }

    /**
     * Url encode values
     *
     * @param array $params
     *
     * @return array
     */
    public static function urlEncodeValues(array $params): array
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $params[$key] = self::urlEncodeValues($value);
            } elseif (is_string($value)) {
                $params[$key] = rawurlencode($value);
            }
        }

        return $params;
    }

    /**
     * Url decode values
     *
     * @param array $params
     *
     * @return array
     */
    public static function urlDecodeValues(array $params): array
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $params[$key] = self::urlDecodeValues($value);
            } elseif (is_string($value)) {
                $params[$key] = rawurldecode($value);
            }
        }

        return $params;
    }

    /**
     * Is mobile
     *
     * @return bool
     */
    public static function isMobile(): bool
    {
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = 0;

        if (preg_match(
            '/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i',
            strtolower($_SERVER['HTTP_USER_AGENT'] ?? null)
        )) {
            $mobile_browser++;
        }

        if (
            (isset($_SERVER['HTTP_ACCEPT'])) and
            (strpos(strtolower($_SERVER['HTTP_ACCEPT'] ?? null), 'application/vnd.wap.xhtml+xml') !== false)
        ) {
            $mobile_browser++;
        }

        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            $mobile_browser++;
        }

        if (isset($_SERVER['HTTP_PROFILE'])) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['ALL_HTTP'] ?? null), 'operamini') !== false) {
            $mobile_browser++;
        }

        // Pre-final check to reset everything if the user is on Windows
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT'] ?? null), 'windows') !== false) {
            $mobile_browser = 0;
        }

        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT'] ?? null), 'windows phone') !== false) {
            $mobile_browser++;
        }

        return $mobile_browser > 0;
    }

    /**
     * Add tag for table field
     *
     * @param string $field
     * @param string $dbTableSplit
     * @param string $tag
     *
     * @return string
     */
    public static function tableFieldAddTag(string $field, string $dbTableSplit = '.', string $tag = '`'): string
    {
        if (false !== strpos($field, $tag)) {
            return $field;
        }

        if (false !== strpos($field, $dbTableSplit)) {
            $field = str_replace($dbTableSplit, "{$tag}{$dbTableSplit}{$tag}", $field);
        }

        return "{$tag}{$field}{$tag}";
    }

    /**
     * Add alias for table field
     *
     * @param string $field
     * @param string $alias
     * @param string $dbTableSplit
     *
     * @return string
     */
    public static function tableFieldAddAlias(string $field, string $alias, string $dbTableSplit = '.'): string
    {
        $field = trim($field, $dbTableSplit);
        if (strpos($field, $dbTableSplit) === false) {
            return "{$alias}{$dbTableSplit}{$field}";
        }

        return $field;
    }

    /**
     * Remove alias for table field
     *
     * @param string $field
     * @param string $asKeyWord
     * @param string $dbTableSplit
     * @param string $tag
     *
     * @return string
     */
    public static function tableFieldDelAlias(
        string $field,
        string $asKeyWord = ' AS ',
        string $dbTableSplit = '.',
        string $tag = '`'
    ): string {

        $field = str_replace($tag, null, $field);
        if (false !== strpos($field, $dbTableSplit)) {
            $field = explode($dbTableSplit, $field)[1];
        }

        if (false !== strpos($field, $asKeyWord)) {
            $field = explode($asKeyWord, $field)[1];
        }

        $asKeyWord = strtolower($asKeyWord);
        if (false !== strpos($field, $asKeyWord)) {
            $field = explode($asKeyWord, $field)[1];
        }

        return trim($field);
    }

    /**
     * Table name to alias
     *
     * @param string $table
     *
     * @return string
     */
    public static function tableNameToAlias(string $table): string
    {
        $table = self::clsName($table);
        $words = explode('_', self::camelToUnder($table));

        $alias = '';
        foreach ($words as $word) {
            $alias .= $word[0];
        }

        return $alias;
    }

    /**
     * Table name from class
     *
     * @param string $table
     *
     * @return string|null
     */
    public static function tableNameFromCls(string $table): string
    {
        return self::camelToUnder(self::clsName($table));
    }

    /**
     * Get alias from field
     *
     * @param string $field
     * @param string $dbTableSplit
     *
     * @return string|null
     */
    public static function getAliasFromField(string $field, string $dbTableSplit = '.'): ?string
    {
        $item = explode($dbTableSplit, $field);
        if (count($item) != 2) {
            return null;
        }

        return current($item);
    }

    /**
     * Get alias and field from field
     *
     * @param string $field
     * @param string $dbTableSplit
     *
     * @return array
     */
    public static function getAliasAndFieldFromField(string $field, string $dbTableSplit = '.'): array
    {
        $item = explode($dbTableSplit, $field);
        if (count($item) != 2) {
            return [null, current($item)];
        }

        return [current($item), last($item)];
    }

    /**
     * Table prefix handler
     *
     * @param string $prefix
     * @param bool   $under
     * @param string $split
     *
     * @return string
     */
    public static function tablePrefixHandler(string $prefix, bool $under = true, string $split = '_'): string
    {
        $prefix = rtrim($prefix, $split);
        $prefix = self::camelToUnder($prefix) . $split;

        return $under ? $prefix : self::underToCamel($prefix);
    }

    /**
     * Database/Table name prefix handler
     *
     * @param string $scheme
     * @param string $prefix
     * @param bool   $removeMode
     *
     * @return string
     */
    public static function schemeNamePrefixHandler(string $scheme, string $prefix, bool $removeMode = false): string
    {
        $scheme = self::camelToUnder($scheme);
        $prefix = self::tablePrefixHandler($prefix);
        $has = strpos($scheme, $prefix) === 0;

        if ($has && $removeMode) {
            $scheme = self::strReplaceOnce($prefix, null, $scheme);
        }

        if (!$has && !$removeMode) {
            $scheme = "{$prefix}{$scheme}";
        }

        return $scheme;
    }

    /**
     * Millisecond
     *
     * @param bool $decimal
     *
     * @return int
     */
    public static function milliTime($decimal = false): int
    {
        $milli = intval(microtime(true) * 1000);

        return $decimal ? $milli % 1000 : $milli;
    }

    /**
     * Microsecond
     *
     * @param bool $decimal
     *
     * @return int
     */
    public static function microTime($decimal = false): int
    {
        [$micro, $second] = explode(self::enSpace(), microtime());
        $micro *= 1000000;

        if ($decimal) {
            return $micro;
        }

        return intval("{$second}{$micro}");
    }

    /**
     * Log runtime cost
     *
     * @param string $scene
     * @param string $tpl
     * @param string $scenePre
     * @param bool   $process
     *
     * @return array
     */
    public static function cost(
        string $scene,
        string $tpl = null,
        string $scenePre = null,
        bool $process = false
    ): array {

        static $sceneFirst;
        static $scenePrevious = 'init';
        static $costHistory = [];

        if (!isset($sceneFirst)) {
            $sceneFirst = $scene;
        }

        $costCurrent = $costHistory[$scene] = self::milliTime();
        $costFirst = $sceneFirst ? $costHistory[$sceneFirst] : $costCurrent;

        $scenePre = $scenePre ?? $scenePrevious;
        $costPrevious = $costHistory[$scenePre] ?? $costCurrent;
        $scenePrevious = $scene;

        $costMilli = $costCurrent - $costFirst;
        $chunkCostMilli = $costCurrent - $costPrevious;
        $chunkCostString = chunk_split($costCurrent, 10, '.');

        $process = $process ? 'between ({scene_prev}, {scene})' : 'at {scene}';
        $tpl = $tpl ?: "-->> {second} (cost: {cost}) {$process}";

        $tpl = str_replace(
            ['{scene_prev}', '{scene}', '{second}', '{cost}'],
            [$scenePre, $scene, $chunkCostString, $chunkCostMilli],
            $tpl
        );

        return [$tpl, $costMilli];
    }

    /**
     * Get the micro time
     *
     * @param string $format
     * @param float  $timestamp
     *
     * @return false|string
     */
    public static function date($format = Abs::FMT_MIC, $timestamp = null)
    {
        if (is_null($timestamp)) {
            $timestamp = microtime(true);
        }

        $time = floor($timestamp);
        $micro = round(($timestamp - $time) * 1000);
        $micro = str_pad($micro, 3, 0, STR_PAD_LEFT);

        $format = str_replace('u', $micro, $format);

        return date($format, $time);
    }

    /**
     * Get time range of minute
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampMinute($date = null): array
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_MINUTES . ':00', $timestamp);
        $begin = strtotime($date);

        return [
            $begin,
            $begin + Abs::TIME_MINUTE - 1,
        ];
    }

    /**
     * Get time range of hour
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampHour($date = null): array
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_HOUR . ':00:00', $timestamp);
        $begin = strtotime($date);

        return [
            $begin,
            $begin + Abs::TIME_HOUR - 1,
        ];
    }

    /**
     * Get time range of day
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampDay($date = null): array
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_DAY, $timestamp);
        $begin = strtotime($date);

        return [
            $begin,
            $begin + Abs::TIME_DAY - 1,
        ];
    }

    /**
     * Get date range of week
     *
     * @param string $date
     *
     * @return array
     */
    public static function dateWeek($date = null): array
    {
        $timestamp = $date ? strtotime($date) : time();
        $w = date('w', $timestamp);

        return [
            self::dateDayDiff($w ? -$w + 1 : -6, $date),
            self::dateDayDiff($w ? -$w + 7 : 0, $date),
        ];
    }

    /**
     * Get time range of week
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampWeek($date = null): array
    {
        [$head, $tail] = self::dateWeek($date);

        return [
            strtotime($head . Abs::_DAY_BEGIN),
            strtotime($tail . Abs::_DAY_END),
        ];
    }

    /**
     * Get date range of month
     *
     * @param string $date
     * @param string $split
     *
     * @return array
     */
    public static function dateMonth($date = null, string $split = '-'): array
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_MONTH_LAST_DAY, $timestamp);
        [$Y, $m, $t] = explode($split, $date);

        return [
            "{$Y}-{$m}-01",
            "{$Y}-{$m}-{$t}",
        ];
    }

    /**
     * Get time range of month
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampMonth($date = null): array
    {
        [$head, $tail] = self::dateMonth($date);

        return [
            strtotime($head . Abs::_DAY_BEGIN),
            strtotime($tail . Abs::_DAY_END),
        ];
    }

    /**
     * Get date range of quarter
     *
     * @param string $date
     * @param string $split
     *
     * @return array
     */
    public static function dateQuarter($date = null, string $split = '-'): array
    {
        $timestamp = $date ? strtotime($date) : time();
        $date = date(Abs::FMT_MONTH, $timestamp);
        [$Y] = explode($split, $date);

        $season = ceil((date('n', strtotime($date))) / 3);

        $headMonth = ($season - 1) * 3 + 1;
        $tailMonth = $headMonth + 2;
        $tailDay = date('t', strtotime("{$Y}-{$tailMonth}-01"));

        return [
            "{$Y}-{$headMonth}-01",
            "{$Y}-{$tailMonth}-{$tailDay}",
        ];
    }

    /**
     * Get time range of quarter
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampQuarter($date = null): array
    {
        [$head, $tail] = self::dateQuarter($date);

        return [
            strtotime($head . Abs::_DAY_BEGIN),
            strtotime($tail . Abs::_DAY_END),
        ];
    }

    /**
     * Get date range of year
     *
     * @param string $date
     *
     * @return array
     */
    public static function dateYear($date = null): array
    {
        $timestamp = $date ? strtotime($date) : time();
        $Y = date(Abs::FMT_YEAR_ONLY, $timestamp);

        return [
            "{$Y}-01-01",
            "{$Y}-12-31",
        ];
    }

    /**
     * Get time range of year
     *
     * @param string $date
     *
     * @return array
     */
    public static function timestampYear($date = null): array
    {
        [$head, $tail] = self::dateYear($date);

        return [
            strtotime($head . Abs::_DAY_BEGIN),
            strtotime($tail . Abs::_DAY_END),
        ];
    }

    /**
     * Get date day difference
     *
     * @param int    $n
     * @param string $date
     * @param string $format
     *
     * @return string
     */
    public static function dateDayDiff(int $n, ?string $date = null, string $format = Abs::FMT_DAY): string
    {
        $n = ($n > 0 ? "+{$n}" : $n);
        $timestamp = $date ? strtotime($date) : time();

        return date($format, strtotime("{$n} days", $timestamp));
    }

    /**
     * Get date range before N days
     *
     * @param int    $n
     * @param string $date
     * @param string $format
     *
     * @return array
     */
    public static function dateDayDiffN(int $n, ?string $date = null, string $format = Abs::FMT_DAY): array
    {
        $n = ($n > 0 ? "+{$n}" : $n);
        $timestamp = $date ? strtotime($date) : time();

        $from = date($format, strtotime("{$n} days", $timestamp));
        $to = date($format, $timestamp);

        return $n > 0 ? [$to, $from] : [$from, $to];
    }

    /**
     * Boundary datetime
     *
     * @param string ...$datetime
     *
     * @return array
     */
    public static function boundaryDateTime(string ...$datetime): array
    {
        $datetime = array_map('strtotime', $datetime);
        asort($datetime);

        $min = date(Abs::FMT_FULL, current($datetime));
        $max = date(Abs::FMT_FULL, end($datetime));

        return [$min, $max];
    }

    /**
     * Compare datetime
     *
     * @param string $left
     * @param string $right
     *
     * @return int
     */
    public static function compareDateTime(string $left, string $right): int
    {
        $left = strtotime($left);
        $right = strtotime($right);

        return $left <=> $right;
    }

    /**
     * Gap second datetime
     *
     * @param string $leftDate
     * @param string $rightDate
     *
     * @return int
     */
    public static function gapDateTime(string $leftDate, string $rightDate): int
    {
        $left = strtotime($leftDate);
        $right = strtotime($rightDate);

        return abs($left - $right);
    }

    /**
     * Get date gap detail
     *
     * @param string $date
     * @param array  $digit
     * @param string $standardDate
     *
     * @return array
     */
    public static function gapDateDetail(string $date, ?array $digit = null, string $standardDate = null): array
    {
        $standardDate = $standardDate ?? date(Abs::FMT_FULL);
        $standardTime = strtotime($standardDate);
        $time = strtotime($date);

        $compare = $time <=> $standardTime;
        $gap = abs($time - $standardTime);

        [$year, $gap] = self::getIntDivAndMod($gap, Abs::TIME_YEAR);
        [$month, $gap] = self::getIntDivAndMod($gap, Abs::TIME_MONTH);
        [$day, $gap] = self::getIntDivAndMod($gap, Abs::TIME_DAY);
        [$hour, $gap] = self::getIntDivAndMod($gap, Abs::TIME_HOUR);
        [$minute, $second] = self::getIntDivAndMod($gap, Abs::TIME_MINUTE);

        if (is_null($digit)) {
            return [$compare, $year, $month, $day, $hour, $minute, $second];
        }

        $digit = array_merge(
            [
                'year'   => 'year',
                'month'  => 'month',
                'day'    => 'day',
                'hour'   => 'hour',
                'minute' => 'minute',
                'second' => 'second',
            ],
            $digit
        );

        $info = null;
        foreach ($digit as $key => $value) {
            if (!empty($$key) && !empty($value)) {
                $info .= "{$$key}{$value}";
            }
        }

        if (empty($info)) {
            $info = "0{$digit['second']}";
        }

        return [$compare, $info];
    }

    /**
     * Datetime with only hour/minute/second
     *
     * @param string $time
     *
     * @return false|int
     */
    public static function datetime(string $time)
    {
        if (!self::isDateString($time)) {
            return false;
        }

        $time = strtotime($time);
        $time = $time - current(self::timestampDay());

        return $time;
    }

    /**
     * Year and week to date
     *
     * @param int    $year
     * @param int    $week
     * @param string $format
     *
     * @return array
     * @throws
     */
    public static function yearWeekToDate(int $year, int $week, string $format = Abs::FMT_DAY): array
    {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $date[] = $dto->format($format);
        $date[] = $dto->modify('+6 days')->format($format);

        return $date;
    }

    /**
     * Get int div and mod
     *
     * @param int  $dividend
     * @param int  $divisor
     * @param bool $leftPadZero
     *
     * @return array
     */
    public static function getIntDivAndMod(int $dividend, int $divisor, bool $leftPadZero = false): array
    {
        $div = intdiv($dividend, $divisor);
        $mod = $dividend % $divisor;

        if ($leftPadZero) {
            $mod = self::strPadLeftLength($mod, 2);
        }

        return [$div, $mod];
    }

    /**
     * Check type for callable
     *
     * @param mixed  $data
     * @param mixed  $type
     * @param string $info
     *
     * @return void
     * @throws
     */
    public static function callReturnType($data, $type, string $info = null)
    {
        $type = (array)$type;
        $info = $info ?: 'the callback';
        $dataType = is_object($data) ? get_class($data) : strtolower(gettype($data));

        foreach ($type as $allowType) {
            if ($data === $allowType) {
                return;
            }

            if ($dataType === $allowType) {
                return;
            }

            if (self::extendClass($dataType, $allowType, true)) {
                return;
            }
        }

        $type = implode(' | ', $type);
        throw new BadFunctionCallException("{$info} should return `{$type}` but got `{$dataType}`");
    }

    /**
     * Check type for class
     *
     * @param object $object
     * @param string $class
     * @param string $info
     *
     * @return void
     * @throws
     */
    public static function objectInstanceOf($object, string $class, string $info = null)
    {
        $info = $info ?: 'the class';

        if (!is_object($object)) {
            $type = gettype($object);
            throw new BadFunctionCallException("{$info} should be instance of `{$class}` but got `{$type}`");
        }

        if (!$object instanceof $class) {
            $nowClass = get_class($object);
            throw new BadFunctionCallException("{$info} should be instance of `{$class}` but got `{$nowClass}`");
        }
    }

    /**
     * Is extend class
     *
     * @param mixed  $target
     * @param string $clsName
     * @param bool   $allowSelf
     *
     * @return bool
     */
    public static function extendClass($target, string $clsName, bool $allowSelf = false): bool
    {
        $subClsName = is_object($target) ? get_class($target) : $target;

        if (!class_exists($subClsName)) {
            return false;
        }

        if ($allowSelf && $subClsName == $clsName) {
            return true;
        }

        if (is_subclass_of($subClsName, $clsName)) {
            return true;
        }

        return false;
    }

    /**
     * Perfect date key for array
     *
     * @param array  $list
     * @param string $from
     * @param string $to
     * @param mixed  $default
     * @param string $format
     * @param int    $step
     * @param bool   $sort
     *
     * @return array
     * @throws
     */
    public static function perfectDateKeys(
        array $list,
        string $from,
        string $to,
        $default = 0,
        string $format = Abs::FMT_DAY,
        int $step = Abs::TIME_DAY,
        bool $sort = true
    ): array {

        if (!($from = strtotime($from)) || !($to = strtotime($to))) {
            throw new InvalidArgumentException('Param `from` and `to` must be date string');
        }

        if ($from >= $to) {
            [$from, $to] = [$to, $from];
        }

        $dayTime = $from;
        while ($dayTime <= $to) {
            $day = date($format, $dayTime);
            if (!isset($list[$day])) {
                $list[$day] = $default;
            }
            $dayTime += $step;
        }

        $sort && ksort($list);

        return $list;
    }

    /**
     * Perfect int key for array
     *
     * @param array $list
     * @param int   $from
     * @param int   $to
     * @param mixed $default
     * @param int   $step
     * @param bool  $sort
     *
     * @return array
     * @throws
     */
    public static function perfectIntKeys(
        array $list,
        int $from,
        int $to,
        $default = 0,
        int $step = 1,
        bool $sort = true
    ): array {

        if ($from >= $to) {
            throw new InvalidArgumentException('Param `from` must less than `to`');
        }

        $counter = $from;
        while ($counter <= $to) {
            if (!isset($list[$counter])) {
                $list[$counter] = $default;
            }
            $counter += $step;
        }

        $sort && ksort($list);

        return $list;
    }

    /**
     * Recursion cut string
     *
     * @param string $string
     * @param array  $rules
     * @param string $expressionSplit
     *
     * @return string
     * @example :
     *          string: $url = 'http://www.w3school.com.cn/php/func_array_slice.asp'
     *          one: get the `func`
     *          $result = Helper::cutString($url, ['/^-1', '_^0']);
     *          two: get the `asp`
     *          $result = Helper::cutString($url, '.^-1');
     */
    public static function cutString(string $string, array $rules, string $expressionSplit = '^'): string
    {
        foreach ($rules as $rule) {
            if (empty($rule)) {
                continue;
            }

            [$split, $index] = explode($expressionSplit, $rule) + [1 => 0];
            $stringArr = explode($split, $string);

            $string = array_slice($stringArr, $index, 1);
            $string = current($string);

            if ($string === false) {
                break;
            }
        }

        return $string;
    }

    /**
     * Return array latest item
     *
     * @param string|array $target
     * @param string       $split
     *
     * @return mixed
     */
    public static function arrayLatestItem($target, string $split = '_')
    {
        if (is_scalar($target)) {
            $target = explode($split, $target);
        }

        return $target[count($target) - 1];
    }

    /**
     * Trim pos when begin
     *
     * @param string $content
     * @param string $pos
     *
     * @return bool|string
     */
    public static function strPosBeginTrim(string $content, string $pos)
    {
        if (strpos($content, $pos) !== 0) {
            return false;
        }

        return substr($content, self::strLen($pos));
    }

    /**
     * Replace pos when begin
     *
     * @param array $contentMap
     * @param array $posMap
     *
     * @return array
     */
    public static function strPosBeginReplace(array $contentMap, array $posMap): array
    {
        foreach ($contentMap as &$content) {
            foreach ($posMap as $pos => $prefix) {
                if (strpos($content, $pos) === 0) {
                    $content = sprintf($prefix, substr($content, self::strLen($pos)));
                    break;
                }
            }
        }

        return $contentMap;
    }

    /**
     * Get color value
     *
     * @param string $label
     * @param bool   $well
     *
     * @return string
     */
    public static function colorValue(?string $label = null, bool $well = false): string
    {
        $colors = [];
        if ($label) {
            $colors = self::split(substr(md5($label), 4, 6));
        } else {
            for ($i = 0; $i < 6; $i++) {
                $colors[] = dechex(rand(0, 15));
            }
        }

        $well = $well ? '#' : null;
        $color = implode('', $colors);
        if ($color == 'ffffff') {
            $color = '666666';
        }

        return "{$well}{$color}";
    }

    /**
     * Split the string with nil
     *
     * @param string $string
     *
     * @return array
     */
    public static function split(string $string): array
    {
        // if only utf-8 use str_split best.
        preg_match_all('/[\s\S]/u', $string, $array);

        return $array[0];
    }

    /**
     * Reverse string with chinese
     *
     * @param string $string
     *
     * @return string
     */
    public static function strReverse(string $string): string
    {
        return implode('', array_reverse(self::split($string)));
    }

    /**
     * Filter the special char
     *
     * @param string       $string
     * @param string       $replace
     * @param string|array $specialChar
     *
     * @return string
     */
    public static function filterSpecialChar(string $string, ?string $replace = null, $specialChar = null): string
    {
        $specialChar = $specialChar ?: self::special4char();

        if (!is_array($specialChar)) {
            $specialChar = self::split($specialChar);
        }

        foreach ($specialChar as $char) {
            $string = str_replace($char, $replace, $string);
        }

        return $string;
    }

    /**
     * Sort for two-dimensional
     *
     * @param array  $target
     * @param mixed  $key
     * @param string $mode
     *
     * @return array
     */
    public static function sortArray(array $target, $key, string $mode = Abs::SORT_ASC): array
    {
        $keysValue = $newArray = [];
        foreach ($target as $k => $v) {
            $keysValue[$k] = $v[$key];
        }

        switch (ucwords($mode)) {
            case Abs::SORT_ASC :
                asort($keysValue);
                break;

            default :
                arsort($keysValue);
                break;
        }

        reset($keysValue);
        foreach ($keysValue as $k => $v) {
            $newArray[$k] = $target[$k];
        }

        return $newArray;
    }

    /**
     * Sort array with count items
     *
     * @param array  $target
     * @param string $mode
     *
     * @return array
     */
    public static function sortArrayWithCount(array $target, string $mode = Abs::SORT_DESC): array
    {
        $assist = [];
        foreach ($target as $key => $item) {
            if (!is_array($item)) {
                return $target;
            }
            $assist[count($item)] = $key;
        }

        $newTarget = [];
        $mode = strtoupper($mode);
        $mode === Abs::SORT_DESC ? krsort($assist) : ksort($assist);

        foreach ($assist as $key) {
            $newTarget[$key] = $target[$key];
        }

        return $newTarget;
    }

    /**
     * Natural sort for array keys
     *
     * @param array $target
     *
     * @return array
     */
    public static function keyNaturalSort(array $target): array
    {
        $keys = array_keys($target);
        natsort($keys);

        return self::arrayPull($target, $keys);
    }

    /**
     * Sort string array with handler
     *
     * @param array    $target
     * @param callable $handler
     * @param string   $mode
     *
     * @return array
     */
    public static function sortStringArrayWithHandler(
        array $target,
        callable $handler,
        string $mode = Abs::SORT_ASC
    ): array {

        $sortSource = [];
        foreach ($target as $key => $value) {
            $source = $handler($value, $key);
            if ($source === false || $source === null) {
                continue;
            }
            $sortSource[$source] = $key;
        }
        $mode === Abs::SORT_DESC ? krsort($sortSource) : ksort($sortSource);

        $newTarget = [];
        foreach ($sortSource as $source => $key) {
            $newTarget[$key] = $target[$key];
        }

        return $newTarget;
    }

    /**
     * Appoint keys from array
     *
     * @param array $target
     * @param null  $appoint
     *
     * @return array|string|null
     */
    public static function arrayAppoint(array $target, $appoint = null)
    {
        if (is_null($appoint)) {
            return $target;
        }

        if (is_array($appoint)) {
            return self::arrayPull($target, $appoint);
        }

        return $target[$appoint] ?? null;
    }

    /**
     * Substring
     *
     * @param string $str
     * @param int    $start
     * @param int    $length
     * @param string $charset
     * @param string $suffix
     *
     * @return string
     */
    public static function mSubStr(
        string $str,
        int $start,
        int $length,
        string $charset = 'utf-8',
        string $suffix = '..'
    ): string {

        if (function_exists('mb_substr')) {
            $slice = mb_substr($str, $start, $length, $charset);
        } else {
            if (function_exists('iconv_substr')) {
                $slice = iconv_substr($str, $start, $length, $charset);
                if (false === $slice) {
                    $slice = null;
                }
            } else {
                $re['utf-8'] = '/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/';
                $re['gb2312'] = '/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/';
                $re['gbk'] = '/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/';
                $re['big5'] = '/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/';
                preg_match_all($re[$charset], $str, $match);
                $slice = join(null, array_slice($match[0], $start, $length));
            }
        }

        return !empty($suffix) ? $slice . $suffix : $slice;
    }

    /**
     * Get the rand string
     *
     * @param int    $len
     * @param string $type alphabet/number/upper/upper-number/lower/lower-number/mixed/captcha
     * @param string $addChars
     *
     * @return string
     */
    public static function randString(int $len = 6, string $type = 'captcha', string $addChars = null): string
    {
        $str = null;
        switch ($type) {
            case 'alphabet' :
                $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz{$addChars}";
                break;
            case 'number' :
                $chars = str_repeat('0123456789', 3);
                break;
            case 'upper' :
                $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ{$addChars}";
                break;
            case 'upper-number' :
                $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789{$addChars}";
                break;
            case 'lower' :
                $chars = "abcdefghijklmnopqrstuvwxyz{$addChars}";
                break;
            case 'lower-number' :
                $chars = "abcdefghijklmnopqrstuvwxyz0123456789{$addChars}";
                break;
            case 'mixed' :
                $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789{$addChars}";
                break;
            default :
                // Remove alphabet `OLl` and number `01`
                $chars = "ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789{$addChars}";
                break;
        }

        if ($len > 10) {
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }

        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str = substr($chars, 0, $len);
        } else {
            for ($i = 0; $i < $len; $i++) {
                $str .= self::mSubStr($chars, floor(mt_rand(0, self::strLen($chars) - 1)), 1, 'utf-8', false);
            }
        }

        return $str;
    }


    /**
     * Get the rand string of readability
     *
     * @access public
     *
     * @param $length
     *
     * @return string
     */
    public static function readability($length)
    {
        $string = null;
        $vocal = explode(' ', 'a e i o u');
        $consonant = explode(' ', 'b c d f g h j k l m n p r s t v w x y z');

        srand((double)microtime() * 1000000);
        $max = $length / 2;

        for ($i = 1; $i <= $max; $i++) {
            $string .= $consonant[rand(0, 19)];
            $string .= $vocal[rand(0, 4)];
        }

        return $string;
    }

    /**
     * Is we chat browser
     *
     * @return bool
     */
    public static function weChatBrowser(): bool
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

    /**
     * Is ali pay browser
     *
     * @return bool
     */
    public static function aliPayBrowser(): bool
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        return strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false;
    }

    /**
     * Array to json string
     *
     * @param array|object $target
     * @param string       $default
     * @param int          $jsonFlag
     *
     * @return string
     */
    public static function jsonStringify($target, string $default = '', int $jsonFlag = 0): string
    {
        return json_encode($target, JSON_UNESCAPED_UNICODE | $jsonFlag) ?: $default;
    }

    /**
     * Array to flexible json string
     *
     * @param array|object $target
     *
     * @return string
     */
    public static function jsonFlexible($target): string
    {
        $default = '{}';

        if (empty($target)) {
            return $default;
        }

        return self::jsonStringify($target, $default);
    }

    /**
     * Array to strict json string
     *
     * @param array|object $target
     *
     * @return string
     */
    public static function jsonStrict($target): string
    {
        return self::jsonStringify($target, '{}', JSON_FORCE_OBJECT);
    }

    /**
     * Array to json string and base64 encode
     *
     * @param array  $target
     * @param string $default
     *
     * @return string
     */
    public static function jsonStringify64(array $target, string $default = ''): string
    {
        return base64_encode(self::jsonStringify($target, $default));
    }

    /**
     * Json string to array
     *
     * @param string $json
     *
     * @return array
     */
    public static function jsonArray(string $json): array
    {
        $json = json_decode($json, true);

        return is_array($json) ? $json : [];
    }

    /**
     * Json string to array after base64 decode
     *
     * @param string $json
     *
     * @return array
     */
    public static function jsonArray64(string $json): array
    {
        return self::parseJsonString(base64_decode($json), []);
    }

    /**
     * Object to array
     *
     * @param mixed $obj
     *
     * @return array
     */
    public static function objectToArray($obj): array
    {
        return self::jsonArray(self::jsonStringify($obj));
    }

    /**
     * Array to object
     *
     * @param $target
     *
     * @return mixed
     */
    public static function arrayToObject(array $target)
    {
        return json_decode(self::jsonStringify($target));
    }

    /**
     * Object to array (only public and protected)
     *
     * @param mixed $entity
     *
     * @return array
     */
    public static function entityToArray($entity)
    {
        $attributes = [];
        foreach ((array)$entity as $key => $value) {
            $attributes[ltrim($key, Abs::ENTITY_KEY_TRIM)] = $value;
        }

        return $attributes;
    }

    /**
     * Get items by keys from array or object
     *
     * @param mixed        $target
     * @param array|string $keys
     *
     * @return array|string
     */
    public static function getItems($target, $keys)
    {
        if (is_object($target)) {
            $target = self::entityToArray($target);
        }

        if (!is_array($target)) {
            return null;
        }

        if (is_scalar($keys)) {
            return $target[$keys] ?? null;
        }

        return self::arrayPull($target, $keys);
    }

    /**
     * Set items to array or object
     *
     * @param mixed $target
     * @param array $source
     *
     * @return mixed
     */
    public static function setItems($target, array $source)
    {
        if (is_array($target)) {
            return array_merge($target, $source);
        }

        if (!is_object($target)) {
            return $target;
        }

        foreach ($source as $key => $val) {
            $target->{$key} = $val;
        }

        return $target;
    }

    /**
     * Unique for more-dimensional
     *
     * @param array $target
     *
     * @return array
     */
    public static function moreDimensionArrayUnique(array $target): array
    {
        $target = array_map('serialize', $target);
        $target = array_unique($target);
        $target = array_map('unserialize', $target);

        return $target;
    }

    /**
     * Array search filter
     *
     * @param array  $target
     * @param string $keyword
     * @param bool   $searchKey
     * @param bool   $searchValue
     *
     * @return array
     */
    public static function arraySearchFilter(
        array $target,
        string $keyword,
        bool $searchKey = false,
        $searchValue = true
    ): array {

        if (!$searchKey && !$searchValue) {
            return $target;
        }

        foreach ($target as $key => $item) {
            $target[$key][Abs::FLAG_SEARCH_ALL] = serialize($item);
        }

        if ($searchValue === true) {
            $searchValue = Abs::FLAG_SEARCH_ALL;
        }

        foreach ($target as $key => $item) {
            $tag = 0;
            if ($searchKey && strpos($key, $keyword) === false) {
                $tag += 1;
            }
            if ($searchValue && strpos($item[$searchValue] ?? null, $keyword) === false) {
                $tag += 1;
            }
            if ($tag > 0) {
                unset($target[$key]);
            }
        }

        foreach ($target as $item) {
            unset($item[Abs::FLAG_SEARCH_ALL]);
        }

        return $target;
    }

    /**
     * Object to string
     *
     * @param $target
     *
     * @return string
     */
    public static function objectToString($target): string
    {
        return self::safeBase64Encode(gzdeflate(serialize($target)));
    }

    /**
     * String to object
     *
     * @param string $target
     *
     * @return object
     */
    public static function stringToObject(string $target)
    {
        return unserialize(gzinflate(self::safeBase64Decode($target)));
    }

    /**
     * Set key use value for two-dimensional
     *
     * @param array  $target
     * @param string $valueKey
     *
     * @return array
     */
    public static function setKeyUseValue(array $target, string $valueKey): array
    {
        $items = array_column($target, $valueKey);
        $target = array_combine($items, $target);

        return [
            $target,
            $items,
        ];
    }

    /**
     * String replace once only
     *
     * @param string $needle
     * @param string $replace
     * @param string $haystack
     *
     * @return string
     */
    public static function strReplaceOnce(string $needle, ?string $replace, string $haystack): string
    {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return $haystack;
        }

        return substr_replace($haystack, $replace, $pos, self::strLen($needle));
    }

    /**
     * Get text width and height
     *
     * @param string $str
     * @param string $fonts
     * @param int    $size
     * @param float  $gap
     *
     * @return array
     */
    public static function textWidthPxByFonts(string $str, string $fonts, int $size = 14, float $gap = 1): array
    {
        $box = imagettfbbox($size, 0, $fonts, $str);

        $width = abs($box[4] - $box[0]);
        $height = abs($box[5] - $box[1]);

        return [
            $width * $gap,
            $height * $gap,
        ];
    }

    /**
     * @param string $str
     * @param array  $byteMapToPx
     *
     * @return float|int
     */
    public static function textWidthPxByMap(string $str, array $byteMapToPx = [])
    {
        $byteMapToPx = $byteMapToPx + [1 => 6, 3 => 11.4];

        $px = 0;
        [$length] = self::strLenByBytes($str);
        foreach ($length as $bytes => $total) {
            $px += (($byteMapToPx[$bytes] ?? 0) * $total);
        }

        return $px;
    }

    /**
     * Parse json string
     *
     * @param string|null $target
     * @param mixed       $default
     *
     * @return mixed
     */
    public static function parseJsonString(?string $target, $default = null)
    {
        if (empty($target) || !is_string($target)) {
            return $default ?? $target;
        }

        $result = self::jsonArray($target);
        $result = $result ?? ($default ?? $target);

        return $result;
    }

    /**
     * Space for en
     *
     * @param int  $n
     * @param bool $tab
     *
     * @return string
     */
    public static function enSpace(int $n = 1, bool $tab = false): string
    {
        return str_repeat(' ', $tab ? $n * 4 : $n);
    }

    /**
     * Space for cn
     *
     * @param int  $n
     * @param bool $tab
     *
     * @return string
     */
    public static function cnSpace(int $n = 1, bool $tab = false): string
    {
        return str_repeat('　', $tab ? $n * 4 : $n);
    }

    /**
     * decimal to n
     *
     * @param int    $number
     * @param int    $add
     * @param string $dict
     *
     * @return string
     */
    public static function decimal2n(int $number, int $add = 10000024, string $dict = null): string
    {
        $number += $add;
        $dict = $dict ?: self::dict4dec();
        $dict = str_replace(self::enSpace(), null, $dict);

        $to = self::strLen($dict);
        $result = null;

        do {
            $result = $dict[bcmod($number, $to)] . $result;
            $number = bcdiv($number, $to);
        } while ($number > 0);

        return ltrim($result, '0');
    }

    /**
     * n to decimal
     *
     * @param string $number
     * @param int    $add
     * @param string $dict
     *
     * @return int
     */
    public static function n2decimal(string $number, int $add = 10000024, string $dict = null): int
    {
        $number = strval($number);
        $dict = $dict ?: self::dict4dec();
        $dict = str_replace(self::enSpace(), null, $dict);

        $from = self::strLen($dict);
        $len = self::strLen($number);

        $result = 0;
        for ($i = 0; $i < $len; $i++) {
            $pos = strpos($dict, $number[$i]);
            $result = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $result);
        }

        return intval($result) - $add;
    }

    /**
     * Join items string by split
     *
     * @access public
     *
     * @param string $split
     * @param string ...$items
     *
     * @return string
     */
    public static function joinString(string $split, ...$items): string
    {
        $total = count($items) - 1;
        $itemsHanding = [];

        foreach ($items as $key => $value) {

            if (is_array($value)) {
                $value = implode($split, $value);
            }

            if (empty($value)) {
                continue;
            }

            if ($key == 0) {
                $itemsHanding[] = rtrim($value, $split);
            } elseif ($key == $total - 1) {
                $itemsHanding[] = ltrim($value, $split);
            } else {
                $itemsHanding[] = trim($value, $split);
            }
        }

        return implode($split, $itemsHanding);
    }

    /**
     * Set array values
     *
     * @param array $target
     * @param mixed $value
     * @param bool  $isValue
     *
     * @return array
     */
    public static function arrayValuesSetTo(array $target, $value, bool $isValue = false): array
    {
        $key = $isValue ? array_values($target) : array_keys($target);
        $value = array_fill(0, count($key), $value);

        return array_combine($key, $value);
    }

    /**
     * Handler for sql items when in
     *
     * @param mixed $items
     *
     * @return array
     */
    public static function sqlInItems($items): array
    {
        if (!is_array($items)) {
            $items = self::stringToArray($items);
        }

        $bind = rtrim(str_repeat('?, ', count($items)), ', ');
        $params = [];
        $types = [];
        foreach ($items as $val) {
            $val = self::numericValue($val);
            $params[] = $val;
            $types[] = is_numeric($val) ? Types::FLOAT : Types::STRING;
        }

        return [$bind, $params, $types];
    }

    /**
     * Handler for dql items when in
     *
     * @param mixed  $items
     * @param string $flag
     *
     * @return array
     */
    public static function dqlInItems($items, string $flag = ':'): array
    {
        if (!is_array($items)) {
            $items = self::stringToArray($items);
        }

        $bind = $params = $types = [];
        $random = self::generateToken(8, 36);

        foreach ($items as $key => $val) {
            $name = "_{$key}_{$random}";
            array_push($bind, "{$flag}{$name}");

            $val = self::numericValue($val);
            $params[$name] = $val;
            $types[$name] = is_numeric($val) ? Types::FLOAT : Types::STRING;
        }

        return [implode(', ', $bind), $params, $types];
    }

    /**
     * Array length
     *
     * @param array  $target
     * @param bool   $valueMode
     * @param string $valueKey
     *
     * @return array
     */
    public static function arrayLength(array $target, bool $valueMode = false, string $valueKey = null): array
    {
        if (!$valueMode) {
            $array = array_keys($target);
        } elseif (is_array(current($target))) {
            $array = array_column($target, $valueKey);
        } else {
            $array = array_values($target);
        }

        return array_map(
            function ($v) {
                return self::strLen($v);
            },
            $array
        );
    }

    /**
     * Assert flag
     *
     * @param int $flags
     * @param int $flag
     *
     * @return bool
     */
    public static function bitFlagAssert(int $flags, int $flag): bool
    {
        return (($flags & $flag) == $flag);
    }

    /**
     * Addition flag for bit
     *
     * @param int $flags
     * @param int $flag
     *
     * @return int
     */
    public static function bitFlagAddition(int $flags, int $flag): int
    {
        if (self::bitFlagAssert($flags, $flag)) {
            return $flags;
        }

        return $flags | $flag;
    }

    /**
     * Subtraction flag for bit
     *
     * @param int $flags
     * @param int $flag
     *
     * @return int
     */
    public static function bitFlagSubtraction(int $flags, int $flag): int
    {
        if (self::bitFlagAssert($flags, $flag)) {
            return $flags ^ $flag;
        }

        return $flags;
    }

    /**
     * Replace document with variables
     *
     * @param string $document
     * @param array  $variables
     * @param string $add
     *
     * @return string
     */
    public static function docVarReplace(string $document, array $variables, string $add = ':'): string
    {
        preg_match_all(self::reg4var($add), $document, $result);

        if (empty($result) || empty(current($result))) {
            return $document;
        }

        foreach ($result[1] as $var) {
            $document = str_replace("{{$var}}", $variables[$var] ?? null, $document);
        }

        return $document;
    }

    /**
     * Get variables from document
     *
     * @param string $document
     * @param string $add
     *
     * @return array
     */
    public static function docVarGet(string &$document, string $add = ':'): array
    {
        preg_match_all(self::reg4var($add), $document, $result);

        if (empty($result) || empty(current($result))) {
            return [];
        }

        $variables = [];
        foreach ($result[1] as $var) {
            if (strpos($var, $add) === false) {
                continue;
            }

            $result = explode($add, $var);
            $type = array_shift($result);
            $name = array_shift($result);

            $variables[$type][$name] = $result;
            $document = trim(str_replace("{{$var}}", null, $document));
        }

        return $variables;
    }

    /**
     * Int to bytes
     *
     * @param int  $number
     * @param bool $bigEndian
     *
     * @return array
     */
    public static function intToBytes(int $number, bool $bigEndian = false): array
    {
        $bytes = [];
        $bytes[0] = ($number & 0xff);
        $bytes[1] = ($number >> 8 & 0xff);
        $bytes[2] = ($number >> 16 & 0xff);
        $bytes[3] = ($number >> 24 & 0xff);

        return $bigEndian ? array_reverse($bytes) : $bytes;
    }

    /**
     * Bytes to int with position
     *
     * @param array $bytes
     * @param int   $position
     *
     * @return int
     */
    public static function bytesToInt(array $bytes, int $position): int
    {
        $val = $bytes[$position + 3] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 2] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 1] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position] & 0xff;

        return $val;
    }

    /**
     * Short string to bytes
     *
     * @param string $target
     *
     * @return array
     */
    public static function shortToBytes(string $target): array
    {
        $bytes = [];
        $bytes[0] = ($target & 0xff);
        $bytes[1] = ($target >> 8 & 0xff);

        return $bytes;
    }

    /**
     * Bytes to short string with position
     *
     * @param array $bytes
     * @param int   $position
     *
     * @return int
     */
    public static function bytesToShort(array $bytes, int $position): int
    {
        $val = $bytes[$position + 1] & 0xff;
        $val = $val << 8;
        $val |= $bytes[$position] & 0xff;

        return $val;
    }

    /**
     * String to bytes
     *
     * @param string $target
     *
     * @return array
     */
    public static function stringToBytes(string $target): array
    {
        $len = self::strLen($target);
        $bytes = [];
        for ($i = 0; $i < $len; $i++) {
            if (ord($target[$i]) >= 128) {
                $byte = ord($target[$i]) - 256;
            } else {
                $byte = ord($target[$i]);
            }
            $bytes[] = $byte;
        }

        return $bytes;
    }

    /**
     * Bytes to string
     *
     * @param array $bytes
     *
     * @return string
     */
    public static function bytesToString(array $bytes): string
    {
        $string = null;
        foreach ($bytes as $ch) {
            $string .= chr($ch);
        }

        return $string;
    }

    /**
     * Insert array to array with position
     *
     * @param array $source
     * @param int   $position
     * @param array $insertArray
     *
     * @return array
     */
    public static function arrayInsert(array $source, int $position, array $insertArray): array
    {
        $beginPart = array_splice($source, 0, $position);

        return array_merge($beginPart, $insertArray, $source);
    }

    /**
     * Insert array to array with assoc
     *
     * @param array  $source
     * @param string $position
     * @param array  $insertArray
     * @param bool   $before
     *
     * @return array
     */
    public static function arrayInsertAssoc(
        array $source,
        string $position,
        array $insertArray,
        bool $before = false
    ): array {

        $offset = array_search($position, array_keys($source));
        if ($offset === false) {
            return $before ? array_merge($insertArray, $source) : array_merge($source, $insertArray);
        }

        $beginPart = array_splice($source, 0, $before ? $offset : $offset + 1);

        return array_merge($beginPart, $insertArray, $source);
    }

    /**
     * Get client IP address
     *
     * @param int  $type 0:IP 1:IPv4
     * @param bool $adv  Advance mode
     *
     * @return mixed
     */
    public static function getClientIp($type = 0, bool $adv = false)
    {
        static $ip = null;
        $type = $type ? 1 : 0;

        if ($ip !== null) {
            return $ip[$type];
        }

        $get = function ($ip) use ($type) {

            if (empty($ip)) {
                return null;
            }

            // Check ip address
            $long = sprintf('%u', ip2long($ip));
            $ip = $long ? [
                $ip,
                $long,
            ] : [
                '0.0.0.0',
                0,
            ];

            return $ip[$type];
        };

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);

                return $get($ip);
            }

            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            return $get($ip);
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];

            return $get($ip);
        }

        return null;
    }

    /**
     * N-dimension array to one
     *
     * @param array  $items
     * @param string $key
     * @param string $split
     *
     * @return array
     */
    public static function nDimension2one(array $items, string $key = null, string $split = '.'): array
    {
        $result = [];
        foreach ($items as $k => $item) {
            $k = ($key ? $key . $split : null) . $k;
            if (!is_array($item)) {
                $result[$k] = $item;
            } else {
                $result = array_merge($result, self::nDimension2one($item, $k, $split));
            }
        }

        return $result;
    }

    /**
     * One-dimension array to n
     *
     * @param array  $items
     * @param string $split
     *
     * @return array
     */
    public static function oneDimension2n(array $items, string $split = '.'): array
    {
        $build = function (&$target, $key, $value) use (&$build, $split) {
            if (empty($key)) {
                return null;
            }
            $firstKey = array_shift($key);
            if (!isset($target[$firstKey])) {
                $target[$firstKey] = empty($key) ? $value : [];
            }
            $build($target[$firstKey], $key, $value);
        };

        $result = [];
        foreach ($items as $key => $item) {
            $key = explode($split, $key);
            $build($result, $key, $item);
        }

        return $result;
    }

    /**
     * Set array value
     *
     * @param array  $target
     * @param string $key
     * @param mixed  $value
     * @param string $split
     */
    public static function setArrayValue(array &$target, string $key, $value, string $split = '.')
    {
        $target = self::merge($target, self::oneDimension2n([$key => $value], $split));
    }

    /**
     * String end with substring
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function strEndWith(string $haystack, string $needle): bool
    {
        $len = strlen($needle);
        $end = substr($haystack, strlen($haystack) - $len, $len);

        return $end == $needle;
    }

    /**
     * Recursion value handler
     *
     * @param array    $source
     * @param callable $handler
     * @param array    $ignoreKeys
     *
     * @return array
     */
    public static function recursionValueHandler(array $source, callable $handler, array $ignoreKeys = []): array
    {
        foreach ($source as $key => $value) {
            if ($ignoreKeys && in_array($key, $ignoreKeys)) {
                continue;
            }
            if (is_iterable($value)) {
                $source[$key] = self::recursionValueHandler($value, $handler, $ignoreKeys);
            } else {
                $source[$key] = call_user_func_array($handler, [$value, $key]);
            }
        }

        return $source;
    }

    /**
     * Last item for array
     *
     * @param array &$source
     *
     * @return array
     */
    public static function arrayLastItem(array &$source): array
    {
        end($source);
        $item = [key($source), current($source)];
        reset($source);

        return $item;
    }

    /**
     * Check is url already
     *
     * @param string $str
     *
     * @return bool
     */
    public static function isUrlAlready(string $str): bool
    {
        return strpos($str, 'http') === 0 || strpos($str, '//') === 0;
    }

    /**
     * Human times
     *
     * @param int $times
     * @param int $decimals
     *
     * @return string
     */
    public static function humanTimes(int $times, int $decimals = 1): string
    {
        $map = [
            'w' => 100000,
            'k' => 10000,
        ];

        foreach ($map as $unit => $size) {
            if ($times >= $size) {
                return self::numberFormat($times / ($size / 10), $decimals, ',') . $unit;
            }
        }

        return self::numberFormat($times, $decimals, ',');
    }

    /**
     * @param string $money
     * @param int    $decimals
     * @param int    $expansion
     *
     * @return string
     */
    public static function humanMoney(string $money, int $decimals = 2, int $expansion = 1): string
    {
        $map = [
            'T' => 1000000000000000000000000000000000000000000,
            'D' => 1000000000000000000000000000000000000000,
            'U' => 1000000000000000000000000000000000000,
            'd' => 1000000000000000000000000000000000,
            'n' => 1000000000000000000000000000000,
            'o' => 1000000000000000000000000000,
            'S' => 1000000000000000000000000,
            's' => 1000000000000000000000,
            'Q' => 1000000000000000000,
            'q' => 1000000000000000,
            't' => 1000000000000,
            'B' => 1000000000,
            'M' => 1000000,
            'K' => 1000,
        ];

        foreach ($map as $unit => $size) {
            if ($money >= $size * $expansion) {
                return self::numberFormat($money / $size, $decimals, '') . $unit;
            }
        }

        return self::numberFormat($money, $decimals, ',');
    }

    /**
     * Human duration
     *
     * @param int $duration
     * @param int $decimals
     * @param int $power
     *
     * @return string
     */
    public static function humanDuration(int $duration, int $decimals = 1, int $power = 1): string
    {
        $map = [
            'year'  => Abs::TIME_YEAR,
            'month' => Abs::TIME_MONTH,
            'day'   => Abs::TIME_DAY,
        ];

        $duration *= Abs::TIME_HOUR;
        foreach ($map as $unit => $redouble) {
            $size = $redouble * $power;
            if ($duration >= $size) {
                return self::numberFormat($duration / $redouble, $decimals, ',') . $unit;
            }
        }

        return self::numberFormat($duration / Abs::TIME_HOUR, $decimals, ',') . 'hour';
    }

    /**
     * Human size
     *
     * @param int $byte
     * @param int $decimals
     *
     * @return string
     */
    public static function humanSize(int $byte, int $decimals = 1): string
    {
        $signed = $byte < 0 ? '-' : null;
        $byte = abs($byte);

        $map = [
            'PB' => 5,
            'TB' => 4,
            'GB' => 3,
            'MB' => 2,
            'KB' => 1,
        ];

        foreach ($map as $unit => $power) {
            $size = 1024 ** $power;
            if ($byte >= $size) {
                return $signed . self::numberFormat($byte / $size, $decimals, ',') . $unit;
            }
        }

        return "{$signed}{$byte}B";
    }

    /**
     * Secret string
     *
     * @param string $content
     * @param string $secret
     * @param array  $map
     *
     * @return string
     */
    public static function secretString(string $content, string $secret = '*', array $map = []): string
    {
        $content = self::split(trim($content));
        $length = count($content);

        $begin = $map[$length][0] ?? null;
        $end = $map[$length][1] ?? null;

        if ($length === 0) {
            $content = [$secret, $secret];
        } elseif ($length === 1) {
            $content = [$content[0], $secret];
        } elseif ($length === 2) {
            $content = [$content[0], $secret, $content[1]];
        } elseif ($length <= 5) {
            $repeat = array_fill(0, 1, $secret);
            array_splice($content, $begin ?? 1, -($end ?? 1), $repeat);
        } elseif ($length < 10) {
            $repeat = array_fill(0, 2, $secret);
            array_splice($content, $begin ?? 2, -($end ?? 2), $repeat);
        } else {
            $repeat = array_fill(0, 3, $secret);
            array_splice($content, $begin ?? 3, -($end ?? 3), $repeat);
        }

        return implode('', $content);
    }

    /**
     * Get args for pagination
     *
     * @param array $args
     * @param int   $pageSize
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public static function pageArgs(array $args, int $pageSize = 20): array
    {
        extract($args);

        $paging = $paging ?? false;
        if (!is_bool($paging)) {
            throw new InvalidArgumentException('Variable `paging` should be boolean');
        }

        $page = $page ?? 1;
        if (!is_int($page) || $page < 1) {
            throw new InvalidArgumentException('Variable `page` should be integer and gte 1');
        }

        $limit = $limit ?? $pageSize;
        if (!is_int($limit) || $limit < 0) {
            throw new InvalidArgumentException('Variable `limit` should be integer and gte 0');
        }

        $offset = ($page - 1) * $limit;

        return compact('paging', 'page', 'limit', 'offset');
    }

    /**
     * Create annotation object string
     *
     * @param array $options
     * @param bool  $inner
     * @param bool  $boundary
     *
     * @return string
     */
    public static function annotationJsonString(array $options, bool $inner = false, bool $boundary = true): string
    {
        $items = [];
        foreach ($options as $key => $value) {

            if ($inner) {
                $key = is_numeric($key) ? $key : "\"{$key}\"";
            }

            $eq = $inner ? ':' : '=';

            if (is_bool($value)) {
                array_push($items, "{$key}{$eq}" . ($value ? 'true' : 'false'));
            } elseif (is_numeric($value)) {
                array_push($items, "{$key}{$eq}{$value}");
            } elseif (is_string($value) && strpos($value, '::')) {
                array_push($items, "{$key}{$eq}{$value}");
            } elseif (is_scalar($value)) {
                array_push($items, "{$key}{$eq}\"{$value}\"");
            } elseif (is_array($value)) {
                array_push($items, "{$key}{$eq}" . self::annotationJsonString($value, true));
            }
        }

        $stringify = implode(', ', $items);
        if ($boundary) {
            return "{{$stringify}}";
        }

        return $stringify;
    }

    /**
     * Typeof array
     *
     * @param array  $target
     * @param string $exceptType
     *
     * @return string|bool
     */
    public static function typeofArray(array $target, string $exceptType = null)
    {

        $count = count($target);
        $same = array_intersect_key($target, range(0, $count - 1));

        if (count($same) == $count) {
            $type = Abs::T_ARRAY_INDEX;
        } elseif (empty($same)) {
            $type = Abs::T_ARRAY_ASSOC;
        } else {
            $type = Abs::T_ARRAY_MIXED;
        }

        return isset($exceptType) ? ($type === $exceptType) : $type;
    }

    /**
     * Calculation earth spherical distance
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     *
     * @return int
     */
    public static function calEarthSphericalDistance(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        /**
         * Approximate radius of earth in meters
         */

        $earthRadius = 6367000;

        /**
         * Convert these degrees to radians to work with the formula
         */

        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;

        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;

        /**
         * Using the Haversine formula calculate the distance
         *
         * @see http://en.wikipedia.org/wiki/Haversine_formula
         */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return intval($calculatedDistance);
    }

    /**
     * Group by date and x for chart
     *
     * @param array    $groupByDateAndX
     * @param string   $xField
     * @param string   $totalField
     * @param array    $xMap
     * @param callable $handler
     * @param string   $from
     * @param string   $to
     * @param string   $titleField
     * @param string   $valueField
     *
     * @return array
     */
    public static function groupByDateAndX4Chart(
        array $groupByDateAndX,
        string $xField,
        ?string $totalField,
        array $xMap,
        ?callable $handler = null,
        ?string $from = null,
        ?string $to = null,
        string $titleField = 'date',
        string $valueField = 'total'
    ): array {

        $data = [];
        foreach ($groupByDateAndX as $item) {

            $title = $item[$titleField];
            $key = $xMap[$item[$xField]] ?? $item[$xField];
            $data[$key][$title] = $item[$valueField];

            if (!isset($totalField)) {
                continue;
            }
            if (!isset($data[$totalField][$title])) {
                $data[$totalField][$title] = 0;
            }

            $data[$totalField][$title] += $item[$valueField];
        }

        if (isset($totalField)) {
            self::sendToBothEnds($data, $totalField);
        }

        foreach ($xMap as $type => $info) {
            if (!isset($data[$xMap[$type]])) {
                $data[$xMap[$type]] = [];
            }
        }

        $dataList = [];
        foreach ($data as $type => $item) {

            if (self::isIntNumeric($from) && self::isIntNumeric($to)) {
                $item = self::perfectIntKeys($item, $from, $to);
            } elseif (self::isDateString($from) && self::isDateString($to)) {
                $item = self::perfectDateKeys($item, $from, $to);
            }

            if ($handler) {
                foreach ($item as $key => $val) {
                    $item[$key] = call_user_func_array($handler, [$val, $key]);
                }
            }
            $dataList[$type] = $item;
        }

        return $dataList;
    }

    /**
     * Group by date for chart
     *
     * @param array    $groupByDate
     * @param callable $handler
     * @param string   $from
     * @param string   $to
     * @param string   $titleField
     * @param string   $valueField
     *
     * @return array
     */
    public static function groupByDate4Chart(
        array $groupByDate,
        ?callable $handler = null,
        ?string $from = null,
        ?string $to = null,
        string $titleField = 'date',
        string $valueField = 'total'
    ): array {

        $groupByDate = array_column($groupByDate, $valueField, $titleField);
        if ($from && $to) {
            $groupByDate = self::perfectDateKeys($groupByDate, $from, $to);
        }

        if ($handler) {
            foreach ($groupByDate as $key => $val) {
                $groupByDate[$key] = call_user_func_array($handler, [$val, $key]);
            }
        }

        return $groupByDate;
    }

    /**
     * Number between min and max
     *
     * @param mixed $number
     * @param float $min
     * @param float $max
     *
     * @return mixed
     */
    public static function numberBetween($number, float $min, float $max)
    {
        if (!is_numeric($number)) {
            return $min;
        }

        $number = min($max, $number);
        $number = max($min, $number);

        return $number;
    }

    /**
     * Is number between min and max
     *
     * @param mixed $number
     * @param float $min
     * @param float $max
     *
     * @return bool
     */
    public static function isNumberBetween($number, float $min, float $max): bool
    {
        if (!is_numeric($number)) {
            return false;
        }

        return (($number >= $min) && ($number <= $max));
    }

    /**
     * Number format
     *
     * @param string $number
     * @param int    $decimals
     * @param string $thousandsSep
     * @param string $decPoint
     * @param bool   $autoFloat
     *
     * @return float|string
     */
    public static function numberFormat(
        $number,
        int $decimals = 1,
        string $thousandsSep = '',
        string $decPoint = '.',
        bool $autoFloat = true
    ) {
        $number = number_format($number, $decimals, $decPoint, $thousandsSep);

        // handler the last zero
        if (strpos($number, $decPoint) !== false) {
            $number = rtrim($number, "0");
            $number = rtrim($number, "{$decPoint}{$thousandsSep}");
        }

        if (!$thousandsSep && $autoFloat) {
            $number = floatval($number);
        }

        return $number;
    }

    /**
     * Get ip chunk
     *
     * @param string $ip
     * @param int    $chunkNum
     *
     * @return string|array|null
     */
    public static function getIpChunk(string $ip, int $chunkNum = -1)
    {
        $ip = explode('.', $ip);

        $a = $ip[0];
        $b = $ip[1] ?? null;
        $c = $ip[2] ?? null;
        $d = $ip[3] ?? null;

        $chunk = [
            0 => "",
            1 => "{$a}",
            2 => trim("{$a}.{$b}", '.'),
            3 => trim("{$a}.{$b}.{$c}", '.'),
            4 => trim("{$a}.{$b}.{$c}.{$d}", '.'),
        ];

        if ($chunkNum < 0) {
            return $chunk;
        }

        return $chunk[$chunkNum] ?? null;
    }

    /**
     * Validate ip in white list
     *
     * @param string $ip
     * @param array  $whiteList
     *
     * @return bool
     */
    public static function ipInWhiteList(string $ip, array $whiteList): bool
    {
        $chunk = self::getIpChunk($ip);

        foreach ($whiteList as $item) {
            [$white, $mask] = explode('/', $item) + [1 => 32];
            $index = $mask / 8;
            if (self::getIpChunk($white, $index) == ($chunk[$index] ?? null)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load .evn file
     *
     * @param string $envFile
     *
     * @return int
     */
    public static function loadEnv(string $envFile): int
    {
        $count = 0;
        $envs = file_get_contents($envFile);

        foreach (explode("\n", $envs) as $env) {

            $env = trim($env);
            if (empty($env) || strpos($env, '#') === 0 || strpos($env, '=') === false) {
                continue;
            }

            $index = strpos($env, '=');
            $key = trim(substr($env, 0, $index));
            $value = trim(substr($env, $index + 1));

            putenv("{$key}={$value}");
            $_ENV[$key] = $_SERVER[$key] = $value;
            $count += 1;
        }

        return $count;
    }

    /**
     * Get file for upload
     *
     * @param string $filepath
     *
     * @return array
     */
    public static function getFileForUpload(string $filepath): array
    {
        clearstatcache();

        return [
            'name'     => pathinfo($filepath, PATHINFO_BASENAME),
            'type'     => (new finfo(FILEINFO_MIME_TYPE))->file($filepath),
            'tmp_name' => $filepath,
            'error'    => 0,
            'size'     => filesize($filepath),
        ];
    }

    /**
     * Array value strong for array_values
     *
     * @param array    $target
     * @param callable $assert
     *
     * @return array
     */
    public static function arrayValues(array $target, callable $assert = null): array
    {
        $doAssets = $assert ? call_user_func_array($assert, [$target]) : true;
        $target = $doAssets ? array_values($target) : $target;

        foreach ($target as $key => $value) {
            if (is_array($value)) {
                $target[$key] = self::arrayValues($value, $assert);
            }
        }

        return $target;
    }

    /**
     * Divide number to N copies
     *
     * @param float $number
     * @param int   $copies
     * @param int   $precision
     *
     * @return array
     */
    public static function divideNumberToNCopies(float $number, int $copies, $precision = 0): array
    {
        $divideNumber = bcdiv($number, $copies, $precision);
        $lastNumber = bcsub($number, $divideNumber * ($copies - 1), $precision);

        $set = array_fill(0, $copies - 1, $divideNumber);
        array_push($set, $lastNumber);

        return $set;
    }

    /**
     * Filter by bytes
     *
     * @param string $content
     * @param int    $bytes
     *
     * @return string
     */
    public static function filterByBytes(string $content, int $bytes = 4): string
    {
        $content = preg_replace_callback(
            '/./u',
            function ($match) use ($bytes) {
                return strlen($match[0]) >= $bytes ? null : $match[0];
            },
            $content
        );

        return $content;
    }

    /**
     * Integer to roman
     *
     * @param int $num
     *
     * @return string
     */
    public static function intToRoman(int $num): string
    {
        $dict = [
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1,
        ];

        $roman = '';
        foreach ($dict as $roman_char => $arabic_value) {
            if ($arabic_value > $num) {
                continue;
            }
            $tmp = (int)($num / $arabic_value);
            $roman .= str_repeat($roman_char, $tmp);
            $num -= $tmp * $arabic_value;
        }

        return $roman;
    }

    /**
     * Roman to integer
     *
     * @param string $roman
     *
     * @return int
     */
    public static function romanToInt(string $roman): int
    {
        $dict = [
            'I'  => 1,
            'IV' => 4,
            'V'  => 5,
            'IX' => 9,
            'X'  => 10,
            'XL' => 40,
            'L'  => 50,
            'XC' => 90,
            'C'  => 100,
            'CD' => 400,
            'D'  => 500,
            'CM' => 900,
            'M'  => 1000,
        ];
        $roman = strtoupper($roman);
        if (isset($dict[$roman])) {
            return $dict[$roman];
        }
        $len = strlen($roman);
        $value = 0;
        for ($i = 0; $i < $len; $i++) {
            $less = $roman[$i];
            $value += $dict[$less];
            if (($i - 1) >= 0) {
                $fLess = $roman[$i - 1];
                if ($fLess && $dict[$fLess] < $dict[$less]) {
                    $value -= ($dict[$fLess] * 2);
                }
            }
        }

        return $value;
    }

    /**
     * Relation array to index array
     *
     * @param array  $target
     * @param string $keyName
     * @param string $valueName
     *
     * @return array
     */
    public static function relationToIndex(array $target, string $keyName = 'key', string $valueName = 'value'): array
    {
        $result = [];
        foreach ($target as $key => $value) {
            array_push($result, [$keyName => $key, $valueName => $value]);
        }

        return $result;
    }

    /**
     * Cli show process
     *
     * @param string $message
     * @param float  $current
     * @param float  $total
     * @param array  $stepConfig
     */
    public static function cliShowProgress(string $message, float $current, float $total, array $stepConfig = [])
    {
        static $inProgress = false;

        if ($inProgress !== false && $inProgress <= $current) {
            fwrite(STDOUT, "\033[1A");
        }
        $inProgress = $current;
        $stepConfig = array_merge(
            [
                'step'  => 30,
                'space' => '-',
                'reach' => '#',
                'left'  => '   [',
                'right' => ' {process}%]',
            ],
            $stepConfig
        );

        if ($current !== false) {
            $current = abs($current);
            $total = $total < 1 ? 1 : $total;

            $percent = number_format(($current / $total) * 100, 2);
            $barStepNow = (int)round($stepConfig['step'] * ($current / $total));
            $barStepSurplus = $stepConfig['step'] - $barStepNow;
            $barStepSurplus = $barStepSurplus < 0 ? 0 : $barStepSurplus;

            $bar = str_repeat($stepConfig['reach'], $barStepNow) . str_repeat($stepConfig['space'], $barStepSurplus);
            $right = str_replace(['%', '{process}'], ['%%', '%3d'], $stepConfig['right']);
            $suffix = sprintf("{$right} %s", $percent, $message);
            fwrite(STDOUT, "{$stepConfig['left']}\033[32m{$bar}\033[0m{$suffix}" . PHP_EOL);
        } else {
            fwrite(STDOUT, "\007");
        }
    }

    /**
     * @param string $tag
     *
     * @return null|string
     */
    public static function parseDoctrineName(string $tag): ?string
    {
        $result = [];
        $doctrineName = null;
        preg_match_all("/.*?\\\\(Entity|Repository)(.*?)\\\\/", $tag, $result);
        if (!empty($result[2][0])) {
            $doctrineName = Helper::camelToUnder($result[2][0]);
        }

        return $doctrineName;
    }
}
