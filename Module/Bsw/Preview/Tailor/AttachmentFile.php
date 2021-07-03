<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;

class AttachmentFile extends Tailor
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

        if (!is_array($this->field) || count($this->field) !== 2) {
            $this->field = ['deep', 'filename'];
        }

        $this->fieldCamel = Helper::underToCamel(end($this->field));
        $this->newField = "{$this->fieldCamel}AttachmentFile";
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
                'label'  => 'Url',
                'render' => Abs::RENDER_LINK,
                'sort'   => $args->previewAnnotation[$this->fieldCamel]['sort'] + 0.01,
                'width'  => 400,
            ],
            $args->target[$this->newField] ?? []
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
                $this->field,
                false
            );

            if (!empty($item[$this->newField])) {
                if (!empty($item['md5'])) {
                    $item[$this->newField] .= "?" . $this->md1($item['md5']);
                } elseif (!empty($item['sha1'])) {
                    $item[$this->newField] .= "?" . $this->md1($item['sha1']);
                }
            }
        }

        return $args->target;
    }
}
