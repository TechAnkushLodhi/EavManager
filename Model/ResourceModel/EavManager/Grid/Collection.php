<?php
namespace Icecube\EavManager\Model\ResourceModel\EavManager\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['eav_attr' => $this->getTable('eav_attribute')],
            'main_table.attribute_id = eav_attr.attribute_id',
            ['attribute_code', 'frontend_label', 'is_required', 'frontend_input', 'default_value']
        );
        $this->getSelect()->joinLeft(
            ['eav_customer_attr' => $this->getTable('customer_eav_attribute')],
            'main_table.attribute_id = eav_customer_attr.attribute_id',
            ['is_visible', 'is_system', 'sort_order']
        );

        return $this;
    }
}
