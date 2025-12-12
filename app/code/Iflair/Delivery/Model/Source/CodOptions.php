<?php
namespace Iflair\Delivery\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CodOptions implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 0, 'label' => __('No')],
        ];
    }
}
