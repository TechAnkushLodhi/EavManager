<?php
namespace Icecube\EavManager\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EavManager extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('icecube_eav_manager', 'entity_id');
    }
}

