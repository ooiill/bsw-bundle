<?php

namespace Leon\BswBundle\Module\Error;

use Symfony\Component\HttpFoundation\Response;

abstract class Error
{
    /**
     * @const int
     */
    const CODE = 0;

    /**
     * @const int
     */
    const HTTP_CODE = Response::HTTP_OK;

    /**
     * @var string
     */
    protected $tiny = 'Oops';

    /**
     * @var string
     */
    protected $description;

    /**
     * Error constructor.
     *
     * @param string|null $tiny
     * @param string|null $description
     */
    public function __construct(string $tiny = null, string $description = null)
    {
        $tiny && $this->tiny = $tiny;
        $description && $this->description = $description;
    }

    /**
     * Message for error
     *
     * @return int
     */
    public function code4logic(): int
    {
        return static::CODE;
    }

    /**
     * @return int
     */
    public function code4http(): int
    {
        return static::HTTP_CODE;
    }

    /**
     * Tiny for error
     *
     * @return string
     */
    public function tiny(): string
    {
        return $this->tiny;
    }

    /**
     * Description for error
     *
     * @return string
     */
    public function description(): string
    {
        return $this->description ?: $this->tiny();
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return [
            $this->code4http(),
            $this->code4logic(),
            $this->tiny(),
            $this->description(),
        ];
    }
}