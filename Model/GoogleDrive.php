<?php
namespace Excellence\Orderexport\Model;
class GoogleDrive extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'excellence_orderexport_googledrive';

    protected function _construct()
    {
        $this->_init('Excellence\Orderexport\Model\ResourceModel\GoogleDrive');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
