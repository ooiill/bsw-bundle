<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Module\Scene\Message;
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
trait Language
{
    /**
     * Change language
     *
     * @Route("/i18n", name="app_language")
     *
     * @I("key")
     *
     * @return Response
     * @throws
     */
    public function postLanguageAction(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $language = $this->moduleHeaderLanguage();
        if (!isset($language[$args->key])) {
            return $this->failedAjax(new ErrorParameter());
        }

        $this->popCache(['app_admin_menu']);
        $this->session->set(Abs::TAG_SESSION_LANG, $args->key);

        $message = $this->translator->trans(
            'Switch lang success, current {{ lang }}',
            ['{{ lang }}' => $args->key],
            'messages',
            $args->key
        );

        $message = (new Message())
            ->setMessage($message)
            ->setRoute($this->getHistoryRoute(-2))
            ->setClassify(Abs::TAG_CLASSIFY_SUCCESS);

        return $this->responseMessageWithAjax($message);
    }
}