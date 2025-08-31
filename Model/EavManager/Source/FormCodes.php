<?php
/**
 * Copyright © Icecube Digital All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Icecube\EavManager\Model\EavManager\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class FormCodes extends AbstractSource
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
                // ['label' => __('Adminhtml Customer'), 'value' => 'adminhtml_customer'],
                ['label' => __('Customer Account Edit'), 'value' => 'customer_account_edit'],
                ['label' => __('Customer Account Create'), 'value' => 'customer_account_create'],
                ['label' => __('Adminhtml Checkout'), 'value' => 'adminhtml_checkout'],
            ];
        }
        return $this->_options;
    }
}