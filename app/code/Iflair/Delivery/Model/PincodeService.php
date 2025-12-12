<?php
namespace Iflair\Delivery\Model;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\ResourceConnection;

class PincodeService
{
    protected $curl;
    protected $resource;

    public function __construct(
        Curl $curl,
        ResourceConnection $resource
    ) {
        $this->curl = $curl;
        $this->resource = $resource;
    }

    /**
     *
     * @param string $pincode
     * @return array
     */
    public function fetchPincodeData($pincode)
    {
        $url = "https://api.postalpincode.in/pincode/" . $pincode;
        $this->curl->get($url);
        $response = json_decode($this->curl->getBody(), true);

        if (isset($response[0]['Status']) && $response[0]['Status'] === 'Success' && !empty($response[0]['PostOffice'])) {

            $postOffice = $response[0]['PostOffice'][0];

            $state = $postOffice['State'] ?? '';
            $district = $postOffice['District'] ?? '';
            $country = $postOffice['Country'] ?? 'IN';
            $region = $postOffice['Region'] ?? '';

            $cutoffHour = 14; 
            $cutoffMinute = 35;
            $now = new \DateTime();
            $cutoffTime = (new \DateTime())->setTime($cutoffHour, $cutoffMinute, 0);

            $minDays = $now <= $cutoffTime ? 3 : 4;
            $maxDays = $minDays + 2;

            $minDelivery = (new \DateTime())->modify("+$minDays days")->format('Y-m-d');
            $maxDelivery = (new \DateTime())->modify("+$maxDays days")->format('Y-m-d');

            return [
                'success' => true,
                'message' => "Delivery expected within $minDays-$maxDays days.",
                'location_details' => [
                    'state_name' => $state,
                    'district' => $district,
                    'country' => $country,
                    'region' => $region
                ],
                'serviceability' => [
                    'is_serviceable' => $postOffice['DeliveryStatus'] === 'Delivery' ? 1 : 0,
                    'cod_available' => 1, 
                    'min_delivery_date' => $minDelivery,
                    'max_delivery_date' => $maxDelivery
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Pincode not serviceable.',
            // 'location_details' => [],
            // 'serviceability' => []
        ];
    }

    /**
     * Save fetched pincode data to database
     *
     * @param array $pincodeData
     * @param string $pincode
     * @return void
     */
    public function saveToDb($pincodeData, $pincode)
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('pincode_delivery_info');

        $data = [
            'pincode' => $pincode,
            'state' => $pincodeData['location_details']['state_name'] ?? null,
            'district' => $pincodeData['location_details']['district'] ?? null,
            'country' => $pincodeData['location_details']['country'] ?? null,
            'region' => $pincodeData['location_details']['region'] ?? null,
            'is_serviceable' => $pincodeData['serviceability']['is_serviceable'] ?? 0,
            'cod_available' => $pincodeData['serviceability']['cod_available'] ?? 0,
            'min_delivery_date' => $pincodeData['serviceability']['min_delivery_date'] ?? null,
            'max_delivery_date' => $pincodeData['serviceability']['max_delivery_date'] ?? null,
            'message' => $pincodeData['message'] ?? null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $existing = $connection->fetchRow(
            $connection->select()->from($table)->where('pincode = ?', $pincode)
        );

        if ($existing) {
            $connection->update($table, $data, ['pincode = ?' => $pincode]);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $connection->insert($table, $data);
        }
    }

    // GET

    public function fetchAllFromDb()
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('pincode_delivery_info');

        $sql = "SELECT * FROM $table ORDER BY id ASC";
        return $connection->fetchAll($sql);
    }
}
