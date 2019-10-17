<?php

declare(strict_types=1);

namespace Web200\Mailjet\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @category    Class
 * @package     Web200\Mailjet\Model
 * @author      Web200 Team <contact@web200.fr>
 * @copyright   Web200
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link        https://www.web200.fr/
 */
class Config
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
     * @param EncryptorInterface   $encryptor
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        EncryptorInterface $encryptor,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->encryptor   = $encryptor;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Is module active ?
     *
     * @return bool
     */
    public function active(): bool
    {
        return (bool)$this->getConfigValue(self::PATH_ACTIVE);
    }

    /**
     * get Api Public Key
     *
     * @return string
     */
    public function getApiKeyPublic(): string
    {
        return (string)$this->getConfigValue(self::PATH_API_KEY_PUBLIC);
    }

    /**
     * get Api Secret Key
     *
     * @return string
     */
    public function getApiKeyPrivate(): string
    {
        return (string)$this->encryptor->decrypt($this->getConfigValue(self::PATH_API_KEY_PRIVATE));
    }

    /**
     * get Contact List
     *
     * @return string
     */
    public function getContactList(): string
    {
        return (string)$this->getConfigValue(self::PATH_CONTACT_LIST);
    }

    /**
     * Is Test active ?
     *
     * @return bool
     */
    public function testIsActive(): bool
    {
        return (bool)$this->getConfigValue(self::PATH_TEST_IS_ACTIVE);
    }

    /**
     * get Test Email
     *
     * @return string
     */
    public function getTestEmail(): string
    {
        return (string)$this->getConfigValue(self::PATH_TEST_EMAIL);
    }

    /**
     * Get Config value
     *
     * @param $path
     * @param string $scope
     * @return string
     */
    public function getConfigValue($path, $scope = ScopeInterface::SCOPE_STORES): ?string
    {
        return $this->scopeConfig->getValue($path, $scope);
    }
}
