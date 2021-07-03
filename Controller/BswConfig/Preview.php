<?php

namespace Leon\BswBundle\Controller\BswConfig;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswConfig;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Filter\Entity\Mixed;
use Leon\BswBundle\Module\Form\Entity\Button;
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
        return BswConfig::class;
    }

    /**
     * @return array
     */
    public function previewFilterAnnotation(): array
    {
        return [
            'text' => [
                'label'      => 'Key and value',
                'sort'       => 1.01,
                'adopt'      => true,
                'field'      => 'bc.text',
                'filter'     => Mixed::class,
                'filterArgs' => [
                    'fields' => [
                        'bc.key',
                        'bc.value',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function previewQuery()
    {
        return [
            'order' => ['bc.key' => Abs::SORT_ASC],
        ];
    }

    /**
     * @return Button[]
     */
    public function previewOperates()
    {
        return [
            (new Button('Project config'))
                ->setType(Abs::THEME_DASHED)
                ->setRoute('app_bsw_configured_preview')
                ->setClick('showIFrame')
                ->setName('yaml_config_list')
                ->setArgs(
                    [
                        'width' => Abs::MEDIA_LG,
                        'title' => false,
                    ]
                ),
            new Button('New record', 'app_bsw_config_persistence', $this->cnf->icon_newly),
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
            (new Button('Edit record', 'app_bsw_config_persistence'))->setArgs(['id' => $args->item['id']]),
            (new ButtonScene('Clone'))
                ->setType(Abs::THEME_ELE_WARNING_OL)
                ->setRoute('app_bsw_config_persistence')
                ->setFill($this->clonePreviewToForm($args->hooked, false)),
            (new Button('Remove', 'app_bsw_config_away'))
                ->setType(Abs::THEME_DANGER)
                ->setClick('requestByAjax')
                ->setConfirm($this->translator->trans('Are you sure'))
                ->setArgs(
                    [
                        'id'      => $args->item['id'],
                        'refresh' => true,
                    ]
                ),
        ];
    }

    /**
     * Preview record
     *
     * @Route("/bsw-config/preview", name="app_bsw_config_preview")
     * @Access()
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

    /**
     * @return array
     */
    public function configuredFilterAnnotation()
    {
        return [
            'key'   => [
                'label' => 'Key',
                'field' => 'key',
            ],
            'value' => [
                'label' => 'Value',
                'field' => 'value',
            ],
        ];
    }

    /**
     * @return array
     */
    public function configuredAnnotation()
    {
        return [
            'id'    => [
                'width'  => 80,
                'align'  => Abs::POS_CENTER,
                'render' => Abs::RENDER_CODE,
            ],
            'key'   => [
                'width' => 200,
                'align' => Abs::POS_RIGHT,
                'html'  => true,
            ],
            'value' => [
                'width' => 500,
                'html'  => true,
            ],
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function configuredPreviewData(Arguments $args): array
    {
        $list = [];
        $id = 1;
        foreach ($this->cnf as $key => $value) {
            $list[] = compact('id', 'key', 'value');
            $id += 1;
        }

        $key = $args->condition['key']['value'] ?? null;
        if ($key) {
            $list = Helper::arraySearchFilter($list, $key, false, 'key');
        }

        $value = $args->condition['value']['value'] ?? null;
        if ($value) {
            $list = Helper::arraySearchFilter($list, $value, false, 'value');
        }

        return $list;
    }

    /**
     * @param Arguments $args
     *
     * @return Button[]
     */
    public function configuredRecordOperates(Arguments $args): array
    {
        return [
            (new Button('Persistence', 'app_bsw_config_persistence'))
                ->setArgs(['iframe' => $this->iframe])
                ->appendArgs($this->clonePreviewToForm($args->hooked)),
        ];
    }

    /**
     * Preview record - configured
     *
     * @Route("/bsw-configured/preview", name="app_bsw_configured_preview")
     * @Access(same="app_bsw_config_preview")
     *
     * @return Response
     */
    public function configured(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview(['removeOperateInIframe' => false]);
    }
}
