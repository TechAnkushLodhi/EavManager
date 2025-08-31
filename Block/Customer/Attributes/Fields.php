<?php
namespace Icecube\EavManager\Block\Customer\Attributes;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Icecube\EavManager\Helper\Data;
use Icecube\EavManager\Model\EavManagerFactory;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory as CustomerAttributeCollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResourceConnection;

class Fields extends Template
{
    protected $helper;
    protected $eavManagerFactory;
    protected $customerAttributeCollectionFactory;
    protected $customerSession;
    protected $storeManager;
    protected $resource;

    public function __construct(
        Context $context,
        Data $helper,
        EavManagerFactory $eavManagerFactory,
        CustomerAttributeCollectionFactory $customerAttributeCollectionFactory,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->eavManagerFactory = $eavManagerFactory->create();
        $this->customerAttributeCollectionFactory = $customerAttributeCollectionFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
        parent::__construct($context, $data);
    }

    public function getEavManager()
    {
        $eavManager = $this->eavManagerFactory->getCollection();  
        $EavAtributes = [];

        $customer = $this->customerSession->getCustomer(); // get logged-in customer

        foreach ($eavManager as $item) {
            $attributeData = $this->getCustomerAttributes($item->getEavAttributeId());
        
            if (!empty($attributeData)) {
                $attributeCode = $attributeData['attribute_code']; // get attribute code
                $attributeValue = $customer->getData($attributeCode); // actual saved value
                // Add customer_data inside attribute data array
                $attributeData['customer_attribute_value'] = $attributeValue;

                
                $EavAtributes[] = $attributeData;
            }
        }
        
        // Filter only visible attributes
        $visibleAttributes = array_filter($EavAtributes, function($attr) {
            return isset($attr['is_visible']) && $attr['is_visible'];
        });

        $visibleAttributes = array_filter($EavAtributes, function($attr) {
            return isset($attr['is_in_form']) && $attr['is_in_form'];
        });

        $visibleAttributes = array_filter($EavAtributes, function($attr) {
            return isset($attr['is_visible_customer_group']) && $attr['is_visible_customer_group'];
        });

        // Sort by sort_order
        usort($visibleAttributes, function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });

        
        
      
        return $visibleAttributes;
    }

    protected function getCustomerAttributes($attributeId)
    {    $storeId = $this->storeManager->getStore()->getId(); // get Current Store ID
       // --------------------  Get Store wise Label Start -----------------------
        
        $customerAttributes = $this->customerAttributeCollectionFactory->create();
        $customerAttributes->addFieldToFilter('main_table.attribute_id', $attributeId);

        $attribute = $customerAttributes->getFirstItem();
        $attributeModel = $attribute->setStoreId($storeId);

        $data = $attribute->getData();
        $data['frontend_label'] = $attributeModel->getStoreLabel(); // Store wise label


       // --------------------  Get forms code Start -----------------------
       // Get form code from request context
        $formCode = $this->getRequest()->getFullActionName();
        // Check customer_form_attribute table
        $connection = $this->resource->getConnection();
        $formTable = $this->resource->getTableName('customer_form_attribute');

        $select = $connection->select()
            ->from($formTable)
            ->where('form_code = ?', $formCode)
            ->where('attribute_id = ?', $attributeId);

        $isInForm = $connection->fetchRow($select); // false if not found
        $data['is_in_form'] = $isInForm ? 1 : 0; // Set to 1 if found, 0 if not



        $customerGroupId = $this->customerSession->isLoggedIn() 
            ? $this->customerSession->getCustomer()->getGroupId() 
            : 0;

        $CustomerGroupsOfAttribute = $this->eavManagerFactory->getCollection()
            ->addFieldToFilter('attribute_id', $attributeId)
            ->getFirstItem()
            ->getCustomerFormIds();

        $CustomerGroupIdsOfAttribute = explode(',', $CustomerGroupsOfAttribute);
        $data['is_visible_customer_group'] = in_array($customerGroupId, $CustomerGroupIdsOfAttribute) ? 1 : 0;
        return $data;
        }


}

