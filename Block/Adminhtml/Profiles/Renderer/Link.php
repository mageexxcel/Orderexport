<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Excellence\Orderexport\Block\Adminhtml\Profiles\Renderer;

/**
 * Render the link in the profile grid
 */
class Link extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render the column block
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $lastFileData = json_decode($row->getData('last_generated_file'), true);
        $dataToReturn = array();
        if(!isset($lastFileData)){
            return;
        }
        foreach ($lastFileData as $data) {
            if($data['is_link']){
                $dataToReturn[] = $data['label'].": "." <a target='_blank' href='https://drive.google.com/open?id=".$data['value']."'>https://drive.google.com/open?id=".$data['value']."</a>";
            } else{
                $dataToReturn[] = $data['label'].": ".$data['value'];
            }
        }
        return implode("<br/><hr>", $dataToReturn);
    }
}
