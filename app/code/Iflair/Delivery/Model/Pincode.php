<?php
namespace Iflair\Delivery\Model;

use Iflair\Delivery\Api\PincodeInterface;
use Iflair\Delivery\Model\PincodeService;

class Pincode implements PincodeInterface
{
    protected $pincodeService;

    public function __construct(PincodeService $pincodeService)
    {
        $this->pincodeService = $pincodeService;    
    }

    public function getPincodeInfo($pincode)
    {
        $data = $this->pincodeService->fetchPincodeData($pincode);

        if (!empty($data['success']) && $data['success'] === true) {
            $this->pincodeService->saveToDb($data, $pincode);
        }

        return $data;
    }

    public function getAllPincodes()
    {
        return $this->pincodeService->fetchAllFromDb();
    }
}
