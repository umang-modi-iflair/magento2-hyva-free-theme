<?php
namespace Mollie\Payment\Model\MollieConfigProvider;

/**
 * Interceptor class for @see \Mollie\Payment\Model\MollieConfigProvider
 */
class Interceptor extends \Mollie\Payment\Model\MollieConfigProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Request\Http $request, \Mollie\Payment\Helper\General $mollieHelper, \Magento\Payment\Helper\Data $paymentHelper, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\View\Asset\Repository $assetRepository, \Magento\Framework\Locale\Resolver $localeResolver, \Mollie\Payment\Config $config, \Mollie\Payment\Service\Mollie\GetIssuers $getIssuers, \Magento\Store\Model\StoreManagerInterface $storeManager, \Mollie\Payment\Service\Mollie\MethodParameters $methodParameters, \Mollie\Payment\Service\Mollie\ApplePay\SupportedNetworks $supportedNetworks, \Mollie\Payment\Service\Mollie\MollieApiClient $mollieApiClient)
    {
        $this->___init();
        parent::__construct($request, $mollieHelper, $paymentHelper, $checkoutSession, $assetRepository, $localeResolver, $config, $getIssuers, $storeManager, $methodParameters, $supportedNetworks, $mollieApiClient);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): array
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfig');
        return $pluginInfo ? $this->___callPlugins('getConfig', func_get_args(), $pluginInfo) : parent::getConfig();
    }
}
