<?php
namespace Excellence\Orderexport\Block\Adminhtml\Orderexport\Edit\Tab;

/**
 * Cms page edit form Template tab
 */
class Template extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        \Magento\Sales\Model\Config\Source\Order\Status $orderStatus,
        \Excellence\Orderexport\Model\Adminhtml\Config\Source\OrderFields $orderFields,
        \Magento\Config\Model\Config\Source\Yesno $yesNoModel,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_orderStatus = $orderStatus;
        $this->_orderFields = $orderFields;
        $this->_yesNoModel = $yesNoModel;
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

        $form->setHtmlIdPrefix('orderexport_template_');

        $fieldsetTemplate = $form->addFieldset('template_fieldset', ['legend' => __('File Template')]);

        $selectAll = "<div style='clear:both;'></div><button id='selectAll'>".__('Select All')."</button>&nbsp;&nbsp;&nbsp;&nbsp;<button id='deselectAll'>".__('Deselect All')."</button>";

        $comment = "<div style='clear:both;'></div><font size=2 color='#666666'>".__("Select the fields you want in the exported file.")."</font>";

        $fieldsetTemplate->addField(
            'order_fields',
            'multiselect',
            [
                'label' => __('Fields to Show'),
                'title' => __('Fields to Show'),
                'name' => 'order_fields',
                'required' => true,
                'after_element_html' => $selectAll.$comment,
                'values' => $this->_orderFields->toOptionArray()
            ]
        );
        
        
        $this->_eventManager->dispatch('adminhtml_orderexport_edit_tab_template_prepare_form', ['form' => $form]);

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
        return __('Template');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Template');
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
