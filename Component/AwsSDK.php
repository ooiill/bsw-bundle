<?php

namespace Leon\BswBundle\Component;

use Aws\Ses\Exception\SesException;
use Aws\Sns\Exception\SnsException;
use Aws\Credentials\Credentials;
use InvalidArgumentException;
use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;

class AwsSDK
{
    /**
     * @var string
     */
    protected $charset = 'UTF-8';

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var array
     */
    protected $options = ['charset' => 'UTF-8'];

    /**
     * AwsSDK constructor.
     *
     * @param string $region
     * @param string $key
     * @param string $secret
     * @param array  $options
     */
    public function __construct(string $region, string $key, string $secret, array $options)
    {
        $this->region = $region;
        $this->key = $key;
        $this->secret = $secret;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get option from attribute options
     *
     * @param string $key
     *
     * @return mixed
     */
    private function option(string $key)
    {
        if (!isset($this->options[$key])) {
            throw new InvalidArgumentException("Params options need item named {$key} for __construct");
        }

        return $this->options[$key];
    }

    /**
     * Get client for mail
     *
     * @return SesClient
     */
    private function clientForMail()
    {
        return new SesClient(
            [
                'version'     => 'latest',
                'region'      => $this->region,
                'credentials' => new Credentials($this->key, $this->secret),
            ]
        );
    }

    /**
     * Mail Sender
     *
     * @param array  $to
     * @param string $title
     * @param string $content
     *
     * @return array
     */
    public function mailSender(array $to, string $title, string $content): array
    {
        try {
            $result = $this->clientForMail()->sendEmail(
                [
                    'Destination' => [
                        'ToAddresses' => $to,
                    ],
                    'Message'     => [
                        'Subject' => [
                            'Charset' => $this->charset,
                            'Data'    => $title,
                        ],
                        'Body'    => [
                            'Html' => [
                                'Charset' => $this->charset,
                                'Data'    => $content,
                            ],
                        ],
                    ],
                    'Source'      => $this->option('mail_sender'),
                ]
            );

            return [$result->get("MessageId"), null];

        } catch (SesException $error) {

            $message = $error->getAwsErrorMessage();
            $message = empty($message) ? $error->getMessage() : $message;

            return [null, $message];
        }
    }

    /**
     * Get client for mail
     *
     * @return SnsClient
     */
    private function clientForSms()
    {
        return new SnsClient(
            [
                'version'     => 'latest',
                'region'      => $this->region,
                'credentials' => new Credentials($this->key, $this->secret),
            ]
        );
    }

    /**
     * Sms Sender
     *
     * @param array  $to
     * @param string $message
     *
     * @return array
     */
    public function smsSender(array $to, string $message): array
    {
        $result = [];

        $client = $this->clientForSms();
        $client->setSMSAttributes(['attributes' => ['DefaultSMSType' => 'Transactional']]);

        foreach ($to as $phone) {

            try {
                $res = $client->publish(
                    [
                        'Message'     => $message,
                        'PhoneNumber' => $phone,
                    ]
                );

                $result[$phone] = [$res->get("MessageId"), null];

            } catch (SnsException $error) {

                $message = $error->getAwsErrorMessage();
                $message = empty($message) ? $error->getMessage() : $message;

                $result[$phone] = [null, $message];
            }
        }

        return $result;
    }
}