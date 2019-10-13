<?php

declare(strict_types=1);

namespace Web200\Mailjet\Plugin\Template;

use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Web200\Mailjet\Model\Webservice\Email as MailjetEmail;
use Zend\Mail\Address as ZendMailAddress;
use Zend\Mail\Message;

/**
 * Class Send
 *
 * @package     Web200\Mailjet\Plugin\Template
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class Send
{
    /**
     * @var MailjetEmail
     */
    protected $mailjetEmail;
    /**
     *
     * @var Json json
     */
    protected $json;

    /**
     * MailTransport constructor.
     *
     * @param MailjetEmail $mailjetEmail
     * @param Json         $json
     */
    public function __construct(
        MailjetEmail $mailjetEmail,
        Json $json
    ) {
        $this->mailjetEmail = $mailjetEmail;
        $this->json         = $json;
    }

    /**
     * Override Send Message
     *
     * @param TransportInterface $transport
     * @param callable           $proceed
     * @return
     */
    public function aroundSendMessage(TransportInterface $transport, callable $proceed)
    {
        $templateId    = 0;
        $mailVariables = [];
        try {
            $message       = Message::fromString($transport->getMessage()->getRawMessage());
            $bodyVariables = $this->json->unserialize($message->getBody());
        } catch (\Exception $e) {
            return $proceed();
        }
        if (isset($bodyVariables['template_id'])) {
            $templateId = (int)$bodyVariables['template_id'];
        }
        if ($templateId === 0) {
            return $proceed();
        }

        if (isset($bodyVariables['variables'])) {
            $mailVariables = $bodyVariables['variables'];
        }

        /** @var ZendMailAddress $address */
        foreach ($message->getFrom() as $address) {
            $this->mailjetEmail->setFromEmail($address->getEmail());
            $this->mailjetEmail->setFromName($address->getName());
        }

        $to = [];
        foreach ($message->getTo() as $address) {
            $to[] = [
                'Email' => $address->getEmail(),
                'Name' => $address->getName()
            ];
        }

        $this->mailjetEmail->setVariables($mailVariables);
        $this->mailjetEmail->setTo($to);
        $this->mailjetEmail->setTemplateId($templateId);
        $this->mailjetEmail->send();
    }
}
