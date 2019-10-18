<?php

namespace Web200\Mailjet\Plugin;

use Magento\Framework\Mail\Template\TransportBuilder;
use Web200\Mailjet\Model\Store as StoreModel;

/**
 * Class Store
 *
 * @package   Web200\Mailjet\Plugin
 * @author    Web200 <contact@web200.fr>
 * @copyright 2019 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Store
{
    /**
     * @var StoreModel $storeModel
     */
    protected $storeModel;

    /**
     * Store constructor.
     *
     * @param StoreModel $storeModel
     */
    public function __construct(
        StoreModel $storeModel
    ) {
        $this->storeModel = $storeModel;
    }

    /**
     * @param TransportBuilder $transportBuilder
     * @param                  $templateOptions
     * @return array
     */
    public function beforeSetTemplateOptions(
        TransportBuilder $transportBuilder,
        $templateOptions
    ): array {
        if (array_key_exists('store', $templateOptions)) {
            $this->storeModel->setStoreId($templateOptions['store']);
        } else {
            $this->storeModel->setStoreId(null);
        }

        return [$templateOptions];
    }
}
