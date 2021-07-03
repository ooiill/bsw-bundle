<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorNoRecord;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Predis\Client;

/**
 * @property Client $redis
 */
trait ThirdMessage
{
    /**
     * Third message
     *
     * @Route("/third-message", name="app_third_message")
     *
     * @return Response
     * @throws
     */
    public function thirdMessageAction(): Response
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        $target = $this->redis->rpop($this->cnf->third_message_key);
        $target = Helper::parseJsonString($target);

        if (empty($target) || empty($target['content']) || empty($target['classify'])) {
            return $this->failedAjax(new ErrorNoRecord());
        }

        $message = (new Message())
            ->setCode($target['code'] ?? Response::HTTP_OK)
            ->setMessage($target['content'])
            ->setRoute($target['url'] ?? null)
            ->setArgs($target['args'] ?? [])
            ->setClassify($target['classify'])
            ->setType($target['type'] ?? Abs::TAG_TYPE_MESSAGE)
            ->setSets($target['data'] ?? [])
            ->setDuration($target['duration'] ?? null);

        return $this->responseMessageWithAjax($message);
    }
}