<?php
namespace Icecube\EavManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED     = 'eav_manager/general/enabled';
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

 

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }


    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Save store-wise labels for attribute
     *
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @param array $storeLabels
     * @return void
     */
    public function saveStoreLabels(\Magento\Eav\Model\Entity\Attribute $attribute, array $storeLabels)
    {
       
        $connection = $this->resourceConnection->getConnection();
        $attributeId = $attribute->getId();
        if ($attributeId) {
            $condition = ['attribute_id =?' => $attributeId];
            $connection->delete('eav_attribute_label', $condition);
        }
    
        foreach ($storeLabels as $storeId => $label) {
            if ($storeId == 0 || $label === null || !strlen($label)) {
                continue;
            }
            $bind = [
                'attribute_id' => $attributeId,
                'store_id' => $storeId,
                'value' => $label
            ];
            $connection->insert('eav_attribute_label', $bind);
        }
    }
}
