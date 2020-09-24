<?php

namespace Leon\BswBundle\Controller\Traits;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Leon\BswBundle\Component\AwsSDK;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Error\Entity\ErrorException;
use Leon\BswBundle\Module\Error\Entity\ErrorThirdService;
use PHPMailer\PHPMailer\PHPMailer;
use Qcloud\Sms\SmsSingleSender;
use Symfony\Component\HttpFoundation\Response;
use Mailgun\Mailgun;
use Monolog\Logger;
use Exception;

/**
 * @property Logger $logger
 */
trait Sns
{
    /**
     * Ali cloud for sms
     *
     * @param string $nationCode
     * @param string $phone
     * @param string $signature
     * @param string $tplCode
     * @param array  $args
     * @param string $app
     *
     * @return Response|bool
     * @throws
     */
    public function smsAli(
        string $nationCode,
        string $phone,
        string $signature,
        string $tplCode,
        array $args,
        string $app = null
    ) {

        $client = AlibabaCloud::accessKeyClient(
            $this->parameterInOrderByEmpty(["ali_sms_key{$app}", 'ali_key']),
            $this->parameterInOrderByEmpty(["ali_sms_secret{$app}", 'ali_secret'])
        );

        $client->asDefaultClient();
        $client->regionId($this->parameter("ali_sms_region{$app}"));

        try {

            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->timeout($this->cnf->curl_timeout_second)
                ->connectTimeout($this->cnf->curl_timeout_second)
                ->options(
                    [
                        'query' => [
                            'PhoneNumbers'  => "{$nationCode}{$phone}",
                            'SignName'      => $signature,
                            'TemplateCode'  => $tplCode,
                            'TemplateParam' => Helper::jsonStringify($args),
                        ],
                    ]
                )
                ->request()
                ->toArray();

            $message = $result['Message'];

            if ($message != 'OK') {
                $this->logger->error("Ali sms error, {$message}", $result);

                return $this->failed(new ErrorThirdService(), $message);
            }

            return true;

        } catch (ClientException|ServerException $e) {

            $message = $e->getErrorMessage();
            $this->logger->error("Ali sms exception, {$message}");

            return $this->failed(new ErrorException(), $message);
        }
    }

    /**
     * Tx cloud for sms
     *
     * @param string $nationCode
     * @param string $phone
     * @param string $content
     * @param string $app
     *
     * @return Response|bool
     * @throws
     */
    public function smsTx(string $nationCode, string $phone, string $content, string $app = null)
    {
        try {

            $sender = new SmsSingleSender(
                $this->parameterInOrderByEmpty(["tx_sms_key{$app}", 'tx_key']),
                $this->parameterInOrderByEmpty(["tx_sms_secret{$app}", 'tx_secret'])
            );

            $result = $sender->send(0, $nationCode, $phone, $content);
            $result = Helper::parseJsonString($result);

            $message = $result['errmsg'] ?? $result['ErrorInfo'] ?? 'Unknown error';
            if ($message != 'OK') {
                $this->logger->error("Tx sms error, {$message}", $result);

                return $this->failed(new ErrorThirdService(), $message);
            }

            return true;

        } catch (Exception $e) {

            $message = $e->getMessage();
            $this->logger->error("Tx sms exception, {$message}");

            return $this->failed(new ErrorException(), $message);
        }
    }

    /**
     * Aws cloud for sms
     *
     * @param string $nationCode
     * @param string $phone
     * @param string $content
     * @param string $class
     *
     * @return Response|bool
     * @throws
     */
    public function smsAws(string $nationCode, string $phone, string $content, string $class = AwsSDK::class)
    {
        /**
         * @var AwsSDK $aws
         */
        $aws = $this->component($class);
        [$_, $error] = current($aws->smsSender(["{$nationCode}{$phone}"], $content));

        if ($error) {
            $this->logger->error("Aws sms error, {$error}");

            return $this->failed(new ErrorThirdService(), $error);
        }

        return true;
    }

    /**
     * Aws cloud for email
     *
     * @param string $email
     * @param string $title
     * @param string $content
     * @param string $class
     *
     * @return Response|bool
     * @throws
     */
    public function emailAws(string $email, string $title, string $content, string $class = AwsSDK::class)
    {
        /**
         * @var AwsSDK $aws
         */
        $aws = $this->component($class);

        [$_, $error] = $aws->mailSender([$email], $title, $content);

        if ($error) {
            $this->logger->error("Aws sns error, {$error}");

            return $this->failed(new ErrorThirdService(), $error);
        }

        return true;
    }

    /**
     * [Simple Mail Transfer Protocol] for email
     *
     * @param string $email
     * @param string $title
     * @param string $content
     *
     * @return Response|bool
     * @throws
     */
    public function emailSMTP(string $email, string $title, string $content)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $this->parameter('smtp_host');
            $mail->SMTPAuth = true;
            $mail->Username = $this->parameter('smtp_sender');
            $mail->Password = $this->parameter('smtp_secret');
            $mail->SMTPSecure = 'tls';
            $mail->Port = $this->parameter('smtp_port');

            // recipients
            $mail->setFrom($this->parameter('smtp_sender'));
            $mail->addAddress($email);

            // content
            $mail->isHTML(true);
            $mail->Subject = $title;
            $mail->Body = $content;
            $mail->CharSet = PHPMailer::CHARSET_UTF8;

            $mail->send();

            return true;

        } catch (Exception $e) {

            $error = "{$mail->ErrorInfo}, {$e->getMessage()}";
            $this->logger->error("SMTP email error, {$error}");

            return $this->failed(new ErrorThirdService(), 'Email send failed');
        }
    }

    /**
     * MailGun for email
     *
     * @param string $email
     * @param string $title
     * @param string $content
     *
     * @return Response|bool
     * @throws
     */
    public function emailGun(string $email, string $title, string $content)
    {
        $mg = Mailgun::create(
            $this->parameter('mail_gun_key'),
            $this->parameter('mail_gun_endpoint', 'https://api.mailgun.net')
        );

        try {
            $host = $this->parameter('mail_gun_host');
            $result = $mg->messages()->send(
                $host,
                [
                    'from'    => $this->parameter('mail_gun_from') . "@{$host}",
                    'to'      => $email,
                    'subject' => $title,
                    'html'    => $content,
                ]
            );
        } catch (Exception $e) {
            $this->logger->error("Mail-gun email error, {$e->getMessage()}");

            return $this->failed(new ErrorThirdService(), 'Email send failed');
        }

        return true;
    }
}