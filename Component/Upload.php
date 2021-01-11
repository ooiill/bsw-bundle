<?php

namespace Leon\BswBundle\Component;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\UploadException;
use Exception;
use stdClass;
use finfo;

class Upload
{
    /**
     * @var string
     */
    public $flag;

    /**
     * @var string
     */
    public $rootPath;

    /**
     * @var array
     */
    public $nameNoChar = ['\\', '/', ':', '*', '?', '"', '<', '>', '|'];

    /**
     * @var float|int
     */
    public $maxSize = 1024 * 8;

    /**
     * @var array
     */
    public $suffix = [];

    /**
     * @var array
     */
    public $noSuffix = ['php'];

    /**
     * @var array
     */
    public $mime = [];

    /**
     * @var array
     */
    public $noMime = [];

    /**
     * @var array
     */
    public $picSizes = [[10, 'max'], [10, 'max']];

    /**
     * @var array
     */
    public $savePathFn = [];

    /**
     * @var string Just data format
     */
    public $savePathFmt;

    /**
     * @var array
     */
    public $saveNameFn = ['uniqid'];

    /**
     * @var bool
     */
    public $saveReplace = false;

    /**
     * @var bool
     */
    public $manual = false;

    /**
     * @var bool
     */
    public $removeAfterUpload = false;

    /**
     * @var array
     */
    public $currentFile;

    /**
     * Upload constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $item => $value) {
            if (!property_exists($this, $item)) {
                continue;
            }
            $this->{$item} = $value;
            $stringToArray = ['suffix', 'noSuffix', 'mime', 'noMime'];
            if (in_array($item, $stringToArray) && is_string($this->{$item})) {
                $this->{$item} = Helper::stringToArray($this->{$item});
            }
        }
    }

    /**
     * Upload File
     *
     * @param array $files
     *
     * @return UploadItem[]
     * @throws
     */
    public function upload(array $files): array
    {
        // no file
        if (empty($files)) {
            throw new UploadException('No file upload');
        }

        // check root path
        $this->checkRootPath($this->rootPath);

        // check file one by one
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);

        $result = [];
        foreach ($files as $key => $item) {
            if (!empty($item['error'])) {
                throw new UploadException(null, [], $item['error']);
            }

            $this->currentFile = $item;
            $file = new UploadItem($item['tmp_name'], $item['name'], $item['key'] ?? $key, $item['size']);

            // get suffix by extend for adobe.flash upload
            $file->type = strtolower($fileInfo->file($file->tmpName));

            // check file
            $this->check($file);

            // create sub directory
            $file->savePath = $this->getSavePath();

            // create save name
            $file->saveName = $this->getSaveName($file);

            // check image
            if (in_array($file->suffix, Abs::IMAGE_SUFFIX)) {

                // check sizes
                if ($this->picSizes && !$this->checkSizes($file->tmpName)) {
                    throw new UploadException(
                        'Image sizes error, sizes: {{ sizes }}',
                        ['{{ sizes }}' => $this->sizesToString()]
                    );
                }

                // check core
                $imgInfo = getimagesize($file->tmpName);
                if (empty($imgInfo) || ('gif' === $file->suffix && empty($imgInfo['bits']))) {
                    throw new UploadException('Image illegal');
                }

                // get width and height
                [$file->width, $file->height] = $imgInfo;
            }

            // save file
            if ($file->file = $this->save($file, $this->saveReplace)) {

                $file->fileName = Helper::joinString(DIRECTORY_SEPARATOR, $file->savePath, $file->saveName);
                $file->md5 = md5_file($file->file);
                $file->sha1 = sha1_file($file->file);

                if ($this->removeAfterUpload) {
                    unlink($file->file);
                }

                $result[$key] = $file;
            }
        }

