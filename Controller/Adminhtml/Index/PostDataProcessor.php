<?php

namespace Excellence\Orderexport\Controller\Adminhtml\Index;

class PostDataProcessor
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Core\Model\Layout\Update\ValidatorFactory $validatorFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    public function filter($data)
    {
        $inputFilter = new \Zend_Filter_Input(
            ['published_at' => $this->dateFilter],
            [],
            $data
        );
        $data = $inputFilter->getUnescaped();
        return $data;
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if someone item is invalid
     */
    public function validate($data)
    {
        $errorNo = true;
        if($data['email_file']){
            if(empty($data['email_recipient']) || !(filter_var($data['email_recipient'], FILTER_VALIDATE_EMAIL))){
                $this->messageManager->addError(__('Please Enter valid Email.'));
                $errorNo = false;
            }
        }
        if($data['use_ftp']){
            if(empty($data['ftp_host'])){
                $this->messageManager->addError(__('Please Enter valid FTP Host.'));
                $errorNo = false;
            }
            if(empty($data['ftp_login'])){
                $this->messageManager->addError(__('Please Enter valid FTP Login.'));
                $errorNo = false;
            }
            if(empty($data['ftp_directory'])){
                $this->messageManager->addError(__('Please Enter valid FTP Directory.'));
                $errorNo = false;
            }
        }
        return $errorNo;
    }
}
