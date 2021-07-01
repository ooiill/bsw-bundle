<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Module\Bsw\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property Session             $session
 * @property TranslatorInterface $translator
 * @property AdapterInterface    $cache
 */
trait Theme
{
    /**
     * Change skin
     *
     * @Route("/skin", name="app_skin")
     * @Access()
     *
     * @I("key")
     *
     * @return Response
     * @throws
     */
    public function postSkinAction(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $skinMap = [
            'default' => '',
            'terse'   => 'terse',
        ];

        $this->commandCaller('bsw:backend-skin', ['skin' => $skinMap[$args->key] ?? $args->key]);
        $message = $this->twigLang("Change skin to {$args->key}");
        $message = (new Message())
            ->setMessage($message)
            ->setRoute($this->getHistoryRoute(-2))
            ->setClassify(Abs::TAG_CLASSIFY_SUCCESS);

        return $this->responseMessageWithAjax($message);
    }

    /**
     * Change theme
     *
     * @Route("/theme", name="app_theme")
     *
     * @I("key")
     *
     * @return Response
     * @throws
     */
    public function postThemeAction(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $themeMap = [
            'ant-d'   => Abs::CSS_ANT_D,
            'bsw'     => Abs::CSS_ANT_D_BSW,
            'ali-yun' => Abs::CSS_ANT_D_ALI,
            'talk'    => Abs::CSS_ANT_D_TALK,
        ];

        $skCnf = $this->session->get($this->skCnf) ?? [];
        $skCnf['theme'] = $themeMap[$args->key] ?? $args->key;
        $this->session->set($this->skCnf, $skCnf);

        $message = $this->twigLang("Change theme to {$args->key}");
        $message = (new Message())
            ->setMessage($message)
            ->setRoute($this->getHistoryRoute(-2))
            ->setClassify(Abs::TAG_CLASSIFY_SUCCESS);

        return $this->responseMessageWithAjax($message);
    }
}