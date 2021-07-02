<?php

namespace Leon\BswBundle\Module\GetSetter;

trait Line
{
    /**
     * @var array
     */
    protected $line = [
        'avg' => [
            'type' => 'average',
            'name' => 'avg',
        ],
    ];

    /**
     * @return array
     */
    public function getLine(): array
    {
        return $this->line;
    }

    /**
     * @param array $line
     *
     * @return $this
     */
    public function setLine(array $line)
    {
        $this->line = $line;

        return $this;
    }
}