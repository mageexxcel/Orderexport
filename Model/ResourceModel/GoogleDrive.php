<?php
namespace Excellence\Orderexport\Model\ResourceModel;
class GoogleDrive extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('excellence_orderexport_googledrive','excellence_orderexport_googledrive_id');
    }
}
