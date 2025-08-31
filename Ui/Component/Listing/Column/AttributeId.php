<?php

namespace Icecube\EavManager\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class AttributeId extends Column
{
    /**
     * __construct
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $link = "magesan.com";
                if ($link) {
                    $item[$fieldName] = "<a target='_blank' href='" . $link . "'>" . $item["attribute_id"] . "</a>";
                } else {
                    $item[$fieldName] = "<a href='javascript:void(0)'>" . $item["attribute_id"] . "</a>";
                }
            }
        }

        return $dataSource;
    }
}