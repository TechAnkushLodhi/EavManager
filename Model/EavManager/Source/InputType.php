<?php
/**
 * Copyright © Icecube Digital All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Icecube\EavManager\Model\EavManager\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class InputType extends AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Text Field'), 'value' => 'text'],
                ['label' => __('Text Area'), 'value' => 'textarea'],
                ['label' => __('Text Editor'), 'value' => 'texteditor'],
                ['label' => __('Date'), 'value' => 'date'],
                ['label' => __('Yes/No'), 'value' => 'boolean'],
                ['label' => __('Multiple Select'), 'value' => 'multiselect'],
                ['label' => __('Dropdown'), 'value' => 'select'],
                // ['label' => __('Radio'), 'value' => 'radio'],
                // ['label' => __('Checkbox'), 'value' => 'checkbox'],
                // ['label' => __('Image'), 'value' => 'image'],
                // ['label' => __('File'), 'value' => 'file'],
                // ['label' => __('Video'), 'value' => 'video'],
                // ['label' => __('Audio'), 'value' => 'audio'],
                // ['label' => __('Time Picker'), 'value' => 'time_picker'],
                // ['label' => __('Color Picker'), 'value' => 'color_picker']
            ];
        }
        return $this->_options;
    }
}