<?php

namespace Newance\Training\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Training upgrade schema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            /**
             * Add column `subscription_form` to table 'newance_training_post'
             */
            $installer->getConnection()->addColumn(
                $installer->getTable('newance_training_post'),
                'subscription_form',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => null,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'Is Subscription Form Active',
                ]
            );
        }

        /**
         * Create table 'newance_training_brand'
         */
        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('newance_training_brand')
            )->addColumn(
                'brand_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Brand ID'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand Title'
            )->addColumn(
                'meta_keywords',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Brand Meta Keywords'
            )->addColumn(
                'meta_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Brand Meta Description'
            )->addColumn(
                'identifier',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => true, 'default' => null],
                'Brand String Identifier'
            )->addColumn(
                'content_heading',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand Content Heading'
            )->addColumn(
                'content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Brand Content'
            )->addColumn(
                'path',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand Path'
            )->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Brand Position'
            )->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'Is Brand Active'
            )->addIndex(
                $installer->getIdxName('newance_training_brand', ['identifier']),
                ['identifier']
            )->addIndex(
                $setup->getIdxName(
                    $installer->getTable('newance_training_brand'),
                    ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->setComment(
                'Newance Training Brand Table'
            );
            $installer->getConnection()->createTable($table);

            /**
             * Create table 'newance_training_brand_store'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('newance_training_brand_store')
            )->addColumn(
                'brand_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Brand ID'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addIndex(
                $installer->getIdxName('newance_training_brand_store', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('newance_training_brand_store', 'brand_id', 'newance_training_brand', 'brand_id'),
                'brand_id',
                $installer->getTable('newance_training_brand'),
                'brand_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('newance_training_brand_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Newance Training Brand To Store Linkage Table'
            );
            $installer->getConnection()->createTable($table);

            /**
             * Create table 'newance_training_post_brand'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('newance_training_post_brand')
            )->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Post ID'
            )->addColumn(
                'brand_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Brand ID'
            )->addIndex(
                $installer->getIdxName('newance_training_post_brand', ['brand_id']),
                ['brand_id']
            )->addForeignKey(
                $installer->getFkName('newance_training_post_brand', 'post_id', 'newance_training_post', 'post_id'),
                'post_id',
                $installer->getTable('newance_training_post'),
                'post_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('newance_training_post_brand', 'brand_id', 'newance_training_brand', 'brand_id'),
                'brand_id',
                $installer->getTable('newance_training_brand'),
                'brand_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Newance Training Post To Brand Linkage Table'
            );
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            /**
             * Add column `brand_img` to table 'newance_training_brand'
             */
            $installer->getConnection()->addColumn(
                $installer->getTable('newance_training_brand'),
                'brand_img',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Brand Image',
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.3') < 0) {
            /**
             * Add column `home_img` to table 'newance_training_post'
             */
            $installer->getConnection()->addColumn(
                $installer->getTable('newance_training_post'),
                'home_img',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Home Banner Image',
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.4') < 0) {
            /**
             * Add column `home_short_content` to table 'newance_training_post'
             */
            $installer->getConnection()->addColumn(
                $installer->getTable('newance_training_post'),
                'home_short_content',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => true,
                    'comment' => 'Short Content Home',
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.5') < 0) {
            /**
             * Add column `banner_img` to table 'newance_training_brand'
             */
            $installer->getConnection()->addColumn(
                $installer->getTable('newance_training_brand'),
                'banner_img',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Banner Image',
                ]
            );
        }

        $installer->endSetup();
    }
}
