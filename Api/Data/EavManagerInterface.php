<?php
namespace Icecube\EavManager\Api\Data;

interface EavManagerInterface
{
    const ID = 'entity_id';
    const ATTRIBUTE_ID = 'attribute_id';  
    const EAV_ENTITY_TYPE = 'entity_type_id';
    const STORE_VIEW_CODES = 'store_view_id';
    const CUSTOMER_FORM_IDS = 'customer_form_ids';

    /**
     * Get ID
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     * @param int $id
     * @return \Icecube\EavManager\Api\Data\EavManagerInterface
     */
    public function setId(?int $id);

    /**
     * Get Attribute ID
     * @return int
     */
    public function getEavAttributeId();

    /**
     * Set Attribute ID
     * @param int $attributeId
     * @return \Icecube\EavManager\Api\Data\EavManagerInterface
     */
    public function setEavAttributeId($attributeId);

    /**
     * Get EAV Entity Type
     * @return string
     */
    public function getEavEntityType();

    /**
     * Set EAV Entity Type
     * @param string $eavEntityType
     * @return \Icecube\EavManager\Api\Data\EavManagerInterface
     */
    public function setEavEntityType($eavEntityType);

    /**
     * Get Store View Codes
     * @return string
     */
    public function getStoreViewCodes();

    /**
     * Set Store View Codes
     * @param string $storeViewCodes
     * @return \Icecube\EavManager\Api\Data\EavManagerInterface
     */
    public function setStoreViewCodes($storeViewCodes);

    /**
     * Get Customer Form IDs
     * @return string
     */
    public function getCustomerFormIds();

    /**
     * Set Customer Form IDs
     * @param string $customerFormIds
     * @return \Icecube\EavManager\Api\Data\EavManagerInterface
     */
    public function setCustomerFormIds($customerFormIds);
}
?>

