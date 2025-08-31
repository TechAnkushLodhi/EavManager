<?php

namespace Icecube\EavManager\Model\ResourceModel\EavManager;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Icecube\EavManager\Model\EavManager as Model;
use Icecube\EavManager\Model\ResourceModel\EavManager as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'icecube_eav_manager_collection';
    protected $_eventObject = 'eav_manager_collection';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}