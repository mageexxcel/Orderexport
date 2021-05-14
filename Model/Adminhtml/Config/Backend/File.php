<?php
 
namespace Excellence\Orderexport\Model\Adminhtml\Config\Backend;
 
class File extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @return string[]
     */
    public function getAllowedExtensions() {
        return ['json'];
    }
}