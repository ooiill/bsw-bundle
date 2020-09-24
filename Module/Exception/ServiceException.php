<?php

namespace Leon\BswBundle\Module\Exception;

use Exception;
use Throwable;

class ServiceException extends Exception
{
    /**
     * @var int
     */
    protected $error;

    /**
     * ServiceException constructor.
     *
     * @param string    $message
     * @param int       $error
     * @param int       $code
     * @param Throwable $previous
     */
    public function __construct(string $message = "", int $error = 0, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->error;
    }
}