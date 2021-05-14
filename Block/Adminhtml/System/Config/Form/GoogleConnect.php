<?php

namespace Excellence\Orderexport\Block\Adminhtml\System\Config\Form;

class GoogleConnect extends \Magento\Config\Block\System\Config\Form\Field
{
    const BUTTON_TEMPLATE = 'system/config/button/button.phtml';

    protected $_storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Excellence\Orderexport\Model\GoogleDriveFactory $googleDriveFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager     = $storeManager;
        $this->_objectManager    = $objectManager;
        $this->_googleDriveFactory = $googleDriveFactory;
    }
 
     /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }
        return $this;
    }
    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
     /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        //$originalData = $element->getOriginalData();
        $this->addData(
            [
                'id'        => 'google_drive_connect',
                'button_label'     => _('Connect to Google Drive')            ]
        );
        return $this->_toHtml();
    }

    public function pageUrl(){
        // return $this->_storeManager->getStore()->getUrl('orderexport');
        return ($this->getBaseUrl().'orderexport/');
    }

    public function isGoogleConnected()
    {
        $googleDriveModel = $this->_googleDriveFactory->create();
        if(count($googleDriveModel->getCollection()->getData()) > 0){
            return true;
        }
        return false;
    }

}