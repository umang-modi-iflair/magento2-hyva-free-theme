<?php
namespace Iflair\Delivery\Api\Data;

interface PincodeDataInterface
{
    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get Pincode
     *
     * @return string
     */
    public function getPincode();

    /**
     * Set Pincode
     *
     * @param string $pincode
     * @return $this
     */
    public function setPincode($pincode);

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

    // All declared variable
}