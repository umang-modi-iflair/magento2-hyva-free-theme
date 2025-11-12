<?php
namespace Mollie\Payment\Model\Mollie;

/**
 * Proxy class for @see \Mollie\Payment\Model\Mollie
 */
class Proxy extends \Mollie\Payment\Model\Mollie implements \Magento\Framework\ObjectManager\NoninterceptableInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Mollie\Payment\Model\Mollie
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Mollie\\Payment\\Model\\Mollie', $shared = true)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['_subject', '_isShared', '_instanceName'];
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        if ($this->_subject) {
            $this->_subject = clone $this->_getSubject();
        }
    }

    /**
     * Debug proxied instance
     */
    public function __debugInfo()
    {
        return ['i' => $this->_subject];
    }

    /**
     * Get proxied instance
     *
     * @return \Mollie\Payment\Model\Mollie
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->_getSubject()->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable(?\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return $this->_getSubject()->isAvailable($quote);
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($paymentAction, $stateObject)
    {
        return $this->_getSubject()->initialize($paymentAction, $stateObject);
    }

    /**
     * {@inheritdoc}
     */
    public function startTransaction(\Magento\Sales\Model\Order $order)
    {
        return $this->_getSubject()->startTransaction($order);
    }

    /**
     * {@inheritdoc}
     */
    public function loadMollieApi($apiKey)
    {
        return $this->_getSubject()->loadMollieApi($apiKey);
    }

    /**
     * {@inheritdoc}
     */
    public function loadMollieApiWithFallbackWrapper($apiKey): \Mollie\Payment\Service\Mollie\Wrapper\MollieApiClientFallbackWrapper
    {
        return $this->_getSubject()->loadMollieApiWithFallbackWrapper($apiKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getMollieApi($storeId = null)
    {
        return $this->_getSubject()->getMollieApi($storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function processTransaction($orderId, $type = 'webhook', $paymentToken = null)
    {
        return $this->_getSubject()->processTransaction($orderId, $type, $paymentToken);
    }

    /**
     * {@inheritdoc}
     */
    public function processTransactionForOrder(\Magento\Sales\Api\Data\OrderInterface $order, $type = 'webhook', $paymentToken = null)
    {
        return $this->_getSubject()->processTransactionForOrder($order, $type, $paymentToken);
    }

    /**
     * {@inheritdoc}
     */
    public function orderHasUpdate($orderId)
    {
        return $this->_getSubject()->orderHasUpdate($orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        return $this->_getSubject()->assignData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function updateShipmentTrack(\Magento\Sales\Model\Order\Shipment $shipment, \Magento\Sales\Model\Order\Shipment\Track $track, \Magento\Sales\Model\Order $order)
    {
        return $this->_getSubject()->updateShipmentTrack($shipment, $track, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function cancelOrder(\Magento\Sales\Model\Order $order)
    {
        return $this->_getSubject()->cancelOrder($order);
    }

    /**
     * {@inheritdoc}
     */
    public function createOrderRefund(\Magento\Sales\Model\Order\Creditmemo $creditmemo, \Magento\Sales\Model\Order $order)
    {
        return $this->_getSubject()->createOrderRefund($creditmemo, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount): \Mollie\Payment\Model\Mollie
    {
        return $this->_getSubject()->refund($payment, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderIdByTransactionId($transactionId)
    {
        return $this->_getSubject()->getOrderIdByTransactionId($transactionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderIdsByTransactionId(string $transactionId): array
    {
        return $this->_getSubject()->getOrderIdsByTransactionId($transactionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIssuers(string $method, string $issuerListType, int $count = 0): ?array
    {
        return $this->_getSubject()->getIssuers($method, $issuerListType, $count);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethods($storeId)
    {
        return $this->_getSubject()->getPaymentMethods($storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatorPool()
    {
        return $this->_getSubject()->getValidatorPool();
    }

    /**
     * {@inheritdoc}
     */
    public function canOrder()
    {
        return $this->_getSubject()->canOrder();
    }

    /**
     * {@inheritdoc}
     */
    public function canAuthorize()
    {
        return $this->_getSubject()->canAuthorize();
    }

    /**
     * {@inheritdoc}
     */
    public function canCapture()
    {
        return $this->_getSubject()->canCapture();
    }

    /**
     * {@inheritdoc}
     */
    public function canCapturePartial()
    {
        return $this->_getSubject()->canCapturePartial();
    }

    /**
     * {@inheritdoc}
     */
    public function canCaptureOnce()
    {
        return $this->_getSubject()->canCaptureOnce();
    }

    /**
     * {@inheritdoc}
     */
    public function canRefund()
    {
        return $this->_getSubject()->canRefund();
    }

    /**
     * {@inheritdoc}
     */
    public function canRefundPartialPerInvoice()
    {
        return $this->_getSubject()->canRefundPartialPerInvoice();
    }

    /**
     * {@inheritdoc}
     */
    public function canVoid()
    {
        return $this->_getSubject()->canVoid();
    }

    /**
     * {@inheritdoc}
     */
    public function canUseInternal()
    {
        return $this->_getSubject()->canUseInternal();
    }

    /**
     * {@inheritdoc}
     */
    public function canUseCheckout()
    {
        return $this->_getSubject()->canUseCheckout();
    }

    /**
     * {@inheritdoc}
     */
    public function canEdit()
    {
        return $this->_getSubject()->canEdit();
    }

    /**
     * {@inheritdoc}
     */
    public function canFetchTransactionInfo()
    {
        return $this->_getSubject()->canFetchTransactionInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function canReviewPayment()
    {
        return $this->_getSubject()->canReviewPayment();
    }

    /**
     * {@inheritdoc}
     */
    public function isGateway()
    {
        return $this->_getSubject()->isGateway();
    }

    /**
     * {@inheritdoc}
     */
    public function isOffline()
    {
        return $this->_getSubject()->isOffline();
    }

    /**
     * {@inheritdoc}
     */
    public function isInitializeNeeded()
    {
        return $this->_getSubject()->isInitializeNeeded();
    }

    /**
     * {@inheritdoc}
     */
    public function isActive($storeId = null)
    {
        return $this->_getSubject()->isActive($storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function canUseForCountry($country)
    {
        return $this->_getSubject()->canUseForCountry($country);
    }

    /**
     * {@inheritdoc}
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->_getSubject()->canUseForCurrency($currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData($field, $storeId = null)
    {
        return $this->_getSubject()->getConfigData($field, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        return $this->_getSubject()->validate();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchTransactionInfo(\Magento\Payment\Model\InfoInterface $payment, $transactionId)
    {
        return $this->_getSubject()->fetchTransactionInfo($payment, $transactionId);
    }

    /**
     * {@inheritdoc}
     */
    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->_getSubject()->order($payment, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->_getSubject()->authorize($payment, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this->_getSubject()->capture($payment, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->_getSubject()->cancel($payment);
    }

    /**
     * {@inheritdoc}
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->_getSubject()->void($payment);
    }

    /**
     * {@inheritdoc}
     */
    public function acceptPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->_getSubject()->acceptPayment($payment);
    }

    /**
     * {@inheritdoc}
     */
    public function denyPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->_getSubject()->denyPayment($payment);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_getSubject()->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function setStore($storeId)
    {
        return $this->_getSubject()->setStore($storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->_getSubject()->getStore();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormBlockType()
    {
        return $this->_getSubject()->getFormBlockType();
    }

    /**
     * {@inheritdoc}
     */
    public function getInfoBlockType()
    {
        return $this->_getSubject()->getInfoBlockType();
    }

    /**
     * {@inheritdoc}
     */
    public function getInfoInstance()
    {
        return $this->_getSubject()->getInfoInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function setInfoInstance(\Magento\Payment\Model\InfoInterface $info)
    {
        return $this->_getSubject()->setInfoInstance($info);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigPaymentAction()
    {
        return $this->_getSubject()->getConfigPaymentAction();
    }

    /**
     * {@inheritdoc}
     */
    public function canSale(): bool
    {
        return $this->_getSubject()->canSale();
    }

    /**
     * {@inheritdoc}
     */
    public function sale(\Magento\Payment\Model\InfoInterface $payment, float $amount)
    {
        return $this->_getSubject()->sale($payment, $amount);
    }
}
