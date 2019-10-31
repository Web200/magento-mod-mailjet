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
     * @param Json         $json
     * @param StoreModel   $storeModel
     * @param Config       $config
     */
    public function __construct(
        Json $json,
        StoreModel $storeModel,
        Config $config
    ) {
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

        $mailjetEmail = new MailjetEmail();
        $mailjetEmail->setStoreId($this->storeModel->getStoreId());

        $message = Message::fromString($transport->getMessage()->getRawMessage());
        try {
            $bodyVariables = $this->json->unserialize($message->getBody());
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
            $mailjetEmail->setHtmlPart($message->getBodyText());
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
        $mailjetEmail->send();
    }
}
