<?php

namespace Iflair\Faq\Block;

use Magento\Framework\View\Element\Template;
use Iflair\Faq\Model\ResourceModel\Faq\CollectionFactory;

class FaqList extends Template
{
    protected $collectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    public function getFaqs()
    {
        $product = $this->_layout
            ->getBlock('product.info')
            ->getProduct();

        if (!$product) {
            return [];
        }

        $productId = (int)$product->getId();

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', 2);   
        $collection->addFieldToFilter('visibility', 1);

        $collection->getSelect()->join(
            ['fp' => $collection->getTable('iflair_faq_product')],
            'fp.faq_id = main_table.faq_id',
            []
        )->where('fp.product_id = ?', $productId);

        $collection->setOrder('faq_id', 'DESC');

        return $collection;
    }
}
