<?php
/**
 * Copyright © Icecube Digital All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Icecube\EavManager\Model\EavManager\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class StoreOptions implements OptionSourceInterface
{
    protected $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Get store options with website and store views in hierarchical structure
     */
    public function toOptionArray()
    {
        $options = [];

        // All Store Views
        $options[] = ['label' => __('All Store Views'), 'value' => 0,'selected' => true];

        // Get Websites and their Stores
        $websites = $this->storeManager->getWebsites();
        foreach ($websites as $website) {
            $websiteLabel = $website->getName();
            $websiteId = 'website_' . $website->getId(); // Unique value for website

            // Website as a Parent Option
            $websiteOption = [
                'label' => $websiteLabel,
                'value' => []
            ];

            // Get Store Groups and Stores
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                
                // Store Group Name
                $groupLabel = $group->getName();
                $groupOption = [
                    'label' => $groupLabel,
                    'value' => []
                ];

                foreach ($stores as $store) {
                    $groupOption['value'][] = [
                        'label' => $store->getName(),
                        'value' => $store->getId()
                    ];
                }

                // Add Group Under Website
                $websiteOption['value'][] = $groupOption;
            }

            $options[] = $websiteOption;
        }

        return $options;
    }
}
