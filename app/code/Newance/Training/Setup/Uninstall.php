<?php

namespace Newance\Training\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Training uninstall
 */
class Uninstall implements UninstallInterface
{
    /**
     * Uninstall module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->dropTable($installer->getTable('newance_training_category'));
        $installer->getConnection()->dropTable($installer->getTable('newance_training_category_store'));

        $installer->getConnection()->dropTable($installer->getTable('newance_training_post'));
        $installer->getConnection()->dropTable($installer->getTable('newance_training_post_store'));
        $installer->getConnection()->dropTable($installer->getTable('newance_training_post_category'));
        $installer->getConnection()->dropTable($installer->getTable('newance_training_post_relatedproduct'));
        $installer->getConnection()->dropTable($installer->getTable('newance_training_post_relatedpost'));
        $installer->getConnection()->dropTable($installer->getTable('newance_training_post_subscriber'));

        $installer->endSetup();
    }
}
