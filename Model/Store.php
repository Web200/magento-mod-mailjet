<?php

namespace Web200\Mailjet\Model;

/**
 * Class Store
 *
 * @package   Web200\Mailjet\Model
 * @author    Web200 <contact@web200.fr>
 * @copyright 2019 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Store
{
    /**
     * @var int $store_id
     */
    protected $storeId;

    /**
     * Get Store Id
     *
     * @return int
     */
    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    /**
     * Set Store Id
     *
     * @param $storeId
     * @return Store
     */
    public function setStoreId($storeId): Store
    {
        $this->storeId = $storeId;
        return $this;
    }
}
