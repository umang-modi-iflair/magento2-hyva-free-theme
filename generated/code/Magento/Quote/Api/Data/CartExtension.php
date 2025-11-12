<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\CartInterface
 */
class CartExtension extends \Magento\Framework\Api\AbstractSimpleObject implements CartExtensionInterface
{
    /**
     * @return \Magento\Quote\Api\Data\ShippingAssignmentInterface[]|null
     */
    public function getShippingAssignments()
    {
        return $this->_get('shipping_assignments');
    }

    /**
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface[] $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments)
    {
        $this->setData('shipping_assignments', $shippingAssignments);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMolliePaymentFee()
    {
        return $this->_get('mollie_payment_fee');
    }

    /**
     * @param float $molliePaymentFee
     * @return $this
     */
    public function setMolliePaymentFee($molliePaymentFee)
    {
        $this->setData('mollie_payment_fee', $molliePaymentFee);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseMolliePaymentFee()
    {
        return $this->_get('base_mollie_payment_fee');
    }

    /**
     * @param float $baseMolliePaymentFee
     * @return $this
     */
    public function setBaseMolliePaymentFee($baseMolliePaymentFee)
    {
        $this->setData('base_mollie_payment_fee', $baseMolliePaymentFee);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMolliePaymentFeeTax()
    {
        return $this->_get('mollie_payment_fee_tax');
    }

    /**
     * @param float $molliePaymentFeeTax
     * @return $this
     */
    public function setMolliePaymentFeeTax($molliePaymentFeeTax)
    {
        $this->setData('mollie_payment_fee_tax', $molliePaymentFeeTax);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseMolliePaymentFeeTax()
    {
        return $this->_get('base_mollie_payment_fee_tax');
    }

    /**
     * @param float $baseMolliePaymentFeeTax
     * @return $this
     */
    public function setBaseMolliePaymentFeeTax($baseMolliePaymentFeeTax)
    {
        $this->setData('base_mollie_payment_fee_tax', $baseMolliePaymentFeeTax);
        return $this;
    }
}
