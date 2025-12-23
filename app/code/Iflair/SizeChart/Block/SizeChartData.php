<?php
namespace Iflair\SizeChart\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\App\ResourceConnection;

class SizeChartData extends Template
{
    protected $registry;
    protected $resource;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        ResourceConnection $resource,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->resource = $resource;
        parent::__construct($context, $data);
    }

    /**
     * Get current product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get size chart JSON data via Table Join
     */
    public function getSizeChartData()
    {
        $product = $this->getProduct();
        if (!$product || !$product->getId()) {
            return [];
        }
    
        $connection = $this->resource->getConnection();
        $templateTable = $this->resource->getTableName('size_chart_templates');
        $mappingTable  = $this->resource->getTableName('size_chart_product');
    
        $select = $connection->select()
            ->from(['t' => $templateTable], ['size_chart_data'])
            ->join(
                ['m' => $mappingTable],
                't.template_id = m.template_id',
                []
            )
            ->where('m.product_id = ?', (int)$product->getId())
            ->limit(1);
    
        $result = $connection->fetchOne($select);
    
        if (!$result) {
            return [];
        }
    
        $decoded = json_decode($result, true);
        return is_array($decoded) ? $decoded : [];
    }
    
}