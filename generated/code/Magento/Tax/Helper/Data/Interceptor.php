<?php
namespace Magento\Tax\Helper\Data;

/**
 * Interceptor class for @see \Magento\Tax\Helper\Data
 */
class Interceptor extends \Magento\Tax\Helper\Data implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\Tax\Model\Config $taxConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $orderTaxCollectionFactory, \Magento\Framework\Locale\ResolverInterface $localeResolver, \Magento\Catalog\Helper\Data $catalogHelper, \Magento\Tax\Api\OrderTaxManagementInterface $orderTaxManagement, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, ?\Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->___init();
        parent::__construct($context, $jsonHelper, $taxConfig, $storeManager, $localeFormat, $orderTaxCollectionFactory, $localeResolver, $catalogHelper, $orderTaxManagement, $priceCurrency, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculatedTaxes($source)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCalculatedTaxes');
        return $pluginInfo ? $this->___callPlugins('getCalculatedTaxes', func_get_args(), $pluginInfo) : parent::getCalculatedTaxes($source);
    }
}
