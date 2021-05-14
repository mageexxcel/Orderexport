<?php

namespace Excellence\Orderexport\Controller\Adminhtml\Index;

class Generate extends \Magento\Backend\App\Action
{
    protected $_orderExportFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Excellence\Orderexport\Model\OrderexportFactory $orderExportFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_orderExportFactory = $orderExportFactory;
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
      $model = $this->_orderExportFactory->create();
      $id = $this->getRequest()->getParam('profile_id');
      if ($id) {
        $fileProcessor = $model->exportFile($id);
      } else{
        $fileProcessor = $model->exportFile();
      }
      
      if ($this->getRequest()->getParam('back')) {
          $this->_redirect('*/*/edit', ['profile_id' => $id]);
          return;
      }
      $this->_redirect('*/*/');
    }
}
