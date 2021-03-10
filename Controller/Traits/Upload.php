<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Component\Upload as Uploader;
use Leon\BswBundle\Component\UploadItem;
use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorUpload;
use Leon\BswBundle\Repository\BswAttachmentRepository;
use OSS\Core\OssException;
use Monolog\Logger;
use OSS\OssClient;
use Exception;
use Qcloud\Cos\Client as CosClient;

/**
 * @property Logger $logger
 */
trait Upload
{
    /**
     * @var array upload document
     */
    public static $docMap = [
        'text/plain'                    => 'txt',
        'text/markdown'                 => 'md',
        'application/pdf'               => 'pdf',
        'application/msword'            => 'doc',
        'application/vnd.ms-powerpoint' => 'ppt',
    ];

    /**
     * @var array upload archive
     */
    public static $archiveMap = [
        'application/x-bzip'           => 'bz',
        'application/x-bzip2'          => 'bz2',
        'application/x-rar-compressed' => 'rar',
        'application/x-tar'            => 'tar',
        'application/zip'              => 'zip',
        'application/x-7z-compressed'  => '7z',
    ];

    /**
     * @var array upload excel
     */
    public static $excelMap = [
        'application/vnd.ms-excel'                                          => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
    ];

    /**
     * @var array upload csv
     */
    public static $csvMap = [
        'text/plain' => 'csv',
        'text/csv'   => 'csv',
    ];

    /**
     * @var array upload pictures
     */
    public static $imgMap = [
        'image/png'     => 'png',
        'image/gif'     => 'gif',
        'image/jpeg'    => 'jpeg',
        'image/jpg'     => 'jpg',
        'image/svg+xml' => 'svg',
        'image/bmp'     => 'bmp',
        'image/webp'    => 'webp',
    ];

    /**
     * @var array
     */
    public static $imgSimpleMap = [
        'image/png'     => 'png',
        'image/jpeg'    => 'jpeg',
        'image/jpg'     => 'jpg',
        'image/svg+xml' => 'svg',
    ];

    /**
     * @var array android package
     */
    public static $apkMap = [
        'application/vnd.android.package-archive' => 'apk',
        'application/zip'                         => 'apk',
    ];

    /**
     * @var array ios package
     */
    public static $ipaMap = [
        'application/octet-stream.ipa' => 'ipa',
        'application/x-ios-app'        => 'ipa',
        'application/zip'              => 'ipa',
    ];

    /**
     * Merge maps
     *
     * @param array ...$maps
     *
     * @return array
     */
    public static function mergeMimeMaps(...$maps)
    {
        $mime = $suffix = [];
        foreach ($maps as $item) {
            $mime = array_merge($mime, array_keys($item));

            $suffixHandling = Helper::stringToArray(implode(',', array_values($item)));
            $suffix = array_merge($suffix, $suffixHandling);
        }

        return [
            'mime'   => array_filter(array_unique($mime)),
            'suffix' => array_filter(array_unique($suffix)),
        ];
    }

    /**
     * Get upload option with flag
     *
     * @param string $flag
     * @param bool   $manual
     *
     * @return array
     */
    public function uploadOptionByFlag(string $flag, bool $manual = false): array
    {
        $default = [
            'flag'        => $flag,
            'maxSize'     => $this->parameter('upload_max_mb') * Abs::HEX_SIZE,
            'suffix'      => [],
            'mime'        => [],
            'picSizes'    => [[10, 'max'], [10, 'max']],
            'saveReplace' => $this->parameter('upload_replace_file'),
            'rootPath'    => $this->parameter('file'),
            'manual'      => $manual,
            'saveNameFn'  => $this->parameter('upload_save_name'),
            'savePathFn'  => $this->parameter('upload_save_path'),
            'savePathFmt' => $this->parameter('upload_save_fmt'),
        ];

        return $this->dispatchMethod(Abs::FN_UPLOAD_OPTIONS, $default, [$flag, $default]);
    }

    /**
     * For Ali Cloud
     *
     * @param UploadItem $file
     *
     * @return UploadItem
     * @throws
     */
    public function ossUpload(UploadItem $file): UploadItem
    {
        $ossKey = $this->parameterInOrderByEmpty(['ali_oss_key', 'ali_key']);
        $ossSecret = $this->parameterInOrderByEmpty(['ali_oss_secret', 'ali_secret']);

        if (!$this->parameter('upload_to_oss') || empty($ossKey) || empty($ossSecret)) {
            return $file;
        }

        try {

            $fileName = "{$file->savePath}/{$file->saveName}";

            $ossClient = new OssClient($ossKey, $ossSecret, $this->parameter('ali_oss_endpoint'));
            $ossClient->setConnectTimeout($this->cnf->curl_timeout_second * 20);
            $ossClient->setTimeout($this->cnf->curl_timeout_second * 20);

            $ossClient->uploadFile(
                $this->parameter('ali_oss_bucket'),
                $fileName,
                $file->file
            );

        } catch (OssException $e) {
            $this->logger->error("Ali oss upload error: {$e->getMessage()}");

            return $file;
        }

        // remove local file
        if ($this->cnf->rm_local_file_when_oss ?? false) {
            @unlink($file->file);
        }

        return $file;
    }

