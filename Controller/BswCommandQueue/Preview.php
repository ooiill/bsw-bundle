<?php

namespace Leon\BswBundle\Controller\BswCommandQueue;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswCommandQueue;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;
use Leon\BswBundle\Module\Bsw\Preview\Tailor;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Leon\BswBundle\Module\Scene\ButtonScene;
use Leon\BswBundle\Module\Scene\Charm;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait Preview
{
    /**
     * @return string
     */
    public function previewEntity(): string
    {
        return BswCommandQueue::class;
    }

    /**
     * @return array
     */
    public function previewTailor(): array
    {
        return [
            Tailor\AttachmentLink::class => [
                0 => 'fileAttachmentId',
            ],
        ];
    }

    /**
     * @return array
     */
    public function previewQuery(): array
    {
        return [
            'order' => [
                'bcq.state' => Abs::SORT_ASC,
                'bcq.id'    => Abs::SORT_DESC,
            ],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            new Button('New record', 'app_bsw_command_queue_persistence', $this->cnf->icon_newly),
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
            (new Button('Edit record', 'app_bsw_command_queue_persistence'))->setArgs(['id' => $args->item['id']]),
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function previewAfterHook(Arguments $args): array
    {
        $condition = Helper::parseJsonString($args->original['condition']);

        if (isset($condition['entity'])) {
            $condition['entity'] = base64_decode($condition['entity']);
        }

        if (isset($condition['query'])) {
            $condition['query'] = '-- CONDITION FOR EXPORT --';
        }

        $args->hooked['condition'] = Helper::formatPrintJson($condition, 4, ': ');

        return $args->hooked;
    }

    /**
     * @param Arguments $args
     *
     * @return mixed
     */
    public function previewCharmCondition(Arguments $args)
    {
        $button = (new ButtonScene($this->fieldLang('Condition')))->sceneCharmCodeModal($args->value);

        return new Charm($this->getButtonHtml($button));
    }

    /**
     * Preview record
     *
     * @Route("/bsw-command-queue/preview", name="app_bsw_command_queue_preview")
     * @Access()
     *
     * @return Response
     */
    public function preview(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $this->appendSrcCssWithKey('highlight', Abs::CSS_HIGHLIGHT_GH);
        $this->appendSrcJsWithKey('highlight', Abs::JS_HIGHLIGHT);

        return $this->showPreview(['dynamic' => 5]);
    }
}