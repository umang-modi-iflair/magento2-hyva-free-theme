<?php
namespace Newance\Training\Controller\Brand;

/**
 * Training brand view
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * View training brand action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $brand = $this->_initBrand();
        if (!$brand) {
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        $this->_objectManager->get('\Magento\Framework\Registry')->register('current_training_brand', $brand);

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Init brand
     *
     * @return \Newance\Training\Model\Brand || false
     */
    protected function _initBrand()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();

        $brand = $this->_objectManager->create('Newance\Training\Model\Brand')->load($id);

        if (!$brand->isVisibleOnStore($storeId)) {
            return false;
        }

        $brand->setStoreId($storeId);

        return $brand;
    }
}
