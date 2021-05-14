<?php
/**
 * Adminhtml orderexport list block
 *
 */
namespace Excellence\Orderexport\Block\Adminhtml;

class Orderexport extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_orderexport';
        $this->_blockGroup = 'Excellence_Orderexport';
        $this->_headerText = __('OrderExport');
        $this->_addButtonLabel = __('Add New Profile');
        parent::_construct();
        if ($this->_isAllowedAction('Excellence_Orderexport::save')) {
            $this->buttonList->update('add', 'label', __('Add New Profile'));
        } else {
            $this->buttonList->remove('add');
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
}
