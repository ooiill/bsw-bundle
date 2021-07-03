<?php

namespace Leon\BswBundle\Module\Bsw\Persistence\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\ImageUpload as FormImageUpload;

class ImageUpload extends Tailor
{
    /**
     * @return void
     */
    public function initial()
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
    public function tailorPersistenceAnnotation(Arguments $args): array
    {
        $args->target[$this->fieldCamel] = Helper::merge(
            ['hide' => true],
            $args->target[$this->fieldCamel] ?? []
        );

        $args->target["{$this->fieldCamel}ImageUpload"] = Helper::merge(
            [
                'label'           => $this->label,
                'column'          => 8,
                'type'            => FormImageUpload::class,
                'disabledOverall' => false,
                'sort'            => $args->persistAnnotation[$this->fieldCamel]['sort'] + 0.01,
                'valueShadow'     => function (array $handing, array $record) {
                    return $record[$this->fieldCamel] ?? null;
                },
                'typeArgs'        => [
                    'flag'       => $this->fieldCamel,
                    'needId'     => false,
                    'fileUrlKey' => $this->fieldCamel,
                    'change'     => 'persistenceImageUploadChange',
                ],
            ],
            $args->target["{$this->fieldCamel}ImageUpload"] ?? []
        );

        return $args->target;
    }
}