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
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config extends AbstractHelper
{
    const PATH_ACTIVE = 'mailjet/general/active';
    const PATH_API_KEY_PUBLIC = 'mailjet/general/api_key_public';
    const PATH_API_KEY_PRIVATE = 'mailjet/general/api_key_private';
    const PATH_CONTACT_LIST = 'mailjet/general/contact_list';
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * Config constructor.
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
        $this->encryptor = $encryptor;
    }

    /**
     * Is module active ?
     * @return bool
     */
    public function active()
    {
        return (bool)$this->getConfigValue(self::PATH_ACTIVE);
    }

    /**
     * get Api Public Key
     * @return bool
     */
    public function getApiKeyPublic()
    {
        return $this->getConfigValue(self::PATH_API_KEY_PUBLIC);
    }

    /**
     * get Api Secret Key
     * @return bool
     */
    public function getApiKeyPrivate()
    {
        return $this->encryptor->decrypt($this->getConfigValue(self::PATH_API_KEY_PRIVATE));
    }

    /**
     * get Contact List
     * @return bool
     */
    public function getContactList()
    {
        return $this->getConfigValue(self::PATH_CONTACT_LIST);
    }

    /**
     * @param $path
     * @param string $scope
     * @return mixed
     */
    public function getConfigValue($path, $scope = ScopeInterface::SCOPE_STORES)
    {
        return $this->scopeConfig->getValue($path, $scope);
    }
}
