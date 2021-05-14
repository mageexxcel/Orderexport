<?php
/**
 * Adminhtml orderexport list block
 *
 */
namespace Excellence\Orderexport\Block\Adminhtml;

class RunAllAction extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
 
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'run_all_profiles',
            'label' => __('Run All Profiles'),
            'class' => 'add',
            'button_class' => '',
            'onclick' => "if(confirm('".__('Generating export files can take a while. Are you sure you want to generate it now ?')."')) setLocation(window.location.href = '".$this->getUrl('orderexport/*/generate')."')"
        ];
        $this->buttonList->add('run_all', $addButtonProps);
 
        return parent::_prepareLayout();
    }
}
