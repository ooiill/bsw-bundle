<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property AbstractController  $container
 * @property TranslatorInterface $translator
 */
trait WebSeo
{
    /**
     * @var bool
     */
    protected $seoWithAppName = true;

    /**
     * @var string
     */
    protected $seoTitle;

    /**
     * @var string
     */
    protected $seoDescription;

    /**
     * @var string
     */
    protected $seoKeywords;

    /**
     * Search engine optimization
     *
     * @return array
     */
    public function seo(): array
    {
        $appName = null;
        if ($this->seoWithAppName) {
            $appName = $this->cnfWithLocate('app_name') ?: 'UnsetAppName';
        }

        /**
         * Trans key
         *
         * @param string $route
         * @param string $type
         *
         * @return string|null
         */
        $get = function (string $route, string $type) use ($appName, &$get) {

            $key = "{$route}_{$type}";
            $message = $this->cnfWithLocate($key) ?? $this->seoLang($key);
            $message = ($message == $key) ? null : $message;

            if ($route !== Abs::TAG_SEO_ACME_KEY) {
                $message = $message ?: $get(Abs::TAG_SEO_ACME_KEY, $type);
            } else {
                $appName = null;
            }

            if ($appName || $message) {
                switch ($type) {
                    case 't':
                        $message = trim("{$appName} - {$message}", '- ');
                        break;
                    case 'd':
                    case 'k':
                        $message = trim("{$appName}, {$message}", ', ');
                        break;
                }
            }

            return $message;
        };

        [$this->seoTitle, $this->seoDescription, $this->seoKeywords] = $this->caching(
            function () use (&$get) {
                return [
                    $this->seoTitle ?: $get($this->route, 't'),
                    $this->seoDescription ?: $get($this->route, 'd'),
                    $this->seoKeywords ?: $get($this->route, 'k'),
                ];
            },
            "seo.{$this->route}"
        );

        return [
            'title'       => $this->seoTitle,
            'description' => $this->seoDescription,
            'keyword'     => $this->seoKeywords,
        ];
    }
}