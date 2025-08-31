<?php
namespace Icecube\EavManager\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class IsSystem extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['is_system'] = ($item['is_system']) ? 'Yes' : 'No';
            }
        }
        return $dataSource;
    }
}

?>