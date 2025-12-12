<?php
namespace Iflair\Delivery\Api;

interface PincodeInterface
{
    /**
     * POST: Get single pincode details
     *
     * @param string $pincode
     * @return array
     */
    public function getPincodeInfo($pincode);

    /**
     * GET: Return all saved pincodes (NO PARAMS)
     *
     * @return array
     */
    public function getAllPincodes();
}
