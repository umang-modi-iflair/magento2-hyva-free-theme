<?php
namespace Newance\Training\Model\ResourceModel;

/**
 * Training subscriber resource model
 */
class Subscriber extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('newance_training_post_subscriber', 'subscriber_id');
    }
}
