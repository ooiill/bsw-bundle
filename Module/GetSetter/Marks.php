<?php

namespace Leon\BswBundle\Module\GetSetter;

use Leon\BswBundle\Component\Helper;

trait Marks
{
    /**
     * @var array|int
     */
    protected $marks = [
        20 => 20,
        40 => 40,
        60 => 60,
        80 => 80,
    ];

    /**
     * @return string
     */
    public function getMarks(): string
    {
        return Helper::jsonStringify($this->marks);
    }

    /**
     * @param array $marks
     *
     * @return $this
     */
    public function setMarks(array $marks)
    {
        $this->marks = $marks;

        return $this;
    }

    /**
     * @param array $marks
     *
     * @return $this
     */
    public function appendMarks(array $marks)
    {
        $this->marks = array_merge($this->marks, $marks);

        return $this;
    }
}