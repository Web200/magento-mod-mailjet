<?php

declare(strict_types=1);

namespace Web200\Mailjet\Model\Webservice;

use \Mailjet\Resources;

/**
 * Class Contact
 *
 * @package   Web200\Mailjet\Model\Webservice
 * @author    Web200 <contact@web200.fr>
 * @copyright 2019 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Contact extends Webservice
{
    /**
     * @var string
     */
    protected $apiVersion = 'v3';
    /**
     * Adds the contact and resets the unsub status to false
     */
    public const ACTION_ADD_FORCE = 'addforce';
    /**
     * Unsubscribes a contact from the list
     */
    public const ACTION_UNSUB = 'unsub';

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

        if ($this->config->getContactList() === '') {
            $this->logger->error('Contact List empty');
            return false;
        }

        try {
            $api      = $this->initApi();
            $body     = [
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

//    /**
//     * Get Contact
//     *
//     * @return bool
//     */
//    public function getContacts()
//    {
//        try {
//            $api      = $this->initApi();
//            $body     = [
//                'ContactsLists' => $this->config->getContactList(),
//                'filters' => [
//                    'Limit' => 100,
//                    'IsExcludedFromCampaigns' => true,
//                    'Sort' => 'LastActivityAt Desc'
//                ]
//            ];
//            $response = $api->get(Resources::$Contact, $body);
//            if (!$response->success()) {
//                $this->logger->error($response->getReasonPhrase());
//            }
//        } catch (\Exception $e) {
//            $this->logger->error($e->getMessage());
//            return false;
//        }
//    }
}