        return $result;
    }

    /**
     * Get options tips
     *
     * @param array    $option
     * @param callable $labelHandler
     *
     * @return array
     */
    public static function optionTips(array $option, callable $labelHandler = null): array
    {
        $option = (object)$option;
        $tips = new stdClass();

        $tips->maxFileSize = '≤' . Helper::humanSize(($option->maxSize ?? 0) * Abs::HEX_SIZE);
        $tips->allowSuffix = ['*'];
        $tips->allowMime = ['*'];

        if (!empty($option->suffix)) {
            $tips->allowSuffix = $option->suffix;
        } elseif (!empty($option->noSuffix)) {
            $tips->allowSuffix = Helper::arrayMap($option->noSuffix, '!%s');
        }

        if (!empty($option->mime)) {
            $tips->allowMime = $option->mime;
        } elseif (!empty($option->noMime)) {
            $tips->allowMime = Helper::arrayMap($option->noMime, '!%s');
        }

        $image = array_merge(Abs::IMAGE_SUFFIX, ['*']);
        $intersect = array_intersect($image, $tips->allowSuffix);
        if (count($intersect) > 0) {
            [$_w, $_h] = $option->picSizes;
            [$wMin, $wMax] = is_array($_w) ? $_w : [$_w, $_w];
            [$hMin, $hMax] = is_array($_h) ? $_h : [$_h, $_h];
            $wMax = (strtoupper($wMax) === Abs::IMAGE_SIZE_MAX) ? Abs::IMAGE_SIZE_MAX : "{$wMax}px";
            $hMax = (strtoupper($hMax) === Abs::IMAGE_SIZE_MAX) ? Abs::IMAGE_SIZE_MAX : "{$hMax}px";
            $wInfo = $wMin == $wMax ? "{$wMin}px" : "[{$wMin}px~{$wMax}]";
            $hInfo = $hMin == $hMax ? "{$hMin}px" : "[{$hMin}px~{$hMax}]";
            $tips->pictureSizes = "{$wInfo}*{$hInfo}";
        }

        $tips->allowSuffix = implode('、', $tips->allowSuffix);
        $tips->allowMime = implode('、', $tips->allowMime);

        $key = 0;
        $tipsHandling = [];
        foreach ($tips as $type => $condition) {
            $type = Helper::stringToLabel($type);
            if (is_callable($labelHandler)) {
                $type = call_user_func_array($labelHandler, [$type]);
            }
            $tipsHandling[] = [
                'key'       => ++$key,
                'type'      => $type,
                'condition' => $condition,
            ];
        }

        return [$tipsHandling, $tips->allowSuffix, $tips->allowMime];
    }

    /**
     * Check The File
     *
     * @param UploadItem $file
     *
     * @return bool
     * @throws
     */
    private function check(UploadItem $file): bool
    {
        if (empty($file->name)) {
            throw new UploadException('Unknown upload error');
        }

        if (!empty($this->nameNoChar)) {
            foreach ($this->nameNoChar as $char) {
                if (strpos($file->name, $char) === false) {
                    continue;
                }
                throw new UploadException('File name illegal');
            }
        }

        if (!$this->manual && !is_uploaded_file($file->tmpName)) {
            throw new UploadException('File illegal');
        }

        // check size
        if (!$this->checkSize($file->size)) {
            throw new UploadException(
                'File size error, size: {{ size }}',
                ['{{ size }}' => Helper::humanSize($this->maxSize * Abs::HEX_SIZE)]
            );
        }

        // check suffix
        if ($this->suffix && !in_array($file->suffix, $this->suffix)) {
            throw new UploadException('File suffix not allow');
        }

        if ($this->noSuffix && in_array($file->suffix, $this->noSuffix)) {
            throw new UploadException('File suffix not allow');
        }

        // check mime
        // warning: all file mime is application/octet-stream by adobe.flash upload
        if ($this->mime && !in_array($file->type, $this->mime)) {
            throw new UploadException('File mime not allow');
        }

        if ($this->noMime && in_array($file->type, $this->noMime)) {
            throw new UploadException('File mime not allow');
        }

        return true;
    }

    /**
     * Check size
     *
     * @param int $size
     *
     * @return bool
     */
    private function checkSize(int $size): bool
    {
        $size /= 1024; // B to KB

        return ($size <= $this->maxSize) || (0 == $this->maxSize);
    }

    /**
     * Sizes rule to string
     *
     * @return string
     */
    private function sizesToString(): string
    {
        [$width, $height] = $this->picSizes;
        if (is_array($width)) {
            $width = implode('-', $width);
        }
        if (is_array($height)) {
            $height = implode('-', $height);
        }

        return "{$width}*{$height}";
    }

    /**
     * Check sizes
     *
     * @param string $filePath
     *
     * @return bool
     * @throws
     */
    private function checkSizes(string $filePath): bool
    {
        [$width, $height] = getimagesize($filePath);

        try {

            [$ruleSizes['width'], $ruleSizes['height']] = $this->picSizes;

            /**
             * Check pic width and height
             *
             * @param array  $ruleSizes
             * @param string $type
             *
             * @return bool
             */
            $checkWidthAndHeight = function ($ruleSizes, $type) use ($width, $height) {
                if (is_array($ruleSizes[$type])) {
                    [$min, $max] = $ruleSizes[$type];
                    if (strtolower($max) === 'max') {
                        $max = ${$type};
                    }
                    if (${$type} < intval($min) || ${$type} > intval($max)) {
                        return false;
                    }
                } else {
                    if (intval($ruleSizes[$type]) != ${$type}) {
                        return false;
                    }
                }

                return true;
            };

            if (!$checkWidthAndHeight($ruleSizes, 'width')) {
                return false;
            }

            if (!$checkWidthAndHeight($ruleSizes, 'height')) {
                return false;
            }

        } catch (Exception $e) {
            throw new UploadException('Picture sizes format error');
        }

        return true;
    }

    /**
     * Get save name
     *
     * @param UploadItem $file
     *
     * @return string
     * @throws
     */
    private function getSaveName(UploadItem $file): string
    {
        if (empty($this->saveNameFn)) {
            return $file->name;
        }

        $saveName = $this->createName($this->saveNameFn);

        if (empty($file->suffix)) {
            return $saveName;
        }

        return "{$saveName}.{$file->suffix}";
    }

    /**
     * Get save directory name
     *
     * @return string
     * @throws
     */
    private function getSavePath(): ?string
    {
        $subPath = null;

        if (empty($this->savePathFn)) {
            return null;
        }

        $subPath = $this->createName($this->savePathFn, $this->savePathFmt);
        $fullPath = Helper::joinString(DIRECTORY_SEPARATOR, $this->rootPath, $subPath);
        if (!empty($subPath) && !is_dir($fullPath) && !@mkdir($fullPath, 0777, true)) {
            throw new UploadException('Create directory fail');
        }

        return $subPath;
    }

    /**
     * Save path builder
     *
     * @return string
     */
    public function savePath(): string
    {
        return $this->flag;
    }

    /**
     * Create name by rule
     *
     * @param array|callable $rule
     * @param string         $fmt
     *
     * @return string
     */
    private function createName($rule, string $fmt = null): string
    {
        $rule = (array)$rule + [1 => [], 2 => false];
        [$fn, $params, $unshiftUploader] = $rule;

        if (is_string($fn) && !function_exists($fn)) {
            $fn = [$this, $fn];
        }

        $params = (array)$params;
        if ($unshiftUploader) {
            array_unshift($params, $this);
        }

        $name = call_user_func_array($fn, array_values($params));

        if ($fmt) {
            $fmt = date($fmt);
            $name = "{$name}{$fmt}";
        }

        return $name;
    }

    /**
     * Check directory
     *
     * @param string $rootPath
     *
     * @return string
     * @throws
     */
    private function checkRootPath(string $rootPath = null): string
    {
        if (empty($rootPath)) {
            throw new UploadException('Root path is required');
        }

        if (!is_dir($rootPath) && !@mkdir($rootPath, 0777, true)) {
            throw new UploadException('Create directory fail');
        }

        return $this->rootPath = realpath($rootPath) . DIRECTORY_SEPARATOR;
    }

    /**
     * Save file
     *
     * @param UploadItem $file
     * @param bool       $replace
     *
     * @return string
     * @throws
     */
    private function save(UploadItem $file, bool $replace = false): string
    {
        $fileName = Helper::joinString(DIRECTORY_SEPARATOR, $this->rootPath, $file->savePath, $file->saveName);

        // replace file
        if (!$replace && is_file($fileName)) {
            throw new UploadException('File move fail, file exists');
        }

        // move file
        if ($this->manual) {
            copy($file->tmpName, $fileName);
            unlink($file->tmpName);
        } else {
            if (!move_uploaded_file($file->tmpName, $fileName)) {
                throw new UploadException('File move error');
            }
        }

        return $fileName;
    }

    /**
     * Rebuild file instance
     *
     * @param UploadItem $file
     * @param object     $record
     *
     * @return UploadItem
     */
    public function rebuild(UploadItem $file, $record): UploadItem
    {
        $file->savePath = $record->deep;
        $file->saveName = $record->filename;
        $file->file = Helper::joinString(DIRECTORY_SEPARATOR, $this->rootPath, $file->savePath, $file->saveName);
        $file->fileName = Helper::joinString(DIRECTORY_SEPARATOR, $file->savePath, $file->saveName);;
        $file->id = $record->id;
        $file->new = false;

        return $file;
    }
}
