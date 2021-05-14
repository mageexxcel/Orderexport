<?php

namespace Excellence\Orderexport\Model\Adminhtml\Config\Source;
 
class CronOptions implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
    {
    	return [
    			15 => __('15 Minutes'),
                30 => __('30 Minutes'),
                60 => __('1 Hour'),
                120 => __('2 Hours'),
                1440 => __('1 Day'),
                'custom' => __('Custom')
			];

    }
}