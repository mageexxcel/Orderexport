<?php

namespace Excellence\Orderexport\Model\ResourceModel;

/**
 * Orderexport Resource Model
 */
class Orderexport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('excellence_orderexport_profiles', 'profile_id');
    }
}
