<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Connection;

/**
 * @property TranslatorInterface $translator
 */
trait SiteIndex
{
    /**
     * @return array
     */
    public function siteIndexAnnotation()
    {
        return [
            'id'    => [
                'width'  => 80,
                'align'  => Abs::POS_CENTER,
                'render' => Abs::RENDER_CODE,
            ],
            'name'  => [
                'width'  => 200,
                'align'  => Abs::POS_RIGHT,
                'render' => Abs::RENDER_CODE,
            ],
            'value' => [
                'width' => 500,
            ],
        ];
    }

    /**
     * @return string
     */
    public function siteIndexWelcome(): string
    {
        return $this->messageLang(
            "Welcome {{ user }}, today is {{ today }}",
            [
                '{{ user }}'  => $this->usr('usr_account'),
                '{{ today }}' => date(Abs::FMT_DAY),
            ]
        );
    }

    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function siteIndexPreviewData(Arguments $args): array
    {
        /**
         * @var Connection $pdo
         */
        $pdo = $this->pdo();
        $version = $pdo->fetchAssoc('SELECT VERSION() AS version');

        $list = [
            [
                'name'  => $this->twigLang('Server protocol'),
                'value' => $_SERVER['SERVER_PROTOCOL'],
            ],
            [
                'name'  => $this->twigLang('Gateway interface'),
                'value' => $_SERVER['GATEWAY_INTERFACE'],
            ],
            [
                'name'  => $this->twigLang('Server software'),
                'value' => $_SERVER['SERVER_SOFTWARE'],
            ],
            [
                'name'  => $this->twigLang('Service address'),
                'value' => $_SERVER['SERVER_ADDR'],
            ],
            [
                'name'  => $this->twigLang('Service port'),
                'value' => $_SERVER['SERVER_PORT'],
            ],
            [
                'name'  => $this->twigLang('Remote address'),
                'value' => $_SERVER['REMOTE_ADDR'],
            ],

            [
                'name'  => $this->twigLang('PHP version'),
                'value' => PHP_VERSION,
            ],
            [
                'name'  => $this->twigLang('Zend version'),
                'value' => Zend_Version(),
            ],
            [
                'name'  => $this->twigLang('MySQL version'),
                'value' => current($version),
            ],
            [
                'name'  => $this->twigLang('PHP uname'),
                'value' => php_uname(),
            ],
            [
                'name'  => $this->twigLang('Http user agent'),
                'value' => $_SERVER['HTTP_USER_AGENT'],
            ],
            [
                'name'  => $this->twigLang('Backend framework'),
                'value' => 'Symfony ' . $this->kernel::VERSION,
            ],
            [
                'name'  => $this->twigLang('Frontend framework'),
                'value' => 'Ant Design for Vue',
            ],
        ];

        $index = 0;
        foreach ($list as &$item) {
            $item['id'] = ++$index;
        }

        return $list;
    }

    /**
     * Welcome page
     *
     * @Route("/", name="app_site_index")
     * @Access()
     *
     * @return Response
     */
    public function siteIndex(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        return $this->showPreview();
    }
}