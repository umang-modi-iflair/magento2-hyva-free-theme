<?php

namespace Newance\Training\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Training setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'newance_training_post'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('newance_training_post')
        )->addColumn(
            'post_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Post Title'
        )->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Post Meta Keywords'
        )->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Post Meta Description'
        )->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null],
            'Post String Identifier'
        )->addColumn(
            'location',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location'
        )->addColumn(
            'hour',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Hour'
        )->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['nullable' => true],
            'Amount'
        )->addColumn(
            'remaining_places',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Remaining places'
        )->addColumn(
            'content_heading',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Post Content Heading'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Post Content'
        )->addColumn(
            'short_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Short Post Content'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Post Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Post Modification Time'
        )->addColumn(
            'publish_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Post Publish Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Post Active'
        )->addColumn(
            'subscription_form',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Subscription Form Active'
        )->addColumn(
            'featured_img',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Image'
        )->addIndex(
            $installer->getIdxName('newance_training_post', ['identifier']),
            ['identifier']
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('newance_training_post'),
                ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Newance Training Post Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'newance_training_post_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('newance_training_post_store')
        )->addColumn(
            'post_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('newance_training_post_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('newance_training_post_store', 'post_id', 'newance_training_post', 'post_id'),
            'post_id',
            $installer->getTable('newance_training_post'),
            'post_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('newance_training_post_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Newance Training Post To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('newance_training_category')
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Category Title'
        )->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Category Meta Keywords'
        )->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Category Meta Description'
        )->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null],
            'Category String Identifier'
        )->addColumn(
            'content_heading',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Category Content Heading'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Category Content'
        )->addColumn(
            'path',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Category Path'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Category Position'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Category Active'
        )->addIndex(
            $installer->getIdxName('newance_training_category', ['identifier']),
            ['identifier']
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('newance_training_category'),
                ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Newance Training Category Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'newance_training_category_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('newance_training_category_store')
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('newance_training_category_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('newance_training_category_store', 'category_id', 'newance_training_category', 'category_id'),
            'category_id',
            $installer->getTable('newance_training_category'),
            'category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('newance_training_category_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Newance Training Category To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'newance_training_post_category'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('newance_training_post_category')
        )->addColumn(
            'post_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Category ID'
        )->addIndex(
            $installer->getIdxName('newance_training_post_category', ['category_id']),
            ['category_id']
        )->addForeignKey(
            $installer->getFkName('newance_training_post_category', 'post_id', 'newance_training_post', 'post_id'),
            'post_id',
            $installer->getTable('newance_training_post'),
            'post_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('newance_training_post_category', 'category_id', 'newance_training_category', 'category_id'),
            'category_id',
            $installer->getTable('newance_training_category'),
            'category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Newance Training Post To Category Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'newance_training_post_relatedproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('newance_training_post_relatedproduct')
        )->addColumn(
            'post_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Related Product ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $installer->getIdxName('newance_training_post_relatedproduct', ['related_id']),
            ['related_id']
        )->addForeignKey(
            $installer->getFkName('newance_training_post_relatedproduct', 'post_id', 'newance_training_post', 'post_id'),
            'post_id',
            $installer->getTable('newance_training_post'),
            'post_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('newance_training_post_relatedproduct', 'related_id', 'catalog_product_entity', 'entity_id'),
            'related_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Newance Training Post To Product Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'newance_training_post_relatedproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('newance_training_post_relatedpost')
        )->addColumn(
            'post_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Related Post ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $installer->getIdxName('newance_training_post_relatedproduct', ['related_id']),
            ['related_id']
        )->addForeignKey(
            $installer->getFkName('newance_training_post_relatedproduct1', 'post_id', 'newance_training_post', 'post_id'),
            'post_id',
            $installer->getTable('newance_training_post'),
            'post_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('newance_training_post_relatedproduct2', 'related_id', 'newance_training_post', 'post_id'),
            'post_id',
            $installer->getTable('newance_training_post'),
            'post_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Newance Training Post To Post Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'newance_training_post_subscriber'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('newance_training_post_subscriber')
        )->addColumn(
            'subscriber_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Subscriber ID'
        )->addColumn(
            'post_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Post ID'
        )->addColumn(
            'date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            'CURRENT_TIMESTAMP',
            ['nullable' => false],
            'Date'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Name'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Email'
        )->addColumn(
            'phone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Phone'
        )->addColumn(
            'company',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Company'
        )->addColumn(
            'vat',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'VAT Number'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Message'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Subscriber Active'
        )->addIndex(
            $installer->getIdxName('newance_training_post_subscriber', ['post_id']),
            ['post_id']
        )->addForeignKey(
            $installer->getFkName('newance_training_post_subscriber', 'post_id', 'newance_training_post', 'post_id'),
            'post_id',
            $installer->getTable('newance_training_post'),
            'post_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Newance Training Subscriber Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
