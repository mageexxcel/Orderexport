<?php

namespace Excellence\Orderexport\Model\Adminhtml\Config\Source;
 
class FormFileNameFormat implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
    {
    	return [
    			1 => 'filename.ext',
    			2 => 'YYYY-MM-DD'.'-'.'filename.ext',
    			3 => 'YYYY-MM-DD-hh-mm-ss'.'-'.'filename.ext',
    			4 => 'filename'.'-'.'YYYY-MM-DD'.'.ext',
    			5 => 'filename'.'-'.'YYYY-MM-DD-hh-mm-ss'.'.ext',
    			6 => 'YYYY-MM-DD-hh-mm-ss'.'.ext'
			];

    }
}