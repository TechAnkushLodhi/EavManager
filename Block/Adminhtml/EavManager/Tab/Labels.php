<?php

namespace Icecube\EavManager\Block\Adminhtml\EavManager\Tab;

use Magento\Backend\Block\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;

class Labels extends Template
{
    protected $_template = 'Icecube_EavManager::eavmanager/labels.phtml';
    protected $storeManager;
    protected $registry;
    protected $attribute;

    protected $dataFormPart = 'eavmanager_form';

    public function __construct(
        Template\Context $context,
        Registry $registry,
        Attribute $attribute,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->attribute = $attribute;
    }

    /**
     * Get all store views
     *
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getStores()
    {
        return $this->storeManager->getStores();
    }

    /**
     * Get label values from request data or default labels
     *
     * @return array
     */
    public function getLabelValues()
    {
        $labels = $this->getData('label_values') ?: [];
        $storeLabels = [];
        foreach ($this->getStores() as $store) {
            $storeId = $store->getId();
            $storeLabels[$storeId] = $labels[$storeId] ?? '';
        }
        return $storeLabels;
    }

    /**
     * Check if read-only mode is enabled
     *
     * @return bool
     */
    public function getReadOnly()
    {
        return (bool)$this->getData('readonly');
    }

    /**
     * @return string
     */
    public function getDataFormPart(): string
    {
        return $this->dataFormPart;
    }

     /**
     * Get Data from DataProvider
     */
    public function getAttributeLabels()
    {
        $attribute = $this->registry->registry('Icecube_EavManager'); 
        if(!$attribute) {
            return [];
        }
        $attrubutelabels = $this->attribute->getStoreLabelsByAttributeId($attribute->getAttributeId());
        if(!$attrubutelabels) {
            return [];
        }
        return  $attrubutelabels;
    }

   
}
