<?php
namespace Excellence\Orderexport\Block\Adminhtml\Orderexport\Edit\Tab;

/**
 * Cms page edit form cron tab
 */
class Cron extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        \Magento\Config\Model\Config\Source\Yesno $yesNoModel,
        \Excellence\Orderexport\Model\Adminhtml\Config\Source\CronOptions $cronOptions,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_yesNoModel = $yesNoModel;
        $this->_cronOptions = $cronOptions;
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

        $form->setHtmlIdPrefix('orderexport_cron_');

        $fieldset = $form->addFieldset('cron_fieldset', ['legend' => __('Cron')]);

        $fieldset->addField(
            'auto_cron',
            'select',
            [
                'name' => 'auto_cron',
                'label' => __('Auto Execute Profile'),
                'title' => __('Auto Execute Profile'),
                'class' => 'required-entry',
                'required' => true,
                'values' => $this->_yesNoModel->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'cron_period',
            'select',
            [
                'name' => 'cron_period',
                'label' => __('Cron Period'),
                'title' => __('Cron Period'),
                'class' => 'required-entry',
                'required' => false,
                'values' => $this->_cronOptions->toOptionArray(),
            ]
        );

        $comment = "<font size=2 color='#666666'>&uarr;&nbsp;".__("Enter Time Period in Minutes.")."</font></p>";

        $fieldset->addField(
            'custom_period',
            'text',
            [
                'name' => 'custom_period',
                'label' => __('Custom Period'),
                'title' => __('Custom Period'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'after_element_html' => $comment
            ]
        );
        
        $this->_eventManager->dispatch('adminhtml_orderexport_edit_tab_cron_prepare_form', ['form' => $form]);

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
        return __('Cron');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Cron');
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
