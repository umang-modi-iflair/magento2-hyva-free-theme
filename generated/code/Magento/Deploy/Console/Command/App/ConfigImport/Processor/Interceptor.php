<?php
namespace Magento\Deploy\Console\Command\App\ConfigImport\Processor;

/**
 * Interceptor class for @see \Magento\Deploy\Console\Command\App\ConfigImport\Processor
 */
class Interceptor extends \Magento\Deploy\Console\Command\App\ConfigImport\Processor implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Deploy\Model\DeploymentConfig\ChangeDetector $changeDetector, \Magento\Deploy\Model\DeploymentConfig\ImporterPool $configImporterPool, \Magento\Deploy\Model\DeploymentConfig\ImporterFactory $importerFactory, \Magento\Framework\App\DeploymentConfig $deploymentConfig, \Magento\Deploy\Model\DeploymentConfig\Hash $configHash, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Console\QuestionPerformer\YesNo $questionPerformer)
    {
        $this->___init();
        parent::__construct($changeDetector, $configImporterPool, $importerFactory, $deploymentConfig, $configHash, $logger, $questionPerformer);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute($input, $output);
    }
}
