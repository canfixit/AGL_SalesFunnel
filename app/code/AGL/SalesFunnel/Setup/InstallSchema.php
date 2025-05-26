<?php
namespace AGL\SalesFunnel\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (!$setup->tableExists('agl_salesfunnel_cart_count')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('agl_salesfunnel_cart_count')
            )
                ->addColumn(
                    'sku',
                    Table::TYPE_TEXT,
                    64,
                    ['nullable' => false, 'primary' => true],
                    'Product SKU'
                )
                ->addColumn(
                    'cart_count',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Cart Count'
                );
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
} 