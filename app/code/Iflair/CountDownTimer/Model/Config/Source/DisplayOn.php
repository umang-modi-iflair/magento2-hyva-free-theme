<?php
namespace Iflair\CountDownTimer\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class DisplayOn implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'home', 'label' => __('Home Page')],
            ['value' => 'category', 'label' => __('Category Pages')],
            ['value' => 'product', 'label' => __('Product Pages')],
            // ['value' => 'cms', 'label' => __('CMS Pages')],
            ['value' => 'cart', 'label' => __('Cart Page')],
            ['value' => 'checkout', 'label' => __('Checkout Page')],
            // ['value' => 'all', 'label' => __('All Pages')],
        ];
    }
}
