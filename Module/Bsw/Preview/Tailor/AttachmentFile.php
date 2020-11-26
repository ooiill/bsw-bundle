<?php

namespace Leon\BswBundle\Module\Bsw\Preview\Tailor;

use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Entity\Abs;

class AttachmentFile extends Tailor
{
    /**
     * @return mixed|void
     */
    protected function initial()
    {
        $this->web->appendSrcJsWithKey('fancy-box', Abs::JS_FANCY_BOX);
        $this->web->appendSrcCssWithKey('fancy-box', Abs::CSS_FANCY_BOX);

        if (!is_array($this->field) || count($this->field) !== 2) {
            $this->field = ['deep', 'filename'];
        }

        $this->fieldCamel = end($this->field);
        $this->label = "_tailor_{$this->fieldCamel}";
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPreviewAnnotation(Arguments $args): array
    {
        $sort = $args->previewAnnotation[$this->fieldCamel]['sort'] + .01;
        $args->target[$this->label] = array_merge(
            [
                'label'  => 'Url',
                'render' => Abs::RENDER_LINK,
                'sort'   => $sort,
                'width'  => 400,
            ],
            $args->target[$this->label] ?? []
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
                $this->label,
                $this->field,
                false
            );

            if (!empty($item[$this->label])) {
                if (!empty($item['md5'])) {
                    $item[$this->label] .= "?" . $this->md1($item['md5']);
                } elseif (!empty($item['sha1'])) {
                    $item[$this->label] .= "?" . $this->md1($item['sha1']);
                }
            }
        }

        return $args->target;
    }
}
