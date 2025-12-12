<?php

namespace Newance\Training\Model\ResourceModel\Subscriber;

/**
 * Training subscriber collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Newance\Training\Model\Subscriber', 'Newance\Training\Model\ResourceModel\Subscriber');
        $this->_map['fields']['subscriber_id'] = 'main_table.subscriber_id';
        $this->_map['fields']['post_id'] = 'main_table.post_id';
        $this->_map['fields']['is_active'] = 'main_table.is_active';
    }

    /**
     * Initialize select (add mapping to newance_training_post for post_title)
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->join(
            ['bp' => 'newance_training_post'],
            'main_table.post_id = bp.post_id',
            [
                'post_title' => 'title',
                'post_publish_time' => 'publish_time',
                'subscriber_post_id' => 'post_id'
            ]
        );

        return $this;
    }

    /**
     * Add enable filter to collection
     *
     * @return $this
     */
    public function addActiveFilter()
    {
        return $this->addFieldToFilter('is_active', 1);
    }
}
