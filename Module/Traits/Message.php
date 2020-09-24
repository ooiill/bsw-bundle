<?php

namespace Leon\BswBundle\Module\Traits;

use Leon\BswBundle\Component\Helper;
use SplQueue;

trait Message
{
    /**
     * @var SplQueue
     */
    protected $message;

    /**
     * @var SplQueue
     */
    protected $flag;

    /**
     * @var SplQueue
     */
    protected $object;

    /**
     * Push message
     *
     * @param string $message
     * @param string $flag
     * @param object $object
     *
     * @return false
     */
    protected function push(string $message, string $flag = null, $object = null)
    {
        if (!isset($this->message)) {
            $this->message = new SplQueue();
            $this->flag = new SplQueue();
            $this->object = new SplQueue();
        }

        $this->message->enqueue($message);
        $this->flag->enqueue($flag);
        $this->object->enqueue(serialize($object));

        return false;
    }

    /**
     * Pop message
     *
     * @param bool $needFlag
     * @param bool $needObject
     *
     * @return string|array|null
     */
    public function pop(bool $needFlag = false, bool $needObject = false)
    {
        if (!$this->message || $this->message->isEmpty()) {
            return null;
        }

        $message = isset($this->message) ? $this->message->dequeue() : null;
        $flag = isset($this->flag) ? $this->flag->dequeue() : null;
        $object = isset($this->object) ? $this->object->dequeue() : null;

        if (!$needFlag && !$needObject) {
            return $message;
        }

        $flag = Helper::numericValue($flag);

        return [$message, $flag, unserialize($object)];
    }
}