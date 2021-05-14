<?php

namespace Excellence\Orderexport\Model\Adminhtml\Config\Source;
 
class FileNameFormat implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
    {
    	return [
    			1 => 'filename.ext',
    			2 => date('Y-m-d').'-'.'filename.ext',
    			3 => date('Y-m-d-H-i-s').'-'.'filename.ext',
    			4 => 'filename'.'-'.date('Y-m-d').'.ext',
    			5 => 'filename'.'-'.date('Y-m-d-H-i-s').'.ext',
    			6 => date('Y-m-d-H-i-s').'.ext'
			];

    }
}