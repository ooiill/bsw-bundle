<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Entity\Abs;

class MessageScene extends Message
{
    /**
     * Success
     *
     * @param string      $message
     * @param array       $messageArgs
     * @param string|null $route
     *
     * @return MessageScene
     */
    public function success(string $message, array $messageArgs = [], ?string $route = null)
    {
        $message = $this
            ->setMessage($message)
            ->setClassify(Abs::TAG_CLASSIFY_SUCCESS)
            ->appendArgs(Helper::arrayMapKey($messageArgs, '{{ %s }}'));

        return $route ? $message->setRoute($route) : $message;
    }

    /**
     * Failed
     *
     * @param string $message
     * @param array  $messageArgs
     *
     * @return MessageScene
     */
    public function failed(string $message, array $messageArgs = [])
    {
        return $this
            ->setMessage($message)
            ->setClassify(Abs::TAG_CLASSIFY_ERROR)
            ->appendArgs(Helper::arrayMapKey($messageArgs, '{{ %s }}'));
    }

    /**
     * Notice
     *
     * @param string      $message
     * @param array       $messageArgs
     * @param null|string $route
     *
     * @return MessageScene
     */
    public function notice(string $message, array $messageArgs = [], ?string $route = null)
    {
        $message = $this
            ->setMessage($message)
            ->setClassify(Abs::TAG_CLASSIFY_INFO)
            ->appendArgs(Helper::arrayMapKey($messageArgs, '{{ %s }}'))
            ->setType(Abs::TAG_TYPE_NOTICE);

        return $route ? $message->setRoute($route) : $message;
    }
}