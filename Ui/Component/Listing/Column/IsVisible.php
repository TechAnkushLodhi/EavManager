<?php
namespace Icecube\EavManager\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class IsVisible extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['is_visible'] = ($item['is_visible']) ? 'Yes' : 'No';
            }
        }
        return $dataSource;
    }
}

?>