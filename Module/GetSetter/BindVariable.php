<?php

namespace Leon\BswBundle\Module\GetSetter;

trait BindVariable
{
    /**
     * @var string|null
     */
    protected $bindVariable;

    /**
     * @return string
     */
    public function getBindVariable(): ?string
    {
        return $this->bindVariable;
    }

    /**
     * @param string $bindVariable
     *
     * @return $this
     */
    public function setBindVariable(string $bindVariable)
    {
        $this->bindVariable = $bindVariable;

        return $this;
    }
}