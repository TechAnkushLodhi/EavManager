<?php

namespace Icecube\EavManager\Model\EavManager\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    protected $eavmanager;

    public function __construct(\Icecube\EavManager\Model\EavManager $eavmanager)
    {
        $this->eavmanager = $eavmanager;
    }

    public function toOptionArray()
    {
        $availableOptions = $this->eavmanager->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
?>
