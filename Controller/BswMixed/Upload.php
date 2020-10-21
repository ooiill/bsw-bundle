<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Component\UploadItem;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Module\Error\Entity\ErrorUpload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

/**
 * @property LoggerInterface $logger
 */
trait Upload
{
    /**
     * File uploader
     *
     * @Route("/upload", name="app_upload")
     * @Access()
     *
     * @I("file_flag")
     *
     * @O("attachment_id")
     * @O("attachment_url")
     * @O("attachment_md5")
     * @O("attachment_sha1")
     * @O("frontend_args")
     *
     * @return Response
     * @throws
     */
    public function uploadAction(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $upload = $_FILES[$args->file_flag] ?? [];
        if (empty($upload)) {
            return $this->failedAjax(new ErrorUpload(), 'No file upload');
        }

        $options = $this->uploadOptionByFlag($args->file_flag);

        /**
         * @var UploadItem $file
         */
        $file = $this->uploadOneCore($upload, $options);
        if ($file instanceof Response) {
            return $file;
        }

        $sets = [
            'attachment_id'   => $file->id,
            'attachment_url'  => $file->url,
            'attachment_md5'  => $file->md5,
            'attachment_sha1' => $file->sha1,
            'frontend_args'   => (array)$args,
        ];

        if ($href = $file->href ?? null) {
            $sets['href'] = $this->redirectUrl($href);
            $this->appendResult(
                [
                    'title'        => $this->messageLang('File upload done'),
                    'subTitle'     => Html::tag(
                        'a',
                        $file->url,
                        [
                            'target' => '_blank',
                            'href'   => $file->url,
                        ]
                    ),
                    'subTitleHtml' => true,
                    'icon'         => 'b:icon-download',
                    'ok'           => 'copyFileLink',
                    'okText'       => $this->twigLang('Copied it and close'),
                    'extra'        => ['link' => $file->url],
                ]
            );

            return $this->okayAjax($sets);
        }

        return $this->okayAjax($sets, 'File upload done');
    }
}