<?php
namespace Excellence\Orderexport\Block\Adminhtml\Orderexport\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        \Excellence\Orderexport\Model\Adminhtml\Config\Source\FormFileNameFormat $fileNameFormat,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_fileNameFormat = $fileNameFormat;
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

        $form->setHtmlIdPrefix('orderexport_main_');

        $fieldsetConfig = $form->addFieldset('config_fieldset', ['legend' => __('Configuration')]);

        if ($model->getId()) {
            $fieldsetConfig->addField('profile_id', 'hidden', ['name' => 'profile_id']);
        }

        $fieldsetConfig->addField(
            'file_name_prefix',
            'text',
            [
                'name' => 'file_name_prefix',
                'label' => __('File Name'),
                'title' => __('File Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldsetConfig->addField(
            'file_type',
            'select',
            [
                'name' => 'file_type',
                'label' => __('File Type'),
                'title' => __('File Type'),
                'required' => true,
                'options' => [
                    1 => 'csv',
                    2 => 'txt',
                    3 => 'xml'
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $fieldsetConfig->addField(
            'file_name_format',
            'select',
            [
                'name' => 'file_name_format',
                'label' => __('File Name Format'),
                'title' => __('File Name Format'),
                'required' => true,
                'options' => $this->_fileNameFormat->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        
        
        $this->_eventManager->dispatch('adminhtml_orderexport_edit_tab_main_prepare_form', ['form' => $form]);

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
        return __('Configuration');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Configuration');
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
