<?php

declare(strict_types=1);

namespace Web200\Mailjet\Model\Webservice;

use Web200\Mailjet\Model\Config;
use Web200\Mailjet\Logger\Logger;
use \Mailjet\Resources;

/**
 * Class Contact
 *
 * @category    Class
 * @package     Web200\Mailjet\Model\Webservice
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class Contact
{
    /**
     * Adds the contact and resets the unsub status to false
     */
    public const ACTION_ADD_FORCE = 'addforce';
    /**
     * Unsubscribes a contact from the list
     */
    public const ACTION_UNSUB = 'unsub';

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Sync constructor.
     *
     * @param Logger  $logger
     * @param Config  $config
     */
    public function __construct(
        Logger $logger,
        Config $config
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Update Subscription
     *
     * @param $subscribe
     * @param $email
     * @param $name
     * @param $properties
     * @return bool
     */
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
                        'action' => $subscribe ? self::ACTION_ADD_FORCE : self::ACTION_UNSUB
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

    /**
     * Get Contact
     *
     * @param $subscribe
     * @param $email
     * @param $name
     * @param $properties
     * @return bool
     */
    public function getContacts()
    {
        if (!$this->checkLogin()) {
            return false;
        }

        try {
            $api = new \Mailjet\Client(
                $this->config->getApiKeyPublic(),
                $this->config->getApiKeyPrivate()
            );
            $body = [
                'ContactsLists' => $this->config->getContactList(),
                'filters' => [
                    'Limit' => 100,
                    'IsExcludedFromCampaigns' => true,
                    'Sort' => 'LastActivityAt Desc'
                ]
            ];
            $response = $api->get(Resources::$Contact, $body);
            if (!$response->success()) {
                $this->logger->error($response->getReasonPhrase());
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Check login information
     *
     * @return bool
     */
    protected function checkLogin(): bool
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
