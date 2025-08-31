<?php
/**
 * Copyright © Icecube Digital All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Icecube\EavManager\Model;

use Magento\Framework\Model\AbstractModel;
use Icecube\EavManager\Api\Data\EavManagerInterface;
use Icecube\EavManager\Model\ResourceModel\EavManager as ResourceModel;

class EavManager extends AbstractModel implements EavManagerInterface
{
    const CACHE_TAG = 'icecube_eav_manager';

    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'icecube_eav_manager';

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }


    /**
     * Get Entity ID
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get EAV Attribute ID
     */
    public function getEavAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    /**
     * Get EAV Entity Type
     */
    public function getEavEntityType()
    {
        return $this->getData(self::EAV_ENTITY_TYPE);
    }

    /**
     * Get Attribute Code
     */
    public function getStoreViewCodes()
    {
        return $this->getData(self::STORE_VIEW_CODES);
    }

    /**
     * Get Store ID
     */
    public function getCustomerFormIds()
    {
        return $this->getData(self::CUSTOMER_FORM_IDS);
    }



    /**
     * Set EAV Attribute ID
     */
    public function setEavAttributeId($attributeId)
    {
        return $this->setData(self::ATTRIUTE_ID, $attributeId);
    }

    /**
     * Set EAV Entity Type
     */
    public function setEavEntityType($eavEntityType)
    {
        return $this->setData(self::EAV_ENTITY_TYPE, $eavEntityType);
    }

    /**
     * Set Attribute Code
     */
    public function setStoreViewCodes($storeViewCodes)
    {
        return $this->setData(self::STORE_VIEW_CODES, $storeViewCodes);
    }

    /**
     * Set Store ID
     */
    public function setCustomerFormIds($customerFormIds)
    {
        return $this->setData(self::CUSTOMER_FORM_IDS, $customerFormIds);
    }

  
}
?>