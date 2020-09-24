<?php

namespace Leon\BswBundle\Module\Filter;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Exception\FilterException;
use Exception;

class Dispatcher
{
    /**
     * @const int Get dql string
     */
    const DQL_MODE = 1;

    /**
     * @const int Get data only
     */
    const PARSE_MODE = 2;

    /**
     * @const int Get sql string
     */
    const SQL_MODE = 3;

    /**
     * Resolve array to expression
     *
     * @param string $field
     * @param array  $item
     * @param int    $mode
     *
     * @return mixed
     * @throws
     */
    public function filter(string $field, array $item, int $mode = self::DQL_MODE)
    {
        $filter = $item['filter'] ?? null;
        $value = $item['value'] ?? null;

        if (empty($filter)) {
            throw new FilterException("Filter `filter` is required with field `{$field}`");
        }

        if (is_null($value)) {
            throw new FilterException("Filter `value` is required with field `{$field}`");
        }

        if (!Helper::extendClass($filter, Filter::class)) {
            $filterHandling = is_object($filter) ? get_class($filter) : $filter;
            throw new FilterException("Filter `{$filterHandling}` is invalid with field `{$field}`");
        }

        if (!is_object($filter)) {
            $filter = new $filter;
        }

        try {
            $parse = $filter->parse($value);
        } catch (FilterException $e) {
            throw new FilterException($e->getMessage());
        } catch (Exception $e) {
            throw new FilterException("Filter data format is not standard with filed `{$field}`");
        }

        if ($mode === self::PARSE_MODE) {
            return $parse;
        }

        $parse = array_values((array)$parse);

        if ($mode === self::SQL_MODE) {
            return $filter->sql(Helper::tableFieldAddTag($field), $parse);
        }

        if ($mode === self::DQL_MODE) {
            return $filter->dql($field, $parse);
        }

        return null;
    }

    /**
     * Resolve array to expression for list
     *
     * @param array $list
     * @param int   $mode
     * @param array $fieldMap
     *
     * @return mixed
     * @throws
     */
    public function filterList(array $list, int $mode = self::SQL_MODE, array $fieldMap = [])
    {
        $listHanding = array_filter($list);
        foreach ($listHanding as $field => $item) {
            $field = $fieldMap[$field] ?? $field;
            $item = $this->filter($field, $item, $mode);
            $listHanding[$field] = $item + [1 => [], 2 => []];
        }

        $orient = [
            'where'  => [],
            'having' => [],
            'args'   => [],
            'type'   => [],
        ];

        foreach ($list as $key => $v) {
            /**
             * @var Filter $filter
             */
            $filter = $v['filter'];
            $k = $filter->isHaving() ? 'having' : 'where';
            $orient[$k][] = $listHanding[$key][0];
            $orient['args'][] = $listHanding[$key][1];
            $orient['type'][] = $listHanding[$key][2];
        }

        if ($mode === self::SQL_MODE) {
            foreach (['where', 'having'] as $k) {
                $orient[$k] = implode(' AND ', $orient[$k]);
            }
            foreach (['args', 'type'] as $k) {
                $orient[$k] = array_merge(...$orient[$k]);
            }

            return $orient;
        }

        $args = [];
        foreach (['args', 'type'] as $k) {
            $orient[$k] = array_merge(...$orient[$k]);
        }
        foreach ($orient['args'] as $key => $value) {
            $args[$key] = [$value, $orient['type'][$key]];
        }

        $orient['args'] = $args;
        unset($orient['type']);

        return $orient;
    }
}