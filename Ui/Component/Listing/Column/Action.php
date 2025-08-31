<?php
namespace Icecube\EavManager\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Action extends Column
{
    /** Url path */
    const EAV_URL_PATH_EDIT = 'env_manager/attribute/edit';
    const EAV_URL_PATH_DELETE = 'env_manager/attribute/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * Constructor
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = 'actions'; // Fix for correct column name

                if (isset($item['entity_id'])) {
                    $editUrl = $this->urlBuilder->getUrl(self::EAV_URL_PATH_EDIT, ['entity_id' => $item['entity_id']]);
                    $deleteUrl = $this->urlBuilder->getUrl(self::EAV_URL_PATH_DELETE, ['entity_id' => $item['entity_id']]);

                    $title = $this->escaper->escapeHtml($item['attribute_code'] ?? 'Attribute');

                    $item[$name] = [
                        'edit' => [
                            'href' => $editUrl,
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $deleteUrl,
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete %1', $title),
                                'message' => __('Are you sure you want to delete the %1 record?', $title)
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
