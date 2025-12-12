<?php
namespace Iflair\Faq\Block\Adminhtml\Faq\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Products extends Extended implements TabInterface
{
    protected $_productCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $productCollectionFactory,
        \Iflair\Faq\Model\FaqFactory $faqFactory, 
        \Magento\Framework\Registry $registry,

        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_faqFactory = $faqFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'sku']); 
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header' => __('ID'),
            'index' => 'entity_id',
            'type' => 'number',
        ]);

        $this->addColumn('name', [
            'header' => __('Product'),
            'index' => 'name',
        ]);

        $this->addColumn('sku', [
            'header' => __('SKU'),
            'index' => 'sku',
        ]);

        $this->addColumn('select', [
            'header' => __('Select'),
            'type' => 'checkbox',
            'index' => 'entity_id',
            'field_name' => 'selected_products[]',
            'values' => $this->getSelectedProducts(), 
        ]);

        return parent::_prepareColumns();
    }


    public function getTabLabel()
    {
        return __('Products');
    }

    public function getTabTitle()
    {
        return __('Products');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
   public function getSelectedProducts()
    {
        $faq = $this->_coreRegistry->registry('faq_data');

        if (!$faq || !$faq->getId()) {
            return [];
        }

        $faqId = (int)$faq->getId();

        $connection = $this->_productCollectionFactory->create()->getConnection();
        $table = $connection->getTableName('iflair_faq_product');

        return $connection->fetchCol(
            $connection->select()
                ->from($table, 'product_id')
                ->where('faq_id = ?', $faqId)
        );
    }
 }

