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
namespace Web200\Mailjet\Plugin\Model;

use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Web200\Mailjet\Helper\Config;
use Web200\Mailjet\Helper\Sync as SyncHelper;

class SubscriberPlugin
{
    /**
     * @var SyncHelper
     */
    protected $syncHelper;
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
    private $config;

    /**
     * @param SyncHelper $syncHelper
     * @param Config $config
     * @param CustomerRepository $customerRepository
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     */

    public function __construct(
        SyncHelper $syncHelper,
        Config $config,
        CustomerRepository $customerRepository,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager
    ) {

        $this->syncHelper = $syncHelper;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->config = $config;
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
            $this->syncHelper->update(
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
            $this->syncHelper->update(
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
            $this->syncHelper->update(
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
        if (strlen($subscriber->getData('subscriber_firstname')) != '') {
            $subscriber->setData('firstname', $subscriber->getData('subscriber_firstname'));
        }
        if (strlen($subscriber->getData('subscriber_lastname')) != '') {
            $subscriber->setData('lastname', $subscriber->getData('subscriber_lastname'));
        }
        if (strlen($subscriber->getData('subscriber_dob')) != '') {
            $subscriber->setData('dob', $subscriber->getData('subscriber_dob'));
        }
        return $subscriber;
    }

    private function buildProperties($properties)
    {
        $final = [];
        foreach ($properties as $key => $value) {
            if ($key == 'dob' && strlen($value) > 0) {
                $properties['dob'] = $value.'T00:00:00+00:00';
            } else {
                $final[$key] = $value;
            }
        }
        return $properties;
    }
}
