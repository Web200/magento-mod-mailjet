<?php

declare(strict_types=1);

namespace Web200\Mailjet\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 *
 * @category    Class
 * @package     Web200\Mailjet\Helper
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class Config extends AbstractHelper
{
    public const PATH_ACTIVE = 'mailjet/general/active';
    public const PATH_API_KEY_PUBLIC = 'mailjet/general/api_key_public';
    public const PATH_API_KEY_PRIVATE = 'mailjet/general/api_key_private';
    public const PATH_CONTACT_LIST = 'mailjet/general/contact_list';
    public const PATH_TEST_IS_ACTIVE = 'mailjet/test_mode/is_active';
    public const PATH_TEST_EMAIL = 'mailjet/test_mode/email';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     *
     * @param EncryptorInterface    $encryptor
     * @param ScopeConfigInterface  $scopeConfig
     * @param Context               $context
     */
    public function __construct(
        EncryptorInterface $encryptor,
        ScopeConfigInterface $scopeConfig,
        Context $context
    ) {
        parent::__construct($context);

        $this->encryptor    = $encryptor;
        $this->scopeConfig  = $scopeConfig;
    }

    /**
     * Is module active ?
     *
     * @param null $storeId
     * @return bool
     */
    public function active($storeId = null): bool
    {
        return (bool)$this->getConfigValue(self::PATH_ACTIVE, $storeId);
    }

    /**
     * get Api Public Key
     *
     * @param null $storeId
     * @return string
     */
    public function getApiKeyPublic($storeId = null): string
    {
        return (string)$this->getConfigValue(self::PATH_API_KEY_PUBLIC, $storeId);
    }

    /**
     * get Api Secret Key
     *
     * @param null $storeId
     * @return string
     */
    public function getApiKeyPrivate($storeId = null): string
    {
        return (string)$this->encryptor->decrypt($this->getConfigValue(self::PATH_API_KEY_PRIVATE, $storeId));
    }

    /**
     * get Contact List
     *
     * @param null $storeId
     * @return string
     */
    public function getContactList($storeId = null): string
    {
        return (string)$this->getConfigValue(self::PATH_CONTACT_LIST, $storeId);
    }

    /**
     * Is Test active ?
     *
     * @param null $storeId
     * @return bool
     */
    public function testIsActive($storeId = null): bool
    {
        return (bool)$this->getConfigValue(self::PATH_TEST_IS_ACTIVE, $storeId);
    }

    /**
     * get Test Email
     *
     * @param null $storeId
     * @return string
     */
    public function getTestEmail($storeId = null): string
    {
        return (string)$this->getConfigValue(self::PATH_TEST_EMAIL, $storeId);
    }

    /**
     * Get Config value
     *
     * @param        $path
     * @param null   $storeId
     * @param string $scope
     * @return string
     */
    public function getConfigValue($path, $storeId = null, $scope = ScopeInterface::SCOPE_STORES): ?string
    {
        return $this->scopeConfig->getValue($path, $scope, $storeId);
    }
}
