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
namespace Web200\Mailjet\Plugin\Model\ResourceModel\Subscriber;

use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection;

class CollectionPlugin
{
    /**
     * Add firstname, Lastname, Dob in subscriber Collection
     *
     * @param Collection $subject
     * @param callable $proceed
     * @return Collection
     */
    public function aroundShowCustomerInfo(
        Collection $subject,
        callable $proceed
    ) {
        $subject->getSelect()->joinLeft(
            [
                'customer' => $subject->getTable('customer_entity')
            ],
            'main_table.customer_id = customer.entity_id',
            ['firstname', 'lastname', 'dob']
        );
        return $subject;
    }
}
