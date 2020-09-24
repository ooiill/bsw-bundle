<?php

namespace Leon\BswBundle\EventListener;

use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Entity\Enum;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * LocaleSubscriber constructor.
     *
     * @param string $defaultLocale
     */
    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @inheritdoc
     *
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $lang = $request->headers->get(Abs::TAG_SESSION_LANG);
        $lang = $lang ?: $request->getSession()->get(Abs::TAG_SESSION_LANG);

        $locale = $lang ? str_replace('-', '_', strtolower($lang)) : false;
        $locale = Enum::LANG_TO_LOCALE[$locale] ?? false;

        if ($locale) {
            $request->setLocale($locale);
        } else {
            $request->setLocale($this->defaultLocale);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}