<?php

namespace Leon\BswBundle\Annotation\Entity\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Form;
use Leon\BswBundle\Module\Form\Entity\Input;
use Symfony\Component\Validator\Constraints\Length;

trait FormTypeConverter
{
    /**
     * @param $value
     *
     * @return Form|Form[]
     * @throws
     */
    protected function type($value)
    {
        $class = Form::class;

        if (is_object($value) && $value instanceof $class) {
            return $value;
        }

        if (!isset($value)) {
            return new Input();
        } elseif (is_array($value)) {
            foreach ($value as &$item) {
                if (!Helper::extendClass($item, $class)) {
                    $this->exception('type[]', "Must be class[] extend {$class}");
                }
                $item = new $item;
            }

            return $value;
        } elseif (!Helper::extendClass($value, $class)) {
            $this->exception('type', "Must be class extend {$class}");
        }

        return new $value;
    }
}