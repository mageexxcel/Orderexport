<?php

namespace Excellence\Orderexport\Block;

/**
 * Orderexport content block
 */
class GoogleConnect extends \Magento\Framework\View\Element\Template
{
    public function __construct(
    	\Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Excellence\Orderexport\Model\GoogleDriveFactory $googleDriveFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Framework\View\Element\Template\Context $context
    )
    {
        parent::__construct($context);
        $this->_storeManager     = $storeManager;
        $this->_objectManager    = $objectManager;
        $this->_googleDriveFactory = $googleDriveFactory;
        $this->_scopeConfigObject = $scopeConfigObject;
        $this->_logo = $logo;
    }
    protected function _prepareLayout()
    {
    	
    }

    public function getCredentialsFilePath()
    {
    	return BP.'/pub/media/google_drive_secret_file/'.$this->_scopeConfigObject->getValue(
                    'orderexport/google_drive/client_secret_file'
                    );
    }

    public function saveData($refreshKey, $redirectUri){
    	$googleDriveModel = $this->_googleDriveFactory->create();
    	$lastItem = $googleDriveModel->getCollection()->getLastItem()->getData();
    	if(count($lastItem)>0){
    		$googleDriveModel->load($lastItem['excellence_orderexport_googledrive_id']);
    	}
    	if(!empty($refreshKey) && !empty($redirectUri)){
    		try {
		    	$googleDriveModel->setData('refresh_key', $refreshKey);
		    	$googleDriveModel->setData('redirect_uri', $redirectUri);
		    	$googleDriveModel->save();
		    	return true;
    		} catch (Exception $e) {
    			echo __('Something went wrong, please try again.<br>Error: '.$e->getMessage());
    			return false;
    		}
    	}
    	else{
    		echo __('Something went wrong, please try again.<br>Error: '.$e->getMessage());
			return false;
    	}
    	return false;
    }
    public function getLogoSrc()
    {    
        return $this->_logo->getLogoSrc();
    }
}
