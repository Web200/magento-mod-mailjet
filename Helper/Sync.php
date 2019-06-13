<?php
/**
 * Web200_Mailjet Magento component
 *
 * @category    Web200
 * @package     Web200_Mailjet
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200 (https://www.web200.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Web200\Mailjet\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Web200\Mailjet\Helper\Config;
use Web200\Mailjet\Logger\Logger;
use \Mailjet\Resources;

class Sync extends AbstractHelper
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Sync constructor.
     * @param Logger $logger
     * @param Config $config
     * @param Context $context
     */
    public function __construct(
        Logger $logger,
        Config $config,
        Context $context
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->logger = $logger;
    }

    public function update($subscribe, $email, $name, $properties)
    {
        $email = trim($email);
        if ($email === '') {
            return false;
        }

        if (!$this->checkLogin()) {
            return false;
        }

        try {
            $api = new \Mailjet\Client(
                $this->config->getApiKeyPublic(),
                $this->config->getApiKeyPrivate()
            );
            $body = [
                'ContactsLists' => [
                    [
                        'ListID' => $this->config->getContactList(),
                        'action' => $subscribe ? 'addforce' : 'unsub'
                    ]
                ],
                'Contacts' => [
                    [
                        'Email' => $email,
                        'Name' => $name,
                        'Properties' => $properties
                    ]
                ]
            ];
            $response = $api->post(Resources::$ContactManagemanycontacts, ['body' => $body]);
            if (!$response->success()) {
                $this->logger->error($response->getReasonPhrase());
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    private function checkLogin()
    {
        if ($this->config->getApiKeyPublic() === '') {
            $this->logger->error('Api Public Key empty');
            return false;
        }
        if ($this->config->getApiKeyPrivate() === '') {
            $this->logger->error('Api Secret Key empty');
            return false;
        }
        if ($this->config->getContactList() === '') {
            $this->logger->error('Contact List empty');
            return false;
        }
        return true;
    }
}
