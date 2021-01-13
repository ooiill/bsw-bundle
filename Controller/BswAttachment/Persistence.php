<?php

namespace Leon\BswBundle\Controller\BswAttachment;

use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Form\Entity\Upload;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait Persistence
{
    /**
     * @return string
     */
    public function persistenceEntity(): string
    {
        return BswAttachment::class;
    }

    /**
     * Persistence record
     *
     * @Route("/bsw-attachment/persistence/{id}", name="app_bsw_attachment_persistence", requirements={"id": "\d+"})
     * @Access()
     *
     * @param int $id
     *
     * @return Response
     */
    public function persistence(int $id = null): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence(['id' => $id]);
    }

    /**
     * @return array
     */
    public function uploadFileAnnotationOnly(): array
    {
        return [
            'upload' => [
                'label'           => false,
                'type'            => Upload::class,
                'disabledOverall' => false,
                'typeArgs'        => [
                    'flag'        => 'mixed',
                    'class'       => 'tools-upload',
                    'needId'      => false,
                    'needTips'    => false,
                    'buttonStyle' => [
                        'font-size' => '16px',
                        'height'    => '64px',
                        'margin'    => '10px auto 0',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function uploadFileFormOperates(): array
    {
        return ['submit' => []];
    }

    /**
     * @return string
     */
    public function uploadFileEntity(): string
    {
        return self::persistenceEntity();
    }

    /**
     * Upload file
     *
     * @Route("/bsw-attachment/upload-file", name="app_bsw_attachment_upload_file")
     * @Access()
     *
     * @return Response
     */
    public function uploadFile(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPersistence();
    }
}
