<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Entity\FileSize;

class AttachmentIcon extends AttachmentImage
{
    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewAnnotation(Arguments $args): array
    {
        $args->target = parent::tailorPreviewAnnotation($args);
        $args->target[$this->newField] = Helper::merge(
            $args->target[$this->newField],
            [
                'render' => Abs::RENDER_IMAGE_TINY,
                'width'  => 120,
            ]
        );

        return $args->target;
    }
}
