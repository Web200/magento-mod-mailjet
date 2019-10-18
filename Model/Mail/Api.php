<?php

namespace Web200\Mailjet\Model\Mail;

use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Web200\Mailjet\Helper\Config;
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
     * @var MailjetEmail
     */
    protected $mailjetEmail;
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
     * Api constructor.
     *
     * @param MailjetEmail $mailjetEmail
     * @param Json         $json
     * @param StoreModel   $storeModel
     * @param Config       $config
     */
    public function __construct(
        MailjetEmail $mailjetEmail,
        Json $json,
        StoreModel $storeModel,
        Config $config
    ) {
        $this->mailjetEmail = $mailjetEmail;
        $this->json         = $json;
        $this->config       = $config;
        $this->storeModel   = $storeModel;
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

        $this->mailjetEmail->setStoreId($this->storeModel->getStoreId());

        $message = Message::fromString($transport->getMessage()->getRawMessage());
        try {
            $bodyVariables = $this->json->unserialize($message->getBody());
            if (isset($bodyVariables['template_id'])) {
                $templateId = (int)$bodyVariables['template_id'];
            }
            if (isset($bodyVariables['variables'])) {
                $mailVariables = $bodyVariables['variables'];
            }
            $this->mailjetEmail->setVariables($mailVariables);
            $this->mailjetEmail->setTemplateId($templateId);
        } catch (\Exception $e) {
        }

        if ($templateId === 0) {
            $this->mailjetEmail->setSubject($message->getSubject());
            $this->mailjetEmail->setHtmlPart($message->getBodyText());
            //$this->mailjetEmail->setTextPart('test');
        }

        /** @var ZendMailAddress $address */
        foreach ($message->getFrom() as $address) {
            $this->mailjetEmail->setFromEmail($address->getEmail());
            $this->mailjetEmail->setFromName($address->getName());
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

        if (!$message->getReplyTo()->count()) {
            $returnPathEmail = $message->getFrom()->count() ? $message->getFrom() : $this->getFromEmailAddress();
            if (is_string($returnPathEmail)) {
                $this->mailjetEmail->setReplyToEmail($returnPathEmail);
            } elseif ($returnPathEmail instanceof ZendMailAddressList) {
                foreach ($returnPathEmail as $address) {
                    $this->mailjetEmail->setReplyToEmail($address->getEmail());
                    $this->mailjetEmail->setReplyToName($address->getName());
                }
            }
        }

        $this->mailjetEmail->setTo($to);
        $this->mailjetEmail->send();
    }
}
