<?php
namespace Excellence\Orderexport\Block\Adminhtml\Orderexport\Edit\Tab;

/**
 * Cms page edit form output tab
 */
class Output extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('orderexport');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Excellence_Orderexport::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('orderexport_output_');

        $fieldsetStorage = $form->addFieldset('output_storage_fieldset', ['legend' => __('Storage Setting')]);

        $fieldsetStorage->addField(
            'file_directory',
            'text',
            [
                'name' => 'file_directory',
                'label' => __('File Directory'),
                'title' => __('File Directory'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        // FTP UPLOAD SETTINGS

        $fieldsetGDrive = $form->addFieldset('output_google_drive_fieldset', ['legend' => __('Google Drive Configuration')]);

        $fieldsetGDrive->addField(
            'use_google_drive',
            'select',
            [
                'name' => 'use_google_drive',
                'label' => __('Upload On Google Drive'),
                'title' => __('Upload On Google Drive'),
                'class' => 'required-entry',
                'required' => true,
                'disabled' => $isElementDisabled,
                'values' => [
                                0 => __('No'),
                                1 => __('Yes')
                            ]
            ]
        );

        $comment = "<font size=2 color='#666666'>".__("If it's set to 'no', exported files will be saved on root folder of Google Drive.")."</font></p>";

        $fieldsetGDrive->addField(
            'use_separate_directory',
            'select',
            [
                'name' => 'use_separate_directory',
                'label' => __('Use Separate Directory for The Profile'),
                'title' => __('Use Separate Directory for The Profile'),
                'class' => 'required-entry',
                'required' => false,
                'disabled' => $isElementDisabled,
                'after_element_html' => $comment,
                'values' => [
                                0 => __('No'),
                                1 => __('Yes')
                            ]
            ]
        );

        // FTP UPLOAD SETTINGS

        $fieldsetFtp = $form->addFieldset('output_ftp_fieldset', ['legend' => __('FTP/SFTP Setting')]);

        $fieldsetFtp->addField(
            'use_ftp',
            'select',
            [
                'name' => 'use_ftp',
                'label' => __('Upload by FTP'),
                'title' => __('Upload by FTP'),
                'class' => 'required-entry',
                'required' => true,
                'disabled' => $isElementDisabled,
                'values' => [
                                0 => __('No'),
                                1 => __('Yes')
                            ]
            ]
        );

        $fieldsetFtp->addField(
            'ftp_host',
            'text',
            [
                'name' => 'ftp_host',
                'label' => __('Host'),
                'title' => __('Host'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldsetFtp->addField(
            'ftp_port',
            'text',
            [
                'name' => 'ftp_port',
                'label' => __('FTP Port'),
                'title' => __('FTP Port'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldsetFtp->addField(
            'ftp_login',
            'text',
            [
                'name' => 'ftp_login',
                'label' => __('FTP Login'),
                'title' => __('FTP Login'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $toggleHtml = "<span class='ftp-password-toggle' id='show'>".__("Show")."</span><span class='ftp-password-toggle' style='display:none;' id='hide'>".__("Hide")."</span>";

        $fieldsetFtp->addField(
            'ftp_password',
            'password',
            [
                'name' => 'ftp_password',
                'label' => __('FTP Password'),
                'title' => __('FTP Password'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'after_element_html' => $toggleHtml
            ]
        );

        $fieldsetFtp->addField(
            'ftp_directory',
            'text',
            [
                'name' => 'ftp_directory',
                'label' => __('File Directory (FTP)'),
                'title' => __('File Directory (FTP)'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldsetFtp->addField(
            'use_sftp',
            'select',
            [
                'name' => 'use_sftp',
                'label' => __('Use SFTP'),
                'title' => __('Use SFTP'),
                'class' => 'required-entry',
                'disabled' => $isElementDisabled,
                'required' => false,
                'values' => [
                                0 => __('No'),
                                1 => __('Yes')
                            ]
            ]
        );

        $fieldsetFtp->addField(
            'delete_local_file',
            'select',
            [
                'name' => 'delete_local_file',
                'label' => __('Delete Local File after FTP Upload'),
                'title' => __('Delete Local File after FTP Upload'),
                'class' => 'required-entry',
                'disabled' => $isElementDisabled,
                'required' => false,
                'values' => [
                                0 => __('No'),
                                1 => __('Yes')
                            ]
            ]
        );

        // SEND MAIL SETTINGS

        $fieldsetEmail = $form->addFieldset('output_email_fieldset', ['legend' => __('Email Setting')]);

        $fieldsetEmail->addField(
            'email_file',
            'select',
            [
                'name' => 'email_file',
                'label' => __('Send Exported File to Email'),
                'title' => __('Send Exported File to Email'),
                'class' => 'required-entry',
                'disabled' => $isElementDisabled,
                'required' => true,
                'values' => [
                                0 => __('No'),
                                1 => __('Yes')
                            ]
            ]
        );

        $fieldsetEmail->addField(
            'email_recipient',
            'text',
            [
                'name' => 'email_recipient',
                'label' => __('Email Recipient'),
                'title' => __('Email Recipient'),
                'required' => false,
                'validate_rules' => 'a:1:{s:16:"input_validation";s:5:"email";}',
                'disabled' => $isElementDisabled
            ]
        );
        
        $this->_eventManager->dispatch('adminhtml_orderexport_edit_tab_output_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Output');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Output');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
}
