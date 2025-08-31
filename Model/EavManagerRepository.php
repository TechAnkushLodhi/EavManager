<?php
namespace Icecube\EavManager\Model;

use Icecube\EavManager\Api\EavManagerRepositoryInterface;
use Icecube\EavManager\Api\Data\EavManagerInterface;
use Icecube\EavManager\Model\ResourceModel\EavManager as ResourceEavManager;
use Icecube\EavManager\Model\ResourceModel\EavManager\CollectionFactory as EavManagerCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class EavManagerRepository implements EavManagerRepositoryInterface
{
    protected $resource;
    protected $eavManagerFactory;
    protected $eavManagerCollectionFactory;

    public function __construct(
        ResourceEavManager $resource,
        \Icecube\EavManager\Model\EavManagerFactory $eavManagerFactory,
        EavManagerCollectionFactory $eavManagerCollectionFactory
    ) {
        $this->resource = $resource;
        $this->eavManagerFactory = $eavManagerFactory;
        $this->eavManagerCollectionFactory = $eavManagerCollectionFactory;
    }

    /**
     * Save EavManager
     *
     * @param EavManagerInterface $attribute
     * @return EavManagerInterface
     * @throws CouldNotSaveException
     */
    public function save(EavManagerInterface $attribute)
    {
        try {
            $this->resource->save($attribute);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save attribute: %1', $e->getMessage()));
        }
        return $attribute;
    }

    /**
     * Get EavManager by ID
     *
     * @param int $Id
     * @return EavManagerInterface
     * @throws NoSuchEntityException
     */
    public function getById($Id)
    {
        $eavManager = $this->eavManagerFactory->create();
        $this->resource->load($eavManager, $Id);
        if (!$eavManager->getId()) {
            throw new NoSuchEntityException(__('Attribute with ID "%1" does not exist.', $Id));
        }
        return $eavManager;
    }

    /**
     * Delete EavManager
     *
     * @param EavManagerInterface $attribute
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(EavManagerInterface $attribute)
    {
        try {
            $this->resource->delete($attribute);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete attribute: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * Delete EavManager by ID
     *
     * @param int $Id
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($Id)
    {
        return $this->delete($this->getById($Id));
    }
}
