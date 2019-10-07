<?php

declare(strict_types=1);

namespace Web200\Mailjet\Plugin\Model;

use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Web200\Mailjet\Model\Config;
use Web200\Mailjet\Model\Webservice\Contact;

/**
 * Class SubscriberPlugin
 *
 * @category    Class
 * @package     Web200\Mailjet\Plugin\Model
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class SubscriberPlugin
{
    /**
     * @var Contact
     */
    protected $contact;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Config
     */
    protected $config;

    /**
     * SubscriberPlugin constructor.
     *
     * @param Contact               $contact
     * @param Config                $config
     * @param CustomerRepository    $customerRepository
     * @param CustomerSession       $customerSession
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Contact $contact,
        Config $config,
        CustomerRepository $customerRepository,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager
    ) {
        $this->contact            = $contact;
        $this->customerRepository = $customerRepository;
        $this->customerSession    = $customerSession;
        $this->storeManager       = $storeManager;
        $this->config             = $config;
    }

    /**
     * Sync with Mailjet
     *
     * @param $subscriber
     * @param $customerId
     * @return array
     * @throws LocalizedException
     */
    public function beforeUnsubscribeCustomerById(
        $subscriber,
        $customerId
    ) {
        if ($this->config->active()) {
            try {
                $customer = $this->customerRepository->getById($customerId);
            } catch (NoSuchEntityException $e) {
                return [$customerId];
            }
            $this->contact->update(
                false,
                $customer->getEmail(),
                $customer->getFirstname(),
                $this->buildProperties(
                    [
                        'firstname' => $customer->getFirstname(),
                        'lastname' => $customer->getLastname(),
                        'dob' => $customer->getDob(),
                    ]
                )
            );
        }
        return [$customerId];
    }

    /**
     * Sync with Mailjet
     *
     * @param $subscriber
     * @param $customerId
     * @return array
     * @throws LocalizedException
     */
    public function beforeSubscribeCustomerById(
        $subscriber,
        $customerId
    ) {
        if ($this->config->active()) {
            try {
                $customer = $this->customerRepository->getById($customerId);
            } catch (NoSuchEntityException $e) {
                return [$customerId];
            }
            $this->contact->update(
                true,
                $customer->getEmail(),
                $customer->getFirstname(),
                $this->buildProperties(
                    [
                        'firstname' => $customer->getFirstname(),
                        'lastname' => $customer->getLastname(),
                        'dob' => $customer->getDob(),
                    ]
                )
            );
        }
        return [$customerId];
    }

    /**
     * Sync with Mailjet
     *
     * @param $subscriber
     * @param $email
     * @return array
     */
    public function beforeSubscribe(
        $subscriber,
        $email
    ) {
        if ($this->config->active()) {
            $lastname = $subscriber->getData('subscriber_lastname');
            $firstname = $subscriber->getData('subscriber_firstname');
            $dob = $subscriber->getData('subscriber_dob');
            $this->contact->update(
                true,
                $email,
                $firstname,
                $this->buildProperties(
                    [
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'dob' => $dob
                    ]
                )
            );
        }
        return [$email];
    }

    /**
     * Add Firstname / Lastname / Dob to Subscriber model
     *
     * @param Subscriber $subject
     * @param $subscriber
     * @return mixed
     */
    public function afterSetOrigData(Subscriber $subject, $subscriber)
    {
        if (strlen($subscriber->getData('subscriber_firstname')) !== '') {
            $subscriber->setData('firstname', $subscriber->getData('subscriber_firstname'));
        }
        if (strlen($subscriber->getData('subscriber_lastname')) !== '') {
            $subscriber->setData('lastname', $subscriber->getData('subscriber_lastname'));
        }
        if (strlen($subscriber->getData('subscriber_dob')) !== '') {
            $subscriber->setData('dob', $subscriber->getData('subscriber_dob'));
        }
        return $subscriber;
    }

    /**
     * Build properties
     *
     * @param $properties
     * @return mixed
     */
    protected function buildProperties($properties)
    {
        $final = [];
        foreach ($properties as $key => $value) {
            if ($key === 'dob' && $value !== '') {
                $final['dob'] = $value.'T00:00:00+00:00';
            } else {
                $final[$key] = $value;
            }
        }
        return $final;
    }
}
