<?php
namespace Iflair\OrderGrid\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Items extends Column
{
    protected $orderRepository;
    protected $imageHelper;
    protected $productRepository;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        Image $imageHelper,
        ProductRepositoryInterface $productRepository,
        array $components = [],
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->imageHelper = $imageHelper;
        $this->productRepository = $productRepository;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

  public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {

            $orderId = $item['entity_id'];
            $order = $this->orderRepository->get($orderId);

            $html = '<div onclick="event.stopPropagation();">'; 
            $html .= '<table style="width:100%; border-collapse:collapse; table-layout: fixed;">';

            $html .= '
                <thead>
                    <tr>
                        <th style="border:1px solid #514943; padding:5px; width:40%; text-align:left;">Product Name</th>
                        <th style="border:1px solid #514943; padding:5px; width:25%; text-align:left;">SKU</th>
                        <th style="border:1px solid #514943; padding:5px; width:10%; text-align:center;">Qty</th>
                        <th style="border:1px solid #514943; padding:5px; width:40%; text-align:center;">Type</th>
                        <th style="border:1px solid #514943; padding:5px; width:20%; text-align:center;">Thumb</th>
                    </tr>
                </thead>';

            $html .= '<tbody>';
            foreach ($order->getAllVisibleItems() as $orderItem) {

                try {
                    $product = $this->productRepository->getById($orderItem->getProductId());
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $product = null;
                }
                
                $imageUrl = '';
                if ($product) {
                    $imageUrl = $this->imageHelper
                        ->init($product, 'product_thumbnail_image')
                        ->getUrl();
                } else {
                    $imageUrl = $this->imageHelper->getDefaultPlaceholderUrl();
                }
                
                $html .= '
                    <tr>
                        <td style="border:1px solid #ccc; padding:5px;">' . $orderItem->getName() . '</td>
                        <td style="border:1px solid #ccc; padding:5px;">' . $orderItem->getSku() . '</td>
                        <td style="border:1px solid #ccc; padding:5px; text-align:center;">' . (int)$orderItem->getQtyOrdered() . '</td>
                        <td style="border:1px solid #ccc; padding:5px; text-align:center;">' . $orderItem->getProductType() . '</td>
                        <td style="border:1px solid #ccc; padding:5px; text-align:center;">
                            <img src="' . $imageUrl . '" width="50" height="50" style="border:1px solid #ccc; max-width: 50px; height: auto;"/>
                        </td>
                    </tr>';
            }
            $html .= '</tbody>';

            $html .= '</table>';
            $html .= '</div>';

            $item[$this->getData('name')] = $html;
        }

        return $dataSource;
    }
}
