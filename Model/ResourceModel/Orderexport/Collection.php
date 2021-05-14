<?php

/**
 * Orderexport Resource Collection
 */
namespace Excellence\Orderexport\Model\ResourceModel\Orderexport;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */

    protected $_idFieldName = 'profile_id';

    protected function _construct()
    {
        $this->_init('Excellence\Orderexport\Model\Orderexport', 'Excellence\Orderexport\Model\ResourceModel\Orderexport');
    }
}
