<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Html;

trait RootClick
{
    /**
     * @var string
     */
    protected $rootClickForVue = 'dispatcherByVue';

    /**
     * @var string
     */
    protected $rootClickForNative = 'bsw.cnf.v.dispatcherByNative(this)';

    /**
     * @return string
     */
    public function getRootClickForVue(): string
    {
        return $this->rootClickForVue;
    }

    /**
     * @return string
     */
    public function getRootClickForNative(): string
    {
        return $this->rootClickForNative;
    }

    /**
     * @param string $rootClickForVue
     *
     * @return $this
     */
    public function setRootClickForVue(string $rootClickForVue)
    {
        $this->rootClickForVue = $rootClickForVue;

        return $this;
    }

    /**
     * @param string $rootClickForNative
     * @param array  $params
     *
     * @return $this
     */
    public function setRootClickForNative(string $rootClickForNative, array $params = [])
    {
        $this->rootClickForNative = Html::scriptBuilder($rootClickForNative, $params);

        return $this;
    }
}