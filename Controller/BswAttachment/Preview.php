<?php

namespace Leon\BswBundle\Controller\BswAttachment;

use Leon\BswBundle\Entity\BswAttachment;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Leon\BswBundle\Module\Scene\ButtonScene;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property TranslatorInterface $translator
 */
trait Preview
{
    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswAttachment::class;
    }

    /**
     * @return array
     */
    public function previewTailor(): array
    {
        return [
            Tailor\AttachmentFile::class => [
                0 => [
                    0 => 'deep',
                    1 => 'filename',
                ],
            ],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            (new Button('Upload', null, $this->cnf->icon_upload))
                ->setType(Abs::THEME_DEFAULT)
                ->setRoute('app_bsw_attachment_upload_file')
                ->setClick('showIFrame')
                ->setName('attachment_diy_upload')
                ->setArgs(
                    [
                        'width'        => Abs::MEDIA_MIN,
                        'height'       => 157,
                        'title'        => false,
                        'closable'     => false,
                        'maskClosable' => true,
                    ]
                ),

            new Button('New record', 'app_bsw_attachment_persistence', $this->cnf->icon_newly),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function previewRecordOperates(Arguments $args): array
    {
        return [
            (new Button('Edit record', 'app_bsw_attachment_persistence'))->setArgs(['id' => $args->item['id']]),

            (new ButtonScene('Copy link'))
                ->setType(Abs::THEME_DEFAULT)
                ->setClick('copyFileLink')
                ->setArgs(['link' => $args->item['filenameAttachmentFile']]),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-attachment/preview", name="app_bsw_attachment_preview")
     * @Access(export=true)
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview();
    }
}