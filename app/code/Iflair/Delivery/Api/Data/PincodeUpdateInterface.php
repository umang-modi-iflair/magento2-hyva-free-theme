<?php
namespace Iflair\Delivery\Api\Data;

/**
 * Interface for Pincode update data
 */
interface PincodeUpdateInterface
{
    /**
     * Get minimum delivery date
     *
     * @return string|null
     */
    public function getMinDeliveryDate();

    /**
     * Set minimum delivery date
     *
     * @param string $value
     * @return $this
     */
    public function setMinDeliveryDate($value);

    /**
     * Get maximum delivery date
     *
     * @return string|null
     */
    public function getMaxDeliveryDate();

    /**
     * Set maximum delivery date
     *
     * @param string $value
     * @return $this
     */
    public function setMaxDeliveryDate($value);

    /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     *
     * @param string $value
     * @return $this
     */
    public function setMessage($value);
}
