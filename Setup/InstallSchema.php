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
namespace Web200\Mailjet\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table = $setup->getTable('newsletter_subscriber');

        $setup->getConnection()->addColumn(
            $table,
            'subscriber_firstname',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 60,
                'comment' => 'First Name'
            ]
        );
        $setup->getConnection()->addColumn(
            $table,
            'subscriber_lastname',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 60,
                'comment' => 'Last Name'
            ]
        );
        $setup->getConnection()->addColumn(
            $table,
            'subscriber_dob',
            [
                'type' => Table::TYPE_DATE,
                'comment' => 'Dob'
            ]
        );
        $installer->endSetup();
    }
}
