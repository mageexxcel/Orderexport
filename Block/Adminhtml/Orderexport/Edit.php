<?php
namespace Excellence\Orderexport\Block\Adminhtml\Orderexport;

/**
 * Admin Orderexport page
 *
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $frontUrlModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_frontUrlModel = $frontUrlModel;
        $this->_storeManager=$storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'profile_id';
        $this->_blockGroup = 'Excellence_Orderexport';
        $this->_controller = 'adminhtml_orderexport';

        parent::_construct();

        if ($this->_isAllowedAction('Excellence_Orderexport::save')) {
            $this->buttonList->update('save', 'label', __('Save Profile'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Excellence_Orderexport::orderexport_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Profile'));
        } else {
            $this->buttonList->remove('delete');
        }
        if ($this->getRequest()->getParam('profile_id')) {

            $this->buttonList->add(
                'generate',[
                    'label' => __('Generate'),
                    'onclick' => "if(confirm('".__('This action may take some time depending upon the profile settings. Are you sure you want to run it now ?')."')) setLocation(window.location.href = '".$this->getUrl('orderexport/*/generate', ['profile_id' => $this->getRequest()->getParam('profile_id'), '_current' => true, 'back' => 'edit'])."')"
                ]
            );
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('orderexport')->getId()) {
            return __("Edit Profile '%1'", $this->escapeHtml($this->_coreRegistry->registry('orderexport')->getTitle()));
        } else {
            return __('New Profile');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('orderexport/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
