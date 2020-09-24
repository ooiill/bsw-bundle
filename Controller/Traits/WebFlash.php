<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\HttpFoundation\Session\Session as Sessions;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Exception;

/**
 * @property Sessions|SessionInterface $session
 */
trait WebFlash
{
    /**
     * Append message
     *
     * @param string $content
     * @param int    $duration
     * @param string $classify
     * @param string $type
     *
     * @throws
     */
    public function appendMessage(
        string $content,
        int $duration = null,
        string $classify = Abs::TAG_CLASSIFY_WARNING,
        string $type = Abs::TAG_TYPE_MESSAGE
    ) {
        if (strpos($content, Abs::FLAG_SQL_ERROR) !== false) {
            throw new Exception($content);
        }

        $message = [
            'type'     => $type,
            'duration' => $duration,
            'classify' => $classify,
            'content'  => Html::cleanHtml($this->messageLang($content)),
        ];
        $message = Helper::arrayBase64EncodeForJs($message);

        // message to flash
        $this->addFlash(Abs::TAG_MESSAGE, Helper::jsonStringify($message));
    }

    /**
     * Append modal
     *
     * @param array $options
     */
    public function appendModal(array $options)
    {
        $options = array_merge(
            [
                'title' => 'Tips',
                'width' => Abs::MEDIA_XS,
            ],
            $options
        );

        foreach (['title', 'content'] as $key) {
            if (!isset($options[$key])) {
                continue;
            }
            if (!($options["{$key}Html"] ?? false)) {
                $options[$key] = Html::cleanHtml($options[$key]);
            }
        }
        $options = Helper::arrayBase64EncodeForJs($options);

        // message to flash
        $this->addFlash(Abs::TAG_MODAL, Helper::jsonStringify($options));
    }

    /**
     * Append result
     *
     * @param array $options
     */
    public function appendResult(array $options)
    {
        $options = array_merge(
            [
                'status' => Abs::RESULT_STATUS_SUCCESS,
                'title'  => 'Operation success',
                'width'  => Abs::MEDIA_XS,
            ],
            $options
        );

        foreach (['title', 'subTitle'] as $key) {
            if (!isset($options[$key])) {
                continue;
            }
            if (!($options["{$key}Html"] ?? false)) {
                $options[$key] = Html::cleanHtml($options[$key]);
            }
        }
        $options = Helper::arrayBase64EncodeForJs($options);

        // message to flash
        $this->addFlash(Abs::TAG_RESULT, Helper::jsonStringify($options));
    }

    /**
     * Get latest message
     *
     * @param string $key
     * @param bool   $jsonDecode
     *
     * @return mixed
     */
    public function latestFlash(string $key, bool $jsonDecode = false)
    {
        $list = $this->session->getFlashBag()->get($key);
        $latest = end($list);

        if (!$latest) {
            return $jsonDecode ? [] : null;
        }

        return $jsonDecode ? Helper::jsonArray($latest) : $latest;
    }
}