<?php
namespace Excellence\Orderexport\Block\Adminhtml\Orderexport\Edit\Tab;

/**
 * Cms page edit form Filters tab
 */
class Filters extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Magento\Config\Model\Config\Source\Yesno $yesNoModel,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_orderStatus = $orderStatus;
        $this->_customerGroup = $customerGroup;
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

        $form->setHtmlIdPrefix('orderexport_filters_');

        $fieldsetOrder = $form->addFieldset('order_fieldset', ['legend' => __('Order Filter')]);

        $fieldsetOrder->addField(
            'store_id',
            'multiselect',
            [
                'label' => __('Export from Store View'),
                'title' => __('Export from Store View'),
                'name' => 'store_id',
                'class' => 'required-entry',
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, false)
            ]
        );

        $fieldsetOrder->addField(
            'starting_order_id',
            'text',
            [
                'name' => 'starting_order_id',
                'label' => __('Start with order #'),
                'title' => __('Start with order #'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $statusArray = $this->_orderStatus->toOptionArray();
        array_shift($statusArray);
        
        $fieldsetOrder->addField(
            'order_status',
            'multiselect',
            [
                'label' => __('Order Status'),
                'title' => __('Order Status'),
                'name' => 'order_status',
                'class' => 'required-entry',
                'required' => true,
                'height' => '50px',
                'values' => $statusArray
            ]
        );

        $fieldsetCustomerGroup = $form->addFieldset('customer_group_fieldset', ['legend' => __('Customer Group')]);

        $fieldsetCustomerGroup->addField(
            'filter_by_customer_group',
            'select',
            [
                'name' => 'filter_by_customer_group',
                'label' => __('Filter by Customer Group'),
                'title' => __('Filter by Customer Group'),
                'class' => 'required-entry',
                'required' => true,
                'values' => $this->_yesNoModel->toOptionArray(),
            ]
        );

        $fieldsetCustomerGroup->addField(
            'customer_group',
            'multiselect',
            [
                'label' => __('Customer Group (s)'),
                'title' => __('Customer Group (s)'),
                'name' => 'customer_group',
                'required' => false,
                'values' => $this->_customerGroup->toOptionArray()
            ]
        );
        
        
        $this->_eventManager->dispatch('adminhtml_orderexport_edit_tab_filters_prepare_form', ['form' => $form]);

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
        return __('Filters');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Filters');
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
