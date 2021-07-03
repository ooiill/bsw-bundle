<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;

class UrlIcon extends UrlImage
{
    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewAnnotation(Arguments $args): array
    {
        $args->target = parent::tailorPreviewAnnotation($args);
        $args->target[$this->fieldCamel] = Helper::merge(
            $args->target[$this->fieldCamel],
            [
                'render' => Abs::RENDER_IMAGE_TINY,
                'width'  => 120,
            ],
        );

        return $args->target;
    }
}
