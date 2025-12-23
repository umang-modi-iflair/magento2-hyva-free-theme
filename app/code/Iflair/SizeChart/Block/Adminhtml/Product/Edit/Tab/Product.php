<?php
namespace Iflair\SizeChart\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;

class Product extends Template
{
    protected $resource;
    protected $registry;

    public function __construct(
        Template\Context $context,
        ResourceConnection $resource,
        Registry $registry,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get Current Product ID
     */
    public function getCurrentProductId()
    {
        $product = $this->registry->registry('current_product');
        return $product ? $product->getId() : null;
    }


    public function getSizeChartTemplates()
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('size_chart_templates');
        return $connection->fetchAll($connection->select()->from($table)->where('status = ?', 1));
    }

    public function getSelectedTemplateId()
    {
        $product = $this->registry->registry('current_product');
        if (!$product || !$product->getId()) return null;

        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('size_chart_product');

        return $connection->fetchOne(
            $connection->select()
                ->from($table, ['template_id'])
                ->where('product_id = ?', (int)$product->getId())
        );
    }

    public function getJsonTemplateData()
    {
        $templates = $this->getSizeChartTemplates();
        $output = [];

        foreach ($templates as $template) {
            if (empty($template['size_chart_data'])) continue;

            $data = json_decode($template['size_chart_data'], true);
            if (!is_array($data)) continue;

            $output[$template['template_id']] = $this->buildHtmlTable($data);
        }

        return json_encode($output);
    }

    private function buildHtmlTable(array $data)
    {
        if (empty($data)) return '';
        
        $firstRow = reset($data);
        $columns = array_keys($firstRow);
        
        $html = '<table class="admin__table-secondary"><thead><tr>';
        foreach ($columns as $col) {
            $html .= '<th>' . $this->escapeHtml(ucfirst($col)) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($columns as $col) {
                $html .= '<td>' . $this->escapeHtml($row[$col] ?? '-') . '</td>';
            }
            $html .= '</tr>';
        }

        return $html . '</tbody></table>';
    }
}