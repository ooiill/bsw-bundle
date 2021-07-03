<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Entity\FileSize;

class AttachmentImage extends Tailor
{
    /**
     * @var string
     */
    protected $newField;

    /**
     * @return void
     */
    protected function initial()
    {
        parent::initial();

        $this->web->appendSrcJsWithKey('fancy-box', Abs::JS_FANCY_BOX);
        $this->web->appendSrcCssWithKey('fancy-box', Abs::CSS_FANCY_BOX);

        $this->newField = "{$this->keyword}AttachmentImage";
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewQuery(Arguments $args): array
    {
        $args->target = Helper::merge(
            $args->target,
            [
                'select' => [
                    empty($args->target['select']) ? $args->target['alias'] : null,
                    "{$this->newField}.deep AS {$this->newField}Deep",
                    "{$this->newField}.filename AS {$this->newField}Filename",
                    "{$this->newField}.size AS {$this->newField}Size",
                ],
                'join'   => [
                    $this->newField => [
                        'entity' => BswAttachment::class,
                        'left'   => ["{$args->target['alias']}.{$this->fieldCamel}", "{$this->newField}.state"],
                        'right'  => ["{$this->newField}.id", Abs::NORMAL],
                    ],
                ],
            ]
        );

        $args->target['select'] = array_unique($args->target['select']);

        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewAnnotation(Arguments $args): array
    {
        $args->target[$this->newField] = Helper::merge(
            [
                'label'  => $this->label,
                'render' => Abs::RENDER_IMAGE,
                'sort'   => $args->previewAnnotation[$this->fieldCamel]['sort'] + 0.01,
                'width'  => 200,
                'align'  => Abs::POS_CENTER,
            ],
            $args->target[$this->newField] ?? []
        );
        $args->target["{$this->newField}Size"] = Helper::merge(
            [
                'hook' => FileSize::class,
                'sort' => $args->previewAnnotation[$this->fieldCamel]['sort'] + 0.02,
                'show' => false,
            ],
            $args->target["{$this->newField}Size"] ?? []
        );

        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewBeforeRender(Arguments $args): array
    {
        foreach ($args->target as &$item) {

            $item = $this->web->attachmentPreviewHandler(
                $item,
                $this->newField,
                ["{$this->newField}Deep", "{$this->newField}Filename"]
            );

            if (!empty($item[$this->newField])) {
                if (!empty($item['md5'])) {
                    $item[$this->newField] .= "?" . $this->md1($item['md5']);
                } elseif (!empty($item['sha1'])) {
                    $item[$this->newField] .= "?" . $this->md1($item['sha1']);
                }
            }

            if (!empty($item[$this->fieldCamel])) {
                $key = "{$this->newField}Size";
                $item[$this->fieldCamel] = "{$item[$this->fieldCamel]} » {$item[$key]}";
            }
        }

        return $args->target;
    }
}
