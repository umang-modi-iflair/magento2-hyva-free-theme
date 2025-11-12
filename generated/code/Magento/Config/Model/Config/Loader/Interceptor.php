<?php
namespace Magento\Config\Model\Config\Loader;

/**
 * Interceptor class for @see \Magento\Config\Model\Config\Loader
 */
class Interceptor extends \Magento\Config\Model\Config\Loader implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ValueFactory $configValueFactory, ?\Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $collectionFactory = null)
    {
        $this->___init();
        parent::__construct($configValueFactory, $collectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigByPath($path, $scope, $scopeId, $full = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfigByPath');
        return $pluginInfo ? $this->___callPlugins('getConfigByPath', func_get_args(), $pluginInfo) : parent::getConfigByPath($path, $scope, $scopeId, $full);
    }
}
