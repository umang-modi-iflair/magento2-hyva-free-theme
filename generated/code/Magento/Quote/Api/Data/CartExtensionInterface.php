<?php
namespace Magento\Quote\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Quote\Api\Data\CartInterface
 */
interface CartExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return \Magento\Quote\Api\Data\ShippingAssignmentInterface[]|null
     */
    public function getShippingAssignments();

    /**
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface[] $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments);

    /**
     * @return float|null
     */
    public function getMolliePaymentFee();

    /**
     * @param float $molliePaymentFee
     * @return $this
     */
    public function setMolliePaymentFee($molliePaymentFee);

    /**
     * @return float|null
     */
    public function getBaseMolliePaymentFee();

    /**
     * @param float $baseMolliePaymentFee
     * @return $this
     */
    public function setBaseMolliePaymentFee($baseMolliePaymentFee);

    /**
     * @return float|null
     */
    public function getMolliePaymentFeeTax();

    /**
     * @param float $molliePaymentFeeTax
     * @return $this
     */
    public function setMolliePaymentFeeTax($molliePaymentFeeTax);

    /**
     * @return float|null
     */
    public function getBaseMolliePaymentFeeTax();

    /**
     * @param float $baseMolliePaymentFeeTax
     * @return $this
     */
    public function setBaseMolliePaymentFeeTax($baseMolliePaymentFeeTax);
}
