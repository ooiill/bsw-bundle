<?php

namespace Leon\BswBundle\Component;

class UploadItem
{
    /**
     * @var string
     */
    public $tmpName;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $size;

    /**
     * @var mixed
     */
    public $key;

    /**
     * @var string
     */
    public $suffix;

    /**
     * @var string
     */
    public $savePath;

    /**
     * @var string
     */
    public $saveName;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var string
     */
    public $file;

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var string
     */
    public $md5;

    /**
     * @var string
     */
    public $sha1;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $url;

    /**
     * @var bool
     */
    public $new = true;

    /**
     * UploadItem constructor.
     *
     * @param string $tmpName
     * @param string $name
     * @param mixed  $key
     * @param int    $size
     */
    public function __construct(string $tmpName, string $name, $key, int $size)
    {
        $this->tmpName = $tmpName;
        $this->name = strip_tags($name);
        $this->key = $key;
        $this->suffix = Helper::getSuffix($this->name);
        $this->size = $size;
    }
}
