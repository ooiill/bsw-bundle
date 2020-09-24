<?php

namespace Leon\BswBundle\Annotation\Entity;

use Doctrine\ORM\Mapping\Id;
use Leon\BswBundle\Annotation\Entity\Traits\FieldConverter;
use Leon\BswBundle\Annotation\Entity\Traits\LabelConverter;
use Leon\BswBundle\Annotation\Entity\Traits\RulesConverter;
use Leon\BswBundle\Annotation\Entity\Traits\TransConverter;
use Leon\BswBundle\Annotation\Entity\Traits\ValidatorConverter;
use Leon\BswBundle\Annotation\AnnotationConverter;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;

/**
 * @property Input $item
 */
class InputConverter extends AnnotationConverter
{
    use FieldConverter;
    use LabelConverter;
    use TransConverter;
    use ValidatorConverter;
    use RulesConverter;

    /**
     * @param $value
     *
     * @return bool
     */
    protected function hide($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        return false;
    }

    /**
     * @param $value
     *
     * @return array
     * @throws
     */
    protected function rulesArgsHandler($value)
    {
        if (empty($value)) {
            return [];
        }

        if (!is_array($value)) {
            $this->exception('rulesArgsHandler', "Must be type array");
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return int
     * @throws
     */
    protected function error($value)
    {
        $errorCls = Error::class;
        if (!Helper::extendClass($value, $errorCls)) {
            $this->exception('code', "Must be class extend {$errorCls}");
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return int
     * @throws
     */
    protected function sign($value)
    {
        if (!is_bool($value) && ($value != Abs::AUTO)) {
            $this->exception('sign', "Must be boolean value or string as 'auto'");
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function type($value)
    {
        if (empty($value)) {
            foreach (['string', 'int', 'numeric'] as $type) {
                if (isset($this->item->rules[$type])) {
                    $value = $type;
                    break;
                }
            }
        }

        return $value ?: 'string';
    }

    /**
     * @param $value
     *
     * @return mixed
     * @throws
     */
    protected function method($value)
    {
        $methods = [
            Abs::REQ_GET,
            Abs::REQ_POST,
            Abs::REQ_DELETE,
            Abs::REQ_HEAD,
        ];

        if (!empty($value) && !in_array($value, $methods)) {
            $methods = implode(',', $methods);
            $this->exception('method', "Must in [{$methods}]");
        }

        return $value;
    }
}