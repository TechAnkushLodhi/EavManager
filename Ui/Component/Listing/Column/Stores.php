<?php
namespace Icecube\EavManager\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

class MaritalStatusOptions implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'single', 'label' => __('Single')],
            ['value' => 'married', 'label' => __('Married')],
        ];
    }
}

?>