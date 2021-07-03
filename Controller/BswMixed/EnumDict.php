<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Select;
use Leon\BswBundle\Component\Reflection;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property TranslatorInterface $translator
 */
trait EnumDict
{
    /**
     * @return array
     */
    public function enumDictFilterAnnotation()
    {
        return [
            'keyword' => [
                'label'  => 'Keyword',
                'field'  => 'keyword',
                'column' => 4,
            ],
            'limit'   => [
                'label'  => 'Limit',
                'field'  => 'limit',
                'column' => 3,
                'type'   => Select::class,
                'enum'   => [
                    0  => Abs::SELECT_ALL_VALUE,
                    5  => 'count ≤ 5',
                    10 => 'count ≤ 10',
                    20 => 'count ≤ 20',
                    30 => 'count ≤ 30',
                    50 => 'count ≤ 50',
                ],
                'value'  => 20,
            ],
        ];
    }

    /**
     * @return array
     */
    public function enumDictAnnotation()
    {
        return [
            'key'  => [
                'width' => 200,
                'align' => Abs::POS_RIGHT,
                'html'  => true,
            ],
            'enum' => [
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
    public function enumDictBeforeHook(Arguments $args): array
    {
        $key = Html::tag('div', $args->original['key'], ['class' => 'bsw-code bsw-long-text']);
        $info = Html::tag(
            'span',
            $args->original['info'],
            [
                'style' => [
                    'display' => 'block',
                    'margin'  => '6px 0',
                    'color'   => '#ccc',
                ],
            ]
        );

        $enum = [];
        foreach ($args->original['enum'] as $k => $v) {
            if (is_array($v)) {
                $v = Helper::printPhpArray($v);
            }
            if (!is_scalar($v)) {
                $v = Abs::NOT_SCALAR;
            }
            $k = Html::tag('div', $k, ['class' => 'ant-tag ant-tag-has-color', 'style' => ['color' => '#1890ff']]);
            $v = Html::tag('div', $v, ['class' => 'bsw-code bsw-long-text']);
            array_push($enum, "{$k} => {$v}");
        }

        $enum = Html::tag(
            'div',
            implode(Abs::LINE_DASHED, $enum),
            ['style' => ['margin' => '0 20px']]
        );

        return [
            'id'   => $args->original['key'],
            'key'  => "{$key}<br>{$info}",
            'enum' => $enum,
        ];
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function enumDictPreviewData(Arguments $args): array
    {
        $reflection = new Reflection();
        $constant = $reflection->getClsConstDoc(static::$enum, true);

        $list = [];
        $limit = $args->condition['limit']['value'] ?? 0;

        $id = 1;
        foreach ($constant as $key => $item) {
            if (empty($item['proto'])) {
                continue;
            }

            $enum = $item['proto']->getValue();
            if ($limit && count($enum) > $limit) {
                continue;
            }

            $enum = $this->enumLang($enum);
            array_push(
                $list,
                [
                    'id'   => $id++,
                    'key'  => $key,
                    'info' => $item['const'],
                    'enum' => $enum,
                ]
            );
        }

        $keyword = $args->condition['keyword']['value'] ?? null;
        if ($keyword) {
            $list = Helper::arraySearchFilter($list, $keyword);
        }

        return $list;
    }

    /**
     * Enum dict
     *
     * @Route("/enum-dict", name="app_enum_dict")
     * @Access()
     *
     * @return Response
     * @throws
     */
    public function enumDict(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview();
    }
}