    /**
     * For TX Cloud
     *
     * @param UploadItem $file
     *
     * @return UploadItem
     * @throws
     */
    public function cosUpload(UploadItem $file): UploadItem
    {
        $cosKey = $this->parameterInOrderByEmpty(['tx_cos_key', 'tx_key']);
        $cosSecret = $this->parameterInOrderByEmpty(['tx_cos_secret', 'tx_secret']);

        if (!$this->parameter('upload_to_cos') || empty($cosKey) || empty($cosSecret)) {
            return $file;
        }

        $cosClient = new CosClient(
            [
                'region'      => $this->parameter('tx_cos_region'),
                'schema'      => 'https',
                'credentials' => [
                    'secretId'  => $cosKey,
                    'secretKey' => $cosSecret,
                ],
            ]
        );

        try {
            $f = fopen($file->file, 'rb');
            $fileName = "{$file->savePath}/{$file->saveName}";
            $cosClient->upload($this->parameter('tx_cos_bucket'), $fileName, $f);
        } catch (Exception $e) {
            $this->logger->error("Tx cos upload error: {$e->getMessage()}");

            return $file;
        }

        // remove local file
        if ($this->cnf->rm_local_file_when_cos ?? false) {
            @unlink($file->file);
        }

        return $file;
    }

    /**
     * Upload one core
     *
     * @param array $file
     * @param array $options
     * @param int   $platform
     *
     * @return object
     * @throws
     */
    public function uploadOneCore(array $file, array $options, int $platform = 2)
    {
        /**
         * @var BswAttachmentRepository $bswAttachment
         */
        $bswAttachment = $this->repo(BswAttachment::class);
        $userId = $this->usr('usr_uid') ?? 0;

        // multiplex in same person
        if (!empty($file['tmp_name'])) {
            $sha1 = sha1_file($file['tmp_name']);
        } else {
            $sha1 = md5(uniqid());
        }

        $nonExistsForce = $options['nonExistsForce'] ?? false;
        if ($nonExistsForce) {
            $exists = false;
        } else {
            $exists = $bswAttachment->findOneBy(
                $unique = [
                    'sha1'     => $sha1,
                    'platform' => $platform,
                    'userId'   => $userId,
                ]
            );
        }

        // upload
        try {
            $options['removeAfterUpload'] = !!$exists;
            $uploader = new Uploader($options);
            $file = current($uploader->upload([$file]));
        } catch (Exception $e) {
            if ($options['manual']) {
                throw new Exception($this->messageLang($e->getMessage(), $e->getArgs()));
            } else {
                return $this->failedAjax(new ErrorUpload(), $e->getMessage(), $e->getArgs());
            }
        }

        if ($exists) {

            // The file already exists
            if ($exists->state !== Abs::NORMAL) {
                $bswAttachment->modify([Abs::PK => $exists->id], ['state' => Abs::NORMAL]);
            }

            $file = $uploader->rebuild($file, $exists);

        } else {

            // The file is new and upload to oss
            $file->id = $bswAttachment->newly(
                [
                    'platform' => $platform,
                    'userId'   => $userId,
                    'sha1'     => $file->sha1,
                    'size'     => $file->size,
                    'deep'     => $file->savePath,
                    'filename' => Html::cleanHtml($file->saveName),
                    'state'    => Abs::NORMAL,
                ]
            );

            if ($file->id === false) {
                if ($options['manual']) {
                    throw new Exception($bswAttachment->pop());
                } else {
                    return $this->failedAjax(new ErrorUpload(), $bswAttachment->pop());
                }
            }

            $file = $this->ossUpload($file);
            $file = $this->cosUpload($file);

            if (is_callable($options['fileUploadFn'] ?? null)) {
                $file = call_user_func_array($options['fileUploadFn'], [$file]);
            }
        }

        // file url
        $file = $this->attachmentPreviewHandler($file, 'url', ['savePath', 'saveName'], false);
        if (is_callable($options['fileFn'] ?? null)) {
            $file = call_user_func_array($options['fileFn'], [$file]);
        }

        return $file;
    }
}
