<?php

namespace Leon\BswBundle\Module\GetSetter;

trait FileItems
{
    /**
     * @var string
     */
    protected $fileListKey;

    /**
     * @var string
     */
    protected $fileMd5Key;

    /**
     * @var string
     */
    protected $fileSha1Key;

    /**
     * @var string
     */
    protected $fileUrlKey;

    /**
     * @return string
     */
    public function getFileListKey(): string
    {
        return $this->fileListKey;
    }

    /**
     * @param string $fileListKey
     *
     * @return $this
     */
    public function setFileListKey(string $fileListKey)
    {
        $this->fileListKey = $fileListKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileMd5Key(): ?string
    {
        return $this->fileMd5Key;
    }

    /**
     * @param string $fileMd5Key
     *
     * @return $this
     */
    public function setFileMd5Key(string $fileMd5Key)
    {
        $this->fileMd5Key = $fileMd5Key;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileSha1Key(): ?string
    {
        return $this->fileSha1Key;
    }

    /**
     * @param string $fileSha1Key
     *
     * @return $this
     */
    public function setFileSha1Key(string $fileSha1Key)
    {
        $this->fileSha1Key = $fileSha1Key;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileUrlKey(): ?string
    {
        return $this->fileUrlKey;
    }

    /**
     * @param string $fileUrlKey
     *
     * @return $this
     */
    public function setFileUrlKey(string $fileUrlKey)
    {
        $this->fileUrlKey = $fileUrlKey;

        return $this;
    }
}