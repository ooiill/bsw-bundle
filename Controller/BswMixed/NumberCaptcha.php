<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Psr\Log\LoggerInterface;

/**
 * @property Session|SessionInterface $session
 * @property LoggerInterface          $logger
 */
trait NumberCaptcha
{
    /**
     * Show captcha
     *
     * @Route("/captcha", name="app_captcha")
     *
     * @return Response|bool
     */
    public function numberCaptcha()
    {
        $digit = intval($this->getArgs('digit') ?? $this->parameter('backend_captcha_digit'));
        if ($digit < 3 || $digit > 6) {
            $digit = 4;
        }

        $colors = [
            [236, 245, 255],
            [255, 239, 213],
            [255, 240, 245],
            [230, 230, 250],
            [245, 245, 220],
            [255, 250, 250],
        ];

        $builder = new CaptchaBuilder(null, new PhraseBuilder($digit));
        $builder->setBackgroundColor(...$colors[rand(0, 5)]);
        $builder->build(150, 50);
        $this->session->set($this->skCaptcha, $builder->getPhrase());

        header('Content-type: image/jpeg');
        $builder->output();

        return new Response('', 200, ['Content-type' => 'image/jpeg']);
    }
}