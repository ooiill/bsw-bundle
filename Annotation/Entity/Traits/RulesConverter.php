<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;

trait RulesConverter
{
    /**
     * @param $value
     *
     * @return array
     */
    protected function rules($value)
    {
        if (is_string($value)) {
            $value = Helper::stringToArray($value, true, true, null, Abs::VALIDATION_SPLIT);
            $rulesArr = [];
            foreach ($value as $rule) {
                $rule = Helper::stringToArray($rule, true, false);
                $rulesArr[array_shift($rule)] = $rule;
            }
            $value = $rulesArr;
        }

        if (!is_array($value)) {
            $this->exception('rules', 'should be string or array');
        }

        $handling = [];
        foreach ($value as $fn => $args) {
            if (is_int($fn)) {
                [$fn, $args] = [$args, []];
            }
            $handling[$fn] = (array)$args;
        }

        return $handling;
    }
}