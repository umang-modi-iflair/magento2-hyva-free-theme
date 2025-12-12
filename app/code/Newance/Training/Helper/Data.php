<?php
namespace Newance\Training\Helper;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Training base helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    private $postData = null;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerViewHelper $customerViewHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerViewHelper $customerViewHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerViewHelper = $customerViewHelper;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }

        /**
         * @var \Magento\Customer\Api\Data\CustomerInterface $customer
         */
        $customer = $this->_customerSession->getCustomerDataObject();

        return trim($this->_customerViewHelper->getCustomerName($customer));
    }

    /**
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }

        /**
         * @var CustomerInterface $customer
         */
        $customer = $this->_customerSession->getCustomerDataObject();

        return $customer->getEmail();
    }

    /**
     * Get value from POST by key
     *
     * @param string $key
     * @return string
     */
    public function getPostValue($key)
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->getDataPersistor()->get('subscriber');
            $this->getDataPersistor()->clear('subscriber');
        }

        if (isset($this->postData[$key])) {
            return (string) $this->postData[$key];
        }

        return '';
    }

    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }

    /**
     * Get Store Name
     *
     * @return StoreManagerInterface
     */
    public function getStoreNameById($storeId)
    {
        // Get the store by ID
        $store = $this->storeManager->getStore($storeId);

        // Get the store name
        $storeName = $store->getName();

        return $storeName;
    }
}
