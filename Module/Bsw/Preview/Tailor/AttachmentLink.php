<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Hook\Entity\FileSize;

class AttachmentLink extends Tailor
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @return mixed|void
     */
    protected function initial()
    {
        $this->alias = "_tailor_{$this->keyword}";
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
                    "{$this->alias}.deep AS {$this->keyword}_deep",
                    "{$this->alias}.filename AS {$this->keyword}_filename",
                    "{$this->alias}.size AS {$this->keyword}_size",
                ],
                'join'   => [
                    "{$this->alias}" => [
                        'entity' => BswAttachment::class,
                        'left'   => ["{$args->target['alias']}.{$this->fieldCamel}", "{$this->alias}.state"],
                        'right'  => ["{$this->alias}.id", Abs::NORMAL],
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
        $sort = $args->previewAnnotation[$this->fieldCamel]['sort'];
        $args->target[$this->alias] = array_merge(
            [
                'label'  => $this->label,
                'render' => Abs::RENDER_LINK,
                'sort'   => $sort + .01,
                'width'  => 400,
            ],
            $args->target[$this->alias] ?? []
        );
        $args->target["{$this->keyword}_size"] = array_merge(
            [
                'hook' => FileSize::class,
                'sort' => $sort + .02,
                'show' => false,
            ],
            $args->target["{$this->keyword}_size"] ?? []
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
                $this->alias,
                ["{$this->keyword}_deep", "{$this->keyword}_filename"]
            );

            if (!empty($item[$this->alias])) {
                if (!empty($item['md5'])) {
                    $item[$this->alias] .= "?" . $this->md1($item['md5']);
                } elseif (!empty($item['sha1'])) {
                    $item[$this->alias] .= "?" . $this->md1($item['sha1']);
                }
            }

            if (!empty($item[$this->fieldCamel])) {
                $key = "{$this->keyword}_size";
                $item[$this->fieldCamel] = "{$item[$this->fieldCamel]} » {$item[$key]}";
            }
        }

        return $args->target;
    }
}
