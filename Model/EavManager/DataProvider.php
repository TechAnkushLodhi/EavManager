<?php

namespace Icecube\EavManager\Model\EavManager;

use Icecube\EavManager\Model\ResourceModel\EavManager\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Registry;


class DataProvider extends AbstractDataProvider
{
        protected $collection;
        protected $dataPersistor; 
        protected $attributeCollectionFactory;
        protected $attribute;
        protected $loadedData = [];
        protected $registry;

        public function __construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            CollectionFactory $collectionFactory,  // 🔹 Corrected variable name
            AttributeCollectionFactory $attributeCollectionFactory, // 🔹 Added for attribute collection
            DataPersistorInterface $dataPersistor,
            Registry $registry,
            array $meta = [],
            array $data = []
        ) {
            $this->collection = $collectionFactory->create();
            $this->attributeCollectionFactory = $attributeCollectionFactory;
            $this->dataPersistor = $dataPersistor;
            $this->registry = $registry;

            parent::__construct(
                $name,
                $primaryFieldName,
                $requestFieldName,
                $meta,
                $data
            );

            $this->meta = $this->prepareMeta($this->meta);
        }

        public function prepareMeta(array $meta)
        {
            return $meta;
        }

        public function getData()
        {
            if (!empty($this->loadedData)) {
                return $this->loadedData;
            }
        
            // Fetch all items from custom collection
            $items = $this->collection->getItems();
        
            foreach ($items as $item) {
            // Get custom data
            $customData = $item->getData();

            // Disable field when we edit the attribute
            $customData['is_disabled'] = true;

            // Explode the store view code  & Set in the Data
            $storeViewCode = explode(',', $customData['store_view_id']);
            $customData['store_view'] =  $storeViewCode;

            // Explode the Customer Group code & Set in the Data
            $CustomerGroups = explode(',', $customData['customer_form_ids']);
            $customData['customer_group'] =  $CustomerGroups;

            // Added the frontend_labels in the Data
            $AttributesLabes = $this->getAttributeLabels($customData['attribute_id']);
            if (!empty($AttributesLabes)) {
                $customData['frontend_labels'] = $AttributesLabes;
            }
            
            // Join `eav_attribute` table based on `attribute_id`
            $attributeId = $customData['attribute_id']; // Assuming 'attribute_id' is the field in your custom table
            $eavAttribute = $this->getEavAttributeData($attributeId);

            
            $AttributeFormCode = $this->getEavAttributeFormCode($attributeId);
             if(isset($AttributeFormCode['show_on_forms'])){
                $customData['show_on_forms'] = $AttributeFormCode['show_on_forms'];
             }else{
                $customData['show_on_forms'] = null;
             }
           
            // Merge custom data with EAV attribute data
            if (isset($eavAttribute[0])) {
                foreach ($eavAttribute[0] as $key => $value) {
                    $customData[$key] = $value;
                }
                $this->loadedData[$item->getId()] = $customData;
            } else {
                $this->loadedData[$item->getId()] = $customData;
            }
        }

        // Persisted data (if any)
        $data = $this->dataPersistor->get("Icecube_EavManager");
        if (!empty($data)) {
            $eavAttribute = $this->collection->getNewEmptyItem();
            $eavAttribute->setData($data);
            $this->loadedData[$eavAttribute->getId()] = $data;
            $this->dataPersistor->clear("Icecube_EavManager");
        }
        // echo "<pre>";
        // print_r($this->loadedData);
        // echo "</pre>";
        // die;
        return $this->loadedData;
    }
    
        /**
         * Function to get EAV attribute data based on attribute_id
         */
        protected function getEavAttributeData($attributeId)
        {
            // Fetch the EAV attribute collection
            $eavAttributeCollection = $this->attributeCollectionFactory->create();
        
            $eavAttributeCollection->joinLeft(
                ['cea' => 'customer_eav_attribute'], // Alias 'cea' for the table
                'main_table.attribute_id = cea.attribute_id', // Join condition
                ['is_visible', 'is_used_in_grid','sort_order'] // Specific columns to fetch
            );
            // Add a filter to get only the specific attribute by attribute_id
            $eavAttributeCollection->addFieldToFilter('main_table.attribute_id', $attributeId); 
            return $eavAttributeCollection->getData();
        }


        /**
         * Function to get EAV attribute form code based on attribute_id
         */
        protected function getEavAttributeFormCode($attributeId){
            // Fetch the EAV attribute collection
            $eavAttributeCollection = $this->attributeCollectionFactory->create();
            $eavAttributeCollection->joinLeft(
                ['customerform' => 'customer_form_attribute'], // Alias 'cea' for the table
                'main_table.attribute_id = customerform.attribute_id', // Join condition
                ['form_code'] // Specific columns to fetch
            );
            // Add a filter to get only the specific attribute by attribute_id
            $eavAttributeCollection->addFieldToFilter('main_table.attribute_id', $attributeId);
            $FormCodeData = [];
            foreach ($eavAttributeCollection->getData() as $data) {
                $FormCodeData['show_on_forms'][] = $data['form_code'];
            }
            return  $FormCodeData ?? null;
        }


        /**
         * Function to get attribute labels based on attribute_id
         */
        public function getAttributeLabels($attributeId)
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $attribute = $objectManager->get(\Magento\Eav\Model\ResourceModel\Entity\Attribute::class);
            if(!$attribute) {
                return [];
            }
            $attrubutelabels = $attribute->getStoreLabelsByAttributeId($attributeId);
            if(!$attrubutelabels) {
                return [];
            }
            return  $attrubutelabels;
        }
    

   
    
}
