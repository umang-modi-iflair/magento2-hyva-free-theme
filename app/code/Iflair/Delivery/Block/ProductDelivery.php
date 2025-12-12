<?php
namespace Iflair\Delivery\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class ProductDelivery extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    public function getProductId()
    {
        $product = $this->getProduct();
        return $product ? $product->getId() : null;
    }

    public function getProduct()
    {
        return $this->_registry->registry('current_product');
    }
}
