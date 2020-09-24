<?php

namespace Leon\BswBundle\Module\Hook\Entity;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Hook;
use Symfony\Contracts\Translation\TranslatorInterface;

class Enums extends Hook
{
    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function preview($value, array $args)
    {
        $value = $args['enum'][$value] ?? Abs::DIRTY;
        $trans = $args['trans'] ?? null;

        if (is_object($trans) && $trans instanceof TranslatorInterface) {
            return $trans->trans($value, [], 'enum');
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param array $args
     *
     * @return mixed
     */
    public function persistence($value, array $args)
    {
        return array_flip($args)[$value] ?? null;
    }
}