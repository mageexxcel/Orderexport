<?php

namespace Excellence\Orderexport\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context, PostDataProcessor $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Excellence_Orderexport::save');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        $data['store_id'] = implode(',', $data['store_id']);
        $data['order_status'] = implode(',', $data['order_status']);
        if(isset($data['customer_group'])){
            $data['customer_group'] = implode(',', $data['customer_group']);
        }
        if(isset($data['order_fields'])){
            $data['order_fields'] = implode(',', $data['order_fields']);
        }
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            $model = $this->_objectManager->create('Excellence\Orderexport\Model\Orderexport');

            $id = $this->getRequest()->getParam('profile_id');
            if ($id) {
                $model->load($id);
            }

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', ['profile_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                
                $model->save();
                $this->messageManager->addSuccess(__('The profile has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['profile_id' => $model->getId(), '_current' => true, 'active_tab' => $this->getRequest()->getParam('active_tab')]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the profile.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['profile_id' => $this->getRequest()->getParam('profile_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
