<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;

class UrlImage extends Tailor
{
    /**
     * @return void
     */
    protected function initial()
    {
        parent::initial();

        $this->web->appendSrcJsWithKey('fancy-box', Abs::JS_FANCY_BOX);
        $this->web->appendSrcCssWithKey('fancy-box', Abs::CSS_FANCY_BOX);
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewAnnotation(Arguments $args): array
    {
        $args->target[$this->fieldCamel] = Helper::merge(
            [
                'render' => Abs::RENDER_IMAGE,
                'width'  => 200,
                'align'  => Abs::POS_CENTER,
            ],
            $args->target[$this->fieldCamel] ?? []
        );

        return $args->target;
    }
}
