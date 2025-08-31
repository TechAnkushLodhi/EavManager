<?php
namespace Icecube\EavManager\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class IsRequired extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['is_required'] = ($item['is_required']) ? 'Yes' : 'No';
            }
        }
        return $dataSource;
    }
}

?>