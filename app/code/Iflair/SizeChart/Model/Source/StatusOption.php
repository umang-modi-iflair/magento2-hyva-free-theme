<?php
namespace Iflair\SizeChart\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class StatusOption implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Enabled')],
            ['value' => 0, 'label' => __('Disabled')],
        ];
    }
}
