<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;

class Message
{
    /**
     * @var Error|int
     */
    protected $code = 0;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $classify = Abs::TAG_CLASSIFY_WARNING;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var string
     */
    protected $click;

    /**
     * @var string
     */
    protected $type = Abs::TAG_TYPE_MESSAGE;

    /**
     * @var int
     */
    protected $duration;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var array
     */
    protected $sets = [];

    /**
     * Message constructor.
     *
     * @param string $message
     * @param string $classify
     * @param string $route
     */
    public function __construct(?string $message = null, ?string $classify = null, ?string $route = null)
    {
        isset($message) && $this->message = $message;
        isset($classify) && $this->classify = $classify;
        isset($route) && $this->route = $route;
    }

    /**
     * @return Error|int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param Error|int $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message ?? '';
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @param string $route
     *
     * @return $this
     */
    public function setRoute(?string $route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClick(): ?string
    {
        return $this->click;
    }

    /**
     * @param string $click
     *
     * @return $this
     */
    public function setClick(string $click)
    {
        $this->click = $click;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(?string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return $this
     */
    public function setDuration(?int $duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassify(): string
    {
        return $this->classify;
    }

    /**
     * @param string $classify
     *
     * @return $this
     */
    public function setClassify(string $classify)
    {
        $this->classify = $classify;

        return $this;
    }

    /**
     * @return bool
     */
    public function isErrorClassify(): bool
    {
        return $this->getClassify() == Abs::TAG_CLASSIFY_ERROR;
    }

    /**
     * @return bool
     */
    public function isSuccessClassify(): bool
    {
        return $this->getClassify() == Abs::TAG_CLASSIFY_SUCCESS;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     *
     * @return $this
     */
    public function setArgs(array $args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @param array $args
     *
     * @return $this
     */
    public function appendArgs(array $args)
    {
        $this->args = array_merge($this->args, $args);

        return $this;
    }

    /**
     * @return array
     */
    public function getSets(): array
    {
        return $this->sets;
    }

    /**
     * @param array $sets
     *
     * @return $this
     */
    public function setSets(array $sets)
    {
        $this->sets = $sets;

        return $this;
    }
}