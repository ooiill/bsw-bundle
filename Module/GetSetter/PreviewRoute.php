<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Html;

trait PreviewRoute
{
    /**
     * @var string
     */
    protected $previewRoute;

    /**
     * @var array
     */
    protected $previewArgs = [];

    /**
     * @var array
     */
    protected $previewIframeArgs = [];

    /**
     * @return string
     */
    public function getPreviewRoute(): ?string
    {
        return $this->previewRoute;
    }

    /**
     * @param string $previewRoute
     *
     * @return $this
     */
    public function setPreviewRoute(string $previewRoute)
    {
        $this->previewRoute = $previewRoute;

        return $this;
    }

    /**
     * @return array
     */
    public function getPreviewArgs(): array
    {
        return $this->previewArgs;
    }

    /**
     * @param array $previewArgs
     *
     * @return $this
     */
    public function setPreviewArgs(array $previewArgs)
    {
        $this->previewArgs = $previewArgs;

        return $this;
    }

    /**
     * @param array $previewArgs
     *
     * @return $this
     */
    public function appendPreviewArgs(array $previewArgs)
    {
        $this->previewArgs = array_merge($this->previewArgs, $previewArgs);

        return $this;
    }

    /**
     * @return array
     */
    public function getPreviewIframeArgs(): array
    {
        return $this->previewIframeArgs;
    }

    /**
     * @param array $previewIframeArgs
     *
     * @return $this
     */
    public function setPreviewIframeArgs(array $previewIframeArgs)
    {
        $this->previewIframeArgs = $previewIframeArgs;

        return $this;
    }
}