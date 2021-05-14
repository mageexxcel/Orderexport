<?php
namespace Excellence\Orderexport\Model\ResourceModel\GoogleDrive;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Excellence\Orderexport\Model\GoogleDrive','Excellence\Orderexport\Model\ResourceModel\GoogleDrive');
    }
}
