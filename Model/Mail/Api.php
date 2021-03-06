<?php

namespace Web200\Mailjet\Model\Mail;

use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Web200\Mailjet\Helper\Config;
use Web200\Mailjet\Logger\Logger;
use Web200\Mailjet\Model\Store as StoreModel;
use Web200\Mailjet\Model\Webservice\Email as MailjetEmail;
use Zend\Mail\Address as ZendMailAddress;
use Zend\Mail\AddressList as ZendMailAddressList;
use Zend\Mail\Message;

/**
 * Class Api
 *
 * @package   Web200\Mailjet\Model\Mail
 * @author    Web200 <contact@web200.fr>
 * @copyright 2019 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Api
{
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var StoreModel
     */
    protected $storeModel;
    /**
     * Description $logger field
     *
     * @var Logger $logger
     */
    protected $logger;

    /**
     * Api constructor.
     *
     * @param Json       $json
     * @param StoreModel $storeModel
     * @param Config     $config
     * @param Logger     $logger
     */
    public function __construct(
        Json $json,
        StoreModel $storeModel,
        Config $config,
        Logger $logger
    ) {
        $this->json       = $json;
        $this->config     = $config;
        $this->storeModel = $storeModel;
        $this->logger     = $logger;
    }

    /**
     * Send Mail
     *
     * @param TransportInterface $transport
     */
    public function sendMail(TransportInterface $transport)
    {
        $templateId    = 0;
        $mailVariables = [];

        $mailjetEmail = new MailjetEmail($this->logger, $this->config);
        $mailjetEmail->setStoreId($this->storeModel->getStoreId());

        $message = Message::fromString($transport->getMessage()->getRawMessage());
        try {
            $bodyVariables = $this->json->unserialize($this->getBody($message));
            if (isset($bodyVariables['template_id'])) {
                $templateId = (int)$bodyVariables['template_id'];
            }
            if (isset($bodyVariables['variables'])) {
                $mailVariables = $bodyVariables['variables'];
            }
            $mailjetEmail->setVariables($mailVariables);
            $mailjetEmail->setTemplateId($templateId);
        } catch (\Exception $e) {
        }

        if ($templateId === 0) {
            $mailjetEmail->setSubject($message->getSubject());
            $mailjetEmail->setHtmlPart($this->getBody($message));
            //$mailjetEmail->setTextPart('test');
        }

        /** @var ZendMailAddress $address */
        foreach ($message->getFrom() as $address) {
            $mailjetEmail->setFromEmail($address->getEmail());
            $mailjetEmail->setFromName($address->getName());
        }

        $to = [];
        foreach ($message->getTo() as $address) {
            if ($this->config->testIsActive()) {
                $to[] = [
                    'Email' => $this->config->getTestEmail(),
                    'Name' => 'Test'
                ];
            } else {
                $to[] = [
                    'Email' => $address->getEmail(),
                    'Name' => $address->getName()
                ];
            }
        }

        $bcc = [];
        foreach ($message->getBcc() as $address) {
            if (!$this->config->testIsActive()) {
                $bcc[] = [
                    'Email' => $address->getEmail(),
                    'Name' => $address->getName()
                ];
            }
        }

        $cc = [];
        foreach ($message->getCc() as $address) {
            if (!$this->config->testIsActive()) {
                $cc[] = [
                    'Email' => $address->getEmail(),
                    'Name' => $address->getName()
                ];
            }
        }

        if (!$message->getReplyTo()->count()) {
            $returnPathEmail = $message->getFrom()->count() ? $message->getFrom() : $this->getFromEmailAddress();
            if (is_string($returnPathEmail)) {
                $mailjetEmail->setReplyToEmail($returnPathEmail);
            } elseif ($returnPathEmail instanceof ZendMailAddressList) {
                foreach ($returnPathEmail as $address) {
                    $mailjetEmail->setReplyToEmail($address->getEmail());
                    $mailjetEmail->setReplyToName($address->getName());
                }
            }
        }

        $mailjetEmail->setTo($to);
        if (!empty($bcc)) {
            $mailjetEmail->setBcc($bcc);
        }
        if (!empty($cc)) {
            $mailjetEmail->setCc($cc);
        }
        $mailjetEmail->send();
    }

    /**
     * Get Body
     *
     * @param $message
     * @return mixed
     */
    protected function getBody($message): string
    {
        if ($message->getEncoding() === 'ASCII') {
            return quoted_printable_decode($message->getBody());
        }
        return $message->getBody();
    }
}
