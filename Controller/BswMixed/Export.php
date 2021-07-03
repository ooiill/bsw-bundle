<?php

namespace Leon\BswBundle\Controller\BswMixed;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswCommandQueue;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Annotation\Entity\AccessControl as Access;
use Leon\BswBundle\Module\Form\Entity\Upload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

trait Export
{
    /**
     * @return string
     */
    public function exportEntity(): string
    {
        return BswCommandQueue::class;
    }

    /**
     * @return array
     */
    public function exportAnnotationOnly(): array
    {
        $condition = $this->getArgs(['entity', 'query', 'time', 'signature']);
        $condition = array_map('urldecode', $condition);

        return [
            'title'            => true,
            'command'          => [
                'value' => 'mission:export-preview',
                'hide'  => true,
            ],
            'condition'        => [
                'value' => Helper::formatPrintJson($condition, 4, ': '),
                'hide'  => true,
            ],
            'telegramReceiver' => true,
            'cronReuse'        => [
                'value' => 0,
                'hide'  => true,
            ],
        ];
    }

    /**
     * Export record
     *
     * @Route("/export", name="app_export")
     * @Access(same="app_bsw_command_queue_persistence")
     *
     * @return Response
     */
    public function export(): Response
    {
        if (($args = $this->valid()) instanceof Response) {
            return $args;
        }

        $nextRoute = $this->getHistoryRoute(-2);

        return $this->showPersistence(
            [
                'nextRoute'      => $nextRoute,
                'messageHandler' => function (Message $message) {
                    $this->appendResult(
                        [
                            'width'      => 400,
                            'title'      => $this->messageLang('Newly mission queue done'),
                            'status'     => Abs::RESULT_STATUS_SUCCESS,
                            'cancelShow' => true,
                            'okText'     => $this->twigLang('Look up'),
                            'ok'         => 'redirect',
                            'extra'      => ['location' => $this->url('app_bsw_command_queue_preview')],
                        ]
                    );

                    return $message->setMessage('');
                },
            ]
        );
    }
}