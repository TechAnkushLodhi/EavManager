<?php
declare(strict_types=1);

namespace Icecube\EavManager\Model\EavManager\Source;

use Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class AttributeEntityType extends AbstractSource
{
    /**
     * @var CollectionFactory
     */
    protected $entityTypeCollectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $entityTypeCollectionFactory
     */
    public function __construct(CollectionFactory $entityTypeCollectionFactory)
    {
        $this->entityTypeCollectionFactory = $entityTypeCollectionFactory;
    }

    /**
     * Get all options dynamically from eav_entity_type table
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [];

            $collection = $this->entityTypeCollectionFactory->create();
            foreach ($collection as $entityType) {
                $this->_options[] = [
                    'label' => __($entityType->getEntityTypeCode()), // Label
                    'value' => $entityType->getEntityTypeId() // Value
                ];
            }
        }
        return $this->_options;
    }
}
