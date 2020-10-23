<?php

namespace Leon\BswBundle\Module\Exception;

use Exception;
use Throwable;

class UploadException extends Exception
{
    /**
     * @var array
     */
    protected $args = [];

    /**
     * UploadException constructor.
     *
     * @param string         $message
     * @param array          $args
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(?string $message = null, array $args = [], int $code = 0, Throwable $previous = null)
    {
        if (!empty($code)) {
            $message = $this->codeToMessage($code);
        }

        $message = $message ?? 'Unknown upload error';
        $this->args = $args;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @param $code
     *
     * @return string|null
     */
    private function codeToMessage(int $code): ?string
    {
        $message = null;

        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "Uploaded file exceeds the php directive";
                break;

            case UPLOAD_ERR_FORM_SIZE:
                $message = "Uploaded file exceeds the html directive";
                break;

            case UPLOAD_ERR_PARTIAL:
                $message = "Uploaded file was only partially";
                break;

            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;

            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;

            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;
        }

        return $message;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}