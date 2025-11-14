<?php

namespace Newance\Training\Block\Post\View;

use Magento\Store\Model\ScopeInterface;

/**
 * Training post subscribers block
 */
class Subscriber extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Block template file
     * @var string
     */
    protected $_template = 'post/view/subscriber.phtml';

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Return subscribers collection
     *
     * @return void
     */
    public function getSubscriberCollection()
    {
        return $this->getPost()->getSubscribers();
    }

    /**
     * Retrieve true if subscribers enabled
     *
     * @return boolean
     */
    public function displaySubscribers()
    {
        return (bool) $this->_scopeConfig->getValue(
            'newance_training/post_view/subscribers/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve posts instance
     *
     * @return \Newance\Training\Model\Category
     */
    public function getPost()
    {
        if (!$this->hasData('post')) {
            $this->setData(
                'post',
                $this->_coreRegistry->registry('current_training_post')
            );
        }
        return $this->getData('post');
    }

    /**
     * Returns action url for subscribers form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getPost()->getPostSubscriberUrl();
    }
}
