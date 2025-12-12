<?php

namespace Iflair\Faq\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class FaqForm extends Template
{
    protected $registry;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('product');   
    }
}
