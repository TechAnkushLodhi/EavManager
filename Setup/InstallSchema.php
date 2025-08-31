<?php
/**
 * Copyright © Icecube Digital All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Icecube\EavManager\Setup;
 
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
 
        // Table Creation
        if (!$setup->tableExists('icecube_eav_manager')) {
            $table = $setup->getConnection()->newTable($setup->getTable('icecube_eav_manager'))
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true,
                ], 'Entity ID')
                ->addColumn('entity_type_id', Table::TYPE_SMALLINT, null, [
                    'unsigned' => true,
                    'nullable' => false,
                ], 'Entity Type ID')
                ->addColumn('attribute_id', Table::TYPE_SMALLINT, null, [
                    'unsigned' => true,
                    'nullable' => false,
                ], 'Attribute ID')
                ->addColumn('store_view_id', Table::TYPE_TEXT, 255, [
                    'nullable' => false,
                ], 'Store View ID')
                ->addColumn('customer_form_ids', Table::TYPE_TEXT, 255, [
                    'nullable' => false,
                ], 'Customer Form IDs')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE
                ], 'Updated At')
                // Foreign Key Constraints
                ->addForeignKey(
                    $setup->getFkName('icecube_eav_manager', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
                    'entity_type_id',
                    $setup->getTable('eav_entity_type'),
                    'entity_type_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName('icecube_eav_manager', 'attribute_id', 'eav_attribute', 'attribute_id'),
                    'attribute_id',
                    $setup->getTable('eav_attribute'),
                    'attribute_id',
                    Table::ACTION_CASCADE
                );
            $setup->getConnection()->createTable($table);
        }
 
        $setup->endSetup();
    }
}
 