<?php
namespace Magento\Sales\Api\Data;

/**
 * Extension class for @see \Magento\Sales\Api\Data\OrderItemInterface
 */
class OrderItemExtension extends \Magento\Framework\Api\AbstractSimpleObject implements OrderItemExtensionInterface
{
    /**
     * @return \Magento\Tax\Api\Data\OrderTaxItemInterface[]|null
     */
    public function getItemizedTaxes()
    {
        return $this->_get('itemized_taxes');
    }

    /**
     * @param \Magento\Tax\Api\Data\OrderTaxItemInterface[] $itemizedTaxes
     * @return $this
     */
    public function setItemizedTaxes($itemizedTaxes)
    {
        $this->setData('itemized_taxes', $itemizedTaxes);
        return $this;
    }

    /**
     * @return \Magento\GiftMessage\Api\Data\MessageInterface|null
     */
    public function getGiftMessage()
    {
        return $this->_get('gift_message');
    }

    /**
     * @param \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage
     * @return $this
     */
    public function setGiftMessage(\Magento\GiftMessage\Api\Data\MessageInterface $giftMessage)
    {
        $this->setData('gift_message', $giftMessage);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMollieRecurringType()
    {
        return $this->_get('mollie_recurring_type');
    }

    /**
     * @param string $mollieRecurringType
     * @return $this
     */
    public function setMollieRecurringType($mollieRecurringType)
    {
        $this->setData('mollie_recurring_type', $mollieRecurringType);
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getMollieRecurringData()
    {
        return $this->_get('mollie_recurring_data');
    }

    /**
     * @param string[] $mollieRecurringData
     * @return $this
     */
    public function setMollieRecurringData($mollieRecurringData)
    {
        $this->setData('mollie_recurring_data', $mollieRecurringData);
        return $this;
    }
}
