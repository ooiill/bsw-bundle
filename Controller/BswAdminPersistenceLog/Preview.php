<?php

namespace Leon\BswBundle\Controller\BswAdminPersistenceLog;

use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Entity\BswAdminPersistenceLog;
use Leon\BswBundle\Entity\BswAdminUser;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;
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
        return BswAdminPersistenceLog::class;
    }

    /**
     * @return array
     */
    public function previewQuery(): array
    {
        return [
            'alias'  => 'pl',
            'select' => ['pl', 'u.name AS userId', 'u.phone'],
            'join'   => [
                'u' => [
                    'entity' => BswAdminUser::class,
                    'left'   => ['pl.userId'],
                    'right'  => ['u.id'],
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
            // new Button('New record', 'app_bsw_admin_persistence_log_persistence', $this->cnf->icon_newly),
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
            // (new Button('Edit record', 'app_bsw_admin_persistence_log_persistence'))->setArgs(['id' => $args->item['id']]),
        ];
    }

    /**
     * @param string $label
     * @param string $value
     *
     * @return Charm
     */
    protected function printJson(string $label, ?string $value): Charm
    {
        if (substr_count($value, "\n") + 1 < 20) {
            return new Charm(Abs::HTML_JSON, Html::cleanHtml($value, true));
        }

        $button = (new ButtonScene($label))->sceneCharmCodeModal($value);

        return new Charm($this->getButtonHtml($button));
    }

    /**
     * @param Arguments $args
     *
     * @return mixed
     */
    public function previewCharmBefore(Arguments $args)
    {
        return $this->printJson('BEFORE', $args->value);
    }

    /**
     * @param Arguments $args
     *
     * @return mixed
     */
    public function previewCharmLater(Arguments $args)
    {
        return $this->printJson('LATER', $args->value);
    }

    /**
     * @param Arguments $args
     *
     * @return mixed
     */
    public function previewCharmEffect(Arguments $args)
    {
        return $this->printJson('EFFECT', $args->value);
    }

    /**
     * Preview record
     *
     * @Route("/bsw-admin-persistence-log/preview", name="app_bsw_admin_persistence_log_preview")
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

        return $this->showPreview(
            [
                'function'     => 'initHighlightBlock',
                'functionArgs' => ['selector' => 'pre.bsw-long-text code'],
            ]
        );
    }
}