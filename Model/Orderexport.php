<?php

namespace Excellence\Orderexport\Model;

include_once BP . '/app/code/Excellence/Orderexport/lib/google/vendor/autoload.php';
include_once BP."/app/code/Excellence/Orderexport/lib/google/templates/base.php";

/**
 * Orderexport Model
 *
 * @method \Excellence\Orderexport\Model\Resource\Page _getResource()
 * @method \Excellence\Orderexport\Model\Resource\Page getResource()
 */

use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;

class Orderexport extends \Magento\Framework\Model\AbstractModel
{
	protected $_orderFactory;
    protected $_directoryList;
    protected $_transportBuilder;
    protected $_logger;
    protected $_mimeTypes = array(
							    1 =>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
							    2 =>'text/plain',
							    3 =>'text/xml'
							);

	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Filesystem\Io\Ftp $ioFtp,
        \Magento\Framework\Filesystem\Io\Sftp $ioSftp,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Excellence\Orderexport\Model\ResourceModel\Orderexport $resource,
        \Excellence\Orderexport\Model\ResourceModel\Orderexport\Collection $resourceCollection,
        \Excellence\Orderexport\Model\Adminhtml\Config\Source\FileNameFormat $fileNameFormat,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Filesystem\Directory\ReadFactory $directoryRead,
        TransportBuilder $transportBuilder,
        \Excellence\Orderexport\Model\Mail\TransportBuilder $excTransportBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $timezoneInterface,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\Helper\Data $helper,
        \Excellence\Orderexport\Model\GoogleDriveFactory $googleDriveFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        array $data = []
    )
    {
    	$this->_ioFtp = $ioFtp;
        $this->_ioSftp = $ioSftp;
        $this->_ioWrite = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $this->_orderFactory = $orderFactory;
        $this->_directoryList = $directoryList;
        $this->_fileNameFormat = $fileNameFormat;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_directoryRead = $directoryRead->create($this->getAbsoluteRootDir());
        $this->_transportBuilder = $transportBuilder;
        $this->_excTransportBuilder = $excTransportBuilder;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_logger = $logger;
        $this->helper = $helper;
        $this->_googleDriveFactory = $googleDriveFactory;
        $this->_scopeConfigObject = $scopeConfigObject;
        parent::__construct(
                $context, $registry, $resource, $resourceCollection, $data
        );
    }


    public function exportFile($profile_id = null)
    {
    	if(!($this->_scopeConfigObject->getValue('orderexport/basic_setting/enabled'))){
    		$this->_messageManager->addNotice(__("The module is disabled. Please enable the module from <a href='%1'>here</a> before proceeding to export.", $this->helper->getUrl('adminhtml/system_config/edit/section/orderexport')));
    		return;
    	}
    	try{

    		$fileNameArray = $this->_fileNameFormat->toOptionArray();

	    	$fileExtensionArray = [
				                    1 => 'csv',
				                    2 => 'txt',
				                    3 => 'xml'
				                ];

	    	$orderModel = $this->_orderFactory->create();

			if(isset($profile_id) && $profile_id != null){
				$fileNames = array();

				$profile = $this->load($profile_id);
				$profileData = $profile->getData();

				$orderData = $orderModel->getCollection();
				
				$orderData = $this->getOrderData($profileData, $orderData);
				$lastRow = end($orderData);
				$last_exported_order_id = $lastRow['entity_id'];

				if(count($orderData) < 1){
					$this->_messageManager->addNotice(__("No Orders to Export."));
					return;
				}

	    		$directoryPath = BP."/".$profileData['file_directory'];
	    		if (!is_dir($directoryPath)) {
					mkdir($directoryPath, 0777);
				}
				$tempFileName = $fileNameArray[$profileData['file_name_format']]; //temporary file name

				$tempFileName = str_replace("filename", $profileData['file_name_prefix'], $tempFileName); //changing name

				$fileName = str_replace("ext", $fileExtensionArray[$profileData['file_type']], $tempFileName); //changing extension

				$filePath = $directoryPath."/".$fileName;

				$isExported = false;

				// Generating File according to profile configuration:
				switch ($profileData['file_type']) {
	                case 1:
	                   	if($this->generateCsv($orderData, $filePath)){
	                		$isExported = true;
	                   	}
	                   	break;
	                case 2:
	                    if($this->generateTxt($orderData, $filePath)){
	                		$isExported = true;
	                   	}
	                    break;
	                case 3:
	                    if($this->generateXML($orderData, $filePath)){
	                		$isExported = true;
	                   	}
	                    break;    
	            }
	            $fileUrl = null;
	            if($isExported){
	            	$profile->setData('last_exported_order_id', $last_exported_order_id);
	            	$profile->save();

	            	$lastGeneratedFile = array();

	            	$filenames[] = $fileName;
	            	if($profileData['use_ftp']){
	            		// Upload on FTP
	            		$this->ftpUpload($profileData['use_sftp'], 0, $profileData['ftp_host'], $profileData['ftp_login'], $profileData['ftp_password'], $profileData['ftp_directory'], $profileData['file_directory'], $fileName, $profileData['ftp_port']);
	            		$lastGeneratedFile[] = ['label' => __('File Path (FTP)'), 'value' => $profileData['ftp_directory']."/".$fileName, 'is_link' => 0 ];
	            		if($profileData['delete_local_file']){
	            			unlink($filePath); //Delete Local File
	            		} else{
	            			$lastGeneratedFile[] = ['label' => __('File Path'), 'value' => $profileData['file_directory']."/".$fileName, 'is_link' => 0 ];
	            		}

	            	} else{
	            		$lastGeneratedFile[] = ['label' => __('File Path'), 'value' => $profileData['file_directory']."/".$fileName, 'is_link' => 0 ];
		            	$this->_messageManager->addSuccess(__('Orders has been exported.'));
	            	}

	            	if($profileData['use_google_drive']){
	            		$result = $this->uploadToGoogleDrive($fileName, $filePath, $profileData);
	            		if($result){
	            			$lastGeneratedFile[] = ['label' => __('Google Drive URL'), 'value' => $result, 'is_link' => 1 ];
	            			$this->_messageManager->addSuccess(__('File has been saved to Google Drive.'));
	            		} else{
	            			$this->_messageManager->addSuccess(__('Some error occured while accessing Google Drive.'));
	            		}
	            	}
		            
		            if($profileData['email_file']){
		            	$files = [['filename'  => $fileName, 'filepath' => $filePath, 'type' => $profileData['file_type']]];
		            	$emailSubject = 'Order Export | Profile Id: '.$profileData['profile_id'];
		            	$this->sendEmail($profileData['email_recipient'], $emailSubject, $files);
		            	$this->_messageManager->addSuccess(__('File has been sent via mail.'));
		            }	
		            
		            $profile->setData('last_generated_file', json_encode($lastGeneratedFile));
					$profile->save();
	            }
	            				
				return;
    		}

	    	$profileCollection = $this->getCollection();
	    	foreach ($profileCollection as $profile) {
	    		$profileData = $profile->getData();

	    		$orderData = $orderModel->getCollection();
				
				$orderData = $this->getOrderData($profileData, $orderData);
				$lastRow = end($orderData);
				$last_exported_order_id = $lastRow['entity_id'] ?? 'default';

				if(count($orderData) < 1){
					$this->_messageManager->addNotice(__('Profile ').$profileData['profile_id'].__(': No Data to Export'));
					continue;
				}

	    		
	    		$directoryPath = BP."/".$profileData['file_directory'];
	    		if (!is_dir($directoryPath)) {
					mkdir($directoryPath, 0777);
				}
				$tempFileName = $fileNameArray[$profileData['file_name_format']]; //temporary file name

				$tempFileName = str_replace("filename", $profileData['file_name_prefix'], $tempFileName); //changing name

				$fileName = str_replace("ext", $fileExtensionArray[$profileData['file_type']], $tempFileName); //changing extension

				$filePath = $directoryPath."/".$fileName;

				switch ($profileData['file_type']) {
	                case 1:
	                   $this->generateCsv($orderData, $filePath);
	                   break;
	                case 2:
	                    $this->generateTxt($orderData, $filePath);
	                    break;
	                case 3:
	                    if(!$this->generateXML($orderData, $filePath)){
	                    	$this->_messageManager->addError(__('Profile ').$profileData['profile_id'].__(': Permission Denied while accessing ').$filePath);
	                    	return;
	                    }
	                    break;    
	            }

	            $profile->setData('last_exported_order_id', $last_exported_order_id);
            	$profile->save();

            	$lastGeneratedFile = array();

	            if($profileData['use_ftp']){
            		// Upload on FTP
            		$this->ftpUpload($profileData['use_sftp'], 0, $profileData['ftp_host'], $profileData['ftp_login'], $profileData['ftp_password'], $profileData['ftp_directory'], $profileData['file_directory'], $fileName, $profileData['ftp_port']);
            		$lastGeneratedFile[] = ['label' => __('File Path (FTP)'), 'value' => $profileData['ftp_directory']."/".$fileName, 'is_link' => 0 ];
            		if($profileData['delete_local_file']){
            			unlink($filePath); //Delete Local File
            		} else{
		            	$lastGeneratedFile[] = ['label' => __('File Path'), 'value' => $profileData['file_directory']."/".$fileName, 'is_link' => 0 ];
            		}

            	} else{
            		$lastGeneratedFile[] = ['label' => __('File Path'), 'value' => $profileData['file_directory']."/".$fileName, 'is_link' => 0 ];
	            	$this->_messageManager->addSuccess(__('Profile ').$profileData['profile_id'].__(': Orders Exported Successfully.'));
            	}

            	if($profileData['use_google_drive']){
            		$result = $this->uploadToGoogleDrive($fileName, $filePath, $profileData);
            		if($result){
            			$lastGeneratedFile[] = ['label' => __('Google Drive URL'), 'value' => $result, 'is_link' => 1 ];
            			$this->_messageManager->addSuccess(__('Profile ').$profileData['profile_id'].__(': File has been saved to Google Drive.'));
            		} else{
            			$this->_messageManager->addSuccess(__('Profile ').$profileData['profile_id'].__(': Some error occured while accessing Google Drive.'));
            		}
            	}

            	if($profileData['email_file']){
	            	$files = [['filename'  => $fileName, 'filepath' => $filePath, 'type' => $profileData['file_type']]];
	            	$emailSubject = 'Order Export | Profile Id: '.$profileData['profile_id'];
	            	$this->sendEmail($profileData['email_recipient'], $emailSubject, $files);
	            }

	            $profile->setData('last_generated_file', json_encode($lastGeneratedFile));
				$profile->save();

	    	}
			return;
    	}
    	catch (\Magento\Framework\Model\Exception $e) {
    		$this->_messageManager->addError(__('Exception : ').$e->getMessage());
			return;
        }
	    return;
    }

    public function exportFileCron() //to be executed via cron
    {
    	if(!($this->_scopeConfigObject->getValue('orderexport/basic_setting/enabled'))){
    		return;
    	}
    	try{

    		$fileNameArray = $this->_fileNameFormat->toOptionArray();

	    	$fileExtensionArray = [
				                    1 => 'csv',
				                    2 => 'txt',
				                    3 => 'xml'
				                ];

	    	$orderModel = $this->_orderFactory->create();

	    	$profileCollection = $this->getCollection();
	    	foreach ($profileCollection as $profile) {
	    		$profileData = $profile->getData();

	    		// is auto execution enabled
	    		if(!($profileData['auto_cron'])){
	    			continue;
	    		}

	    		// Get cron period
	    		$cronPeriod = null;
	    		if($profileData['cron_period'] != 'custom'){
	    			$cronPeriod = $profileData['cron_period'];
	    		} else{
	    			$cronPeriod = $profileData['custom_period'];
	    		}
	    		if(empty($cronPeriod)){
	    			continue;
	    		}

	    		// Calculate time difference
	    		$currentDateTime = new \DateTime($this->_timezoneInterface->gmtDate());
				$lastUpdateDateTime = new \DateTime($profileData['last_update']);
				$interval = $currentDateTime->diff($lastUpdateDateTime);
				if($interval < $cronPeriod){
					continue;
				}

	    		$orderData = $orderModel->getCollection();
				
				$orderData = $this->getOrderData($profileData, $orderData);
				$lastRow = end($orderData);
				$last_exported_order_id = $lastRow['entity_id'];

				if(count($orderData) < 1){
					continue;
				}

	    		
	    		$directoryPath = BP."/".$profileData['file_directory'];
	    		if (!is_dir($directoryPath)) {
					mkdir($directoryPath, 0777);
				}
				$tempFileName = $fileNameArray[$profileData['file_name_format']]; //temporary file name

				$tempFileName = str_replace("filename", $profileData['file_name_prefix'], $tempFileName); //changing name

				$fileName = str_replace("ext", $fileExtensionArray[$profileData['file_type']], $tempFileName); //changing extension

				$filePath = $directoryPath."/".$fileName;

				switch ($profileData['file_type']) {
	                case 1:
	                   $this->generateCsv($orderData, $filePath);
	                   break;
	                case 2:
	                    $this->generateTxt($orderData, $filePath);
	                    break;
	                case 3:
	                    if(!$this->generateXML($orderData, $filePath)){
	                    	$this->_logger->addDebug(__('Permission Denied while accessing ').$filePath);
	                    	return;
	                    }
	                    break;    
	            }

	            $profile->setData('last_exported_order_id', $last_exported_order_id);
            	$profile->save();

            	$lastGeneratedFile = array();

	            if($profileData['use_ftp']){
            		// Upload on FTP
            		$this->ftpUpload($profileData['use_sftp'], 0, $profileData['ftp_host'], $profileData['ftp_login'], $profileData['ftp_password'], $profileData['ftp_directory'], $profileData['file_directory'], $fileName, $profileData['ftp_port']);
            		$lastGeneratedFile[] = ['label' => __('File Path (FTP)'), 'value' => $profileData['ftp_directory']."/".$fileName, 'is_link' => 0 ];
            		if($profileData['delete_local_file']){
            			unlink($filePath); //Delete Local File
            		} else{
            			$lastGeneratedFile[] = ['label' => __('File Path'), 'value' => $profileData['file_directory']."/".$fileName, 'is_link' => 0 ];
            		}

            	} else{
            		$lastGeneratedFile[] = ['label' => __('File Path'), 'value' => $profileData['file_directory']."/".$fileName, 'is_link' => 0 ];
	            	$this->_logger->addDebug(__('Orders has been exported.'));
            	}

            	if($profileData['use_google_drive']){
            		$result = $this->uploadToGoogleDrive($fileName, $filePath, $profileData);
            		if($result){
            			$lastGeneratedFile[] = ['label' => __('Google Drive URL'), 'value' => $result, 'is_link' => 1 ];
            			$this->_logger->addDebug(__('File has been saved to Google Drive.'));
            		} else{
            			$this->_logger->addDebug(__('Some error occured while accessing Google Drive.'));
            		}
            	}

            	if($profileData['email_file']){
	            	$files = [['filename'  => $fileName, 'filepath' => $filePath, 'type' => $profileData['file_type']]];
	            	$emailSubject = 'Order Export | Profile Id: '.$profileData['profile_id'];
	            	$this->sendEmail($profileData['email_recipient'], $emailSubject, $files);
	            }
	            
	            $profile->setData('last_generated_file', json_encode($lastGeneratedFile));
				$profile->save();
	    	}
			return;
    	}
    	catch (\Magento\Framework\Model\Exception $e) {
    		$this->_logger->addDebug(__('Exception : ').$e->getMessage());
			return;
        }
	    return;
    }

    public function uploadToGoogleDrive($fileName, $filePath, $profileData)
    {
    	$googleDriveModel = $this->_googleDriveFactory->create();
    	$lastItem = $googleDriveModel->getCollection()->getLastItem()->getData();
    	$redirect_uri = $lastItem['redirect_uri'];
    	$refresh_token = $lastItem['refresh_key'];
    	$fileType = $profileData['file_type'];

    	$oauth_credentials = $this->getGooogleDriveCredentialsFilePath();
    	$client = new \Google_Client();
		$client->setAuthConfig($oauth_credentials);
		$client->setRedirectUri($redirect_uri);
		$client->addScope("https://www.googleapis.com/auth/drive");

		$service = new \Google_Service_Drive($client);
		$client->refreshToken($refresh_token);
		$newtoken = $client->getAccessToken();
		$_SESSION['upload_token'] = $newtoken;
		if (!empty($_SESSION['upload_token'])) {
			$client->setAccessToken($_SESSION['upload_token']);
			if ($client->isAccessTokenExpired()) {
				unset($_SESSION['upload_token']);
			}
		}
		if ($client->getAccessToken()) {

			$folderId = null; $file = null;

			if($profileData['use_separate_directory']){
				$folderId = $profileData['drive_folder_id'];
				if(empty($folderId)){
					$profile = $this->load($profileData['profile_id']);
					$fileMetadata = new \Google_Service_Drive_DriveFile(array(
					  'name' => $profileData['file_name_prefix'],
					  'mimeType' => 'application/vnd.google-apps.folder'));

					$folder = $service->files->create($fileMetadata, array(
					  'fields' => 'id'));
					$folderId = $folder->id;
					$profile->setData('drive_folder_id', $folderId);
					$profile->save();
				}
			}

			if(!empty($folderId)){
				$file = new \Google_Service_Drive_DriveFile(
					array(
						'name' => $fileName,
						'parents' => array($folderId)
					)
				);
			}
			else{
				$file = new \Google_Service_Drive_DriveFile();
				$file->setName($fileName);
			}

			$result = $service->files->create(
				$file,
				array(
					'data' => file_get_contents($filePath),
        			'mimeType' => $this->_mimeTypes[$fileType],
				)
			);
			if(!empty($result->id)){
				return $result->id;
			} else{
				return false;
			}
		}
		return false;
    }

    public function getGooogleDriveCredentialsFilePath()
    {
    	return BP.'/pub/media/google_drive_secret_file/'.$this->_scopeConfigObject->getValue(
                    'orderexport/google_drive/client_secret_file'
                    );
    }

    public function getAbsoluteRootDir()
    {
        $rootDirectory = $this->_directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        return $rootDirectory;
    }

    public function getOrderData($profileData, $orderData)
    {
    	if(isset($profileData['last_exported_order_id'])){
			$orderData = $orderData->addAttributeToFilter('entity_id', array('gt' => $profileData['last_exported_order_id']));
		} else{
			if(isset($profileData['starting_order_id'])){
				$orderData = $orderData->addAttributeToFilter('entity_id', array('gteq' => $profileData['starting_order_id']));
			}
		}
		
		if(isset($profileData['store_id'])){
			$storeIds = explode(',', $profileData['store_id']);
			$orderData = $orderData->addFieldToFilter(
						     'store_id',
						     ['in' => $storeIds]
						);
		}
		if(isset($profileData['order_status'])){
			if(!(($profileData['order_status']) == 1 && $profileData['order_status'] == 'all')){
				$orderStatuses = explode(',', $profileData['order_status']);
				$orderData = $orderData->addFieldToFilter(
							     'status',
							     ['in' => $orderStatuses]
							);
			}
		}
		if($profileData['filter_by_customer_group'] == 1 && isset($profileData['customer_group'])){
			$customerGroup = explode(',', $profileData['customer_group']);
			$orderData = $orderData->addFieldToFilter(
						     'customer_group_id',
						     ['in' => $customerGroup]
						);
		}

		// Now selecting specified fields for order export file

		$orderFields = explode(',', $profileData['order_fields']);
		foreach ($orderFields as $orderField) {
			$orderData = $orderData->addFieldToSelect($orderField);
		}

		return $orderData->getData();
    }

    public function generateCsv($orderData, $filePath)
    {

    	$dataToWrite = array(); //Data to be written in file
    	$headers = array();
    	foreach (array_keys($orderData[0]) as $header) {
    		$header = explode('_',$header);
            $header = implode(' ', $header);
            $header = ucwords($header);
            $headers[] = $header;
    	}

		$dataToWrite[] = implode(',', $headers);
		foreach ($orderData as $data) {
			$dataToWrite[] = implode(',', $data);
		}

		if(!file_exists($filePath)){
			$file = fopen($filePath, 'w') or die("Can't create file");
		} else {
			$file = fopen($filePath,"w");
		}

		foreach ($dataToWrite as $data)
		{
			fputcsv($file,explode(',',$data));
		}

		fclose($file);
		return true;
    }

    public function generateTxt($orderData, $filePath)
    {
    	if(!file_exists($filePath)){
			$file = fopen($filePath, 'w') or die("Can't create file");
		} else {
			$file = fopen($filePath,"w");
		}
		foreach ($orderData as $order) {
			foreach ($order as $key => $value) {
				$keyLabel = explode('_',$key);
	            $keyLabel = implode(' ', $keyLabel);
	            $keyLabel = ucwords($keyLabel);
				fwrite($file, $keyLabel." : ".$value."\n");
			}
			fwrite($file, "------------------------------------------------------------------------------------\n\n\n");
			
		}
		fclose($file);
		return true;
    }

    public function generateXML($orderData, $filePath)
    {
    	fopen($filePath, "w+") or die("Unable to open file!");
    	$orderCount = count($orderData);
    	$dataToWrite = array('total_orders' => $orderCount, 'orders' => $orderData);
    	$xml_order_info = new \SimpleXMLElement("<?xml version=\"1.0\"?><order_info></order_info>");
    	$this->array_to_xml($dataToWrite,$xml_order_info);
    	//saving generated xml file
		$xml_file = $xml_order_info->asXML($filePath);
		if($xml_file){
		    return true;
		}else{
		    return false;
		}
    }

    public function array_to_xml($array, &$xml_user_info) {
	    foreach($array as $key => $value) {
	        if(is_array($value)) {
	            if(!is_numeric($key)){
	                $subnode = $xml_user_info->addChild("$key");
	                $this->array_to_xml($value, $subnode);
	            }else{
	            	$orderKey = $key+1;
	                $subnode = $xml_user_info->addChild("order$orderKey");
	                $this->array_to_xml($value, $subnode);
	            }
	        }else {
	            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
	        }
	    }
	}

	public function ftpUpload(
        $useSftp,
        $ftpPassive,
        $ftpHost,
        $ftpLogin,
        $ftpPassword,
        $ftpDir,
        $path,
        $file,
        $ftpPort = null
    ) {

        if ($useSftp) {
            $ftp = $this->_ioSftp;
        } else {
            $ftp = $this->_ioFtp;
        }

        $rtn = false;
        try {
            $host = str_replace(["ftp://", "ftps://"], "", $ftpHost);
            if ($useSftp && $ftpPort != null) {
                $host .= ":" . $ftpPort;
            }
            $ftp->open(
                [
                        'host' => $host,
                        'port' => $ftpPort, // only ftp
                        'user' => $ftpLogin,
                        'username' => $ftpLogin, // only sftp
                        'password' => $ftpPassword,
                        'timeout' => '120',
                        'path' => $ftpDir,
                        'passive' => $ftpPassive // only ftp
                    ]
            );

            if ($useSftp) {
                $ftp->cd($ftpDir);
            }

            if (!$useSftp && $ftp->write($file, $this->getAbsoluteRootDir()."/" . $path."/" . $file)) {
                $this->_messageManager->addSuccess(sprintf(__("File '%s' successfully uploaded on %s"), $file, $ftpHost) . ".");
                $rtn = true;
            } elseif ($useSftp && $ftp->write($file, $this->getAbsoluteRootDir()."/" . $path."/" . $file)) {
                $this->_messageManager->addSuccess(sprintf(__("File '%s' successfully uploaded on %s"), $file, $ftpHost) . ".");
                $rtn = true;
            } else {
                $this->_messageManager->addError(sprintf(__("Unable to upload '%s'on %s"), $file, $ftpHost) . ".");
                $rtn = false;
            }
        } catch (\Exception $e) {
            $this->_messageManager->addError(__("Ftp upload error : ") . $e->getMessage());
        }
        $ftp->close();
        return $rtn;
    }

    public function sendEmail($toMail, $subject, $files) {

    	// get Email Sender
		$senderSettingId = $this->_scopeConfigObject->getValue(
							'orderexport/mailsettings/email_sender',
							\Magento\Store\Model\ScopeInterface::SCOPE_STORE
						);
					
		$sender = [
            'name' => $this->_scopeConfigObject->getValue(
						'trans_email/ident_'.$senderSettingId.'/name',
						\Magento\Store\Model\ScopeInterface::SCOPE_STORE
					),
            'email' => $this->_scopeConfigObject->getValue(
						'trans_email/ident_'.$senderSettingId.'/email',
						\Magento\Store\Model\ScopeInterface::SCOPE_STORE
					),
		];

    	// Email Template
        $templateId = 'excellence_export_email_template';

        $toName = 'Admin';

        if ($templateId) {
			        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $transport = $_objectManager->get(\Magento\Framework\Mail\Template\TransportBuilder::class)
                    ->setTemplateIdentifier($templateId)
                    ->setTemplateOptions(
                        [
                            'area' => Area::AREA_FRONTEND, 
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                        ]
                    )
                    ->setTemplateVars(
                    	[
		                    'subject' => $subject,
		                    'time' => $this->_timezoneInterface->gmtDate()
		                ]
                    )
                    ->setFrom($senderSettingId)
                    ->addTo($toMail, $toName)
                    ->getTransport();



				$file = '';
                foreach ($files as $file) {
					$file = $file;
                }
	
				$string = file_get_contents($file['filepath']);
				$fileType = ($file['type'] == null) ? \Zend_Mime::TYPE_OCTETSTREAM : "text/" . $file['type'];
				$fileName = basename($file['filename']);





					$transport = $this->addAttachment($transport, $string, $fileName, $fileType);

            $transport->sendMessage();
        }

        return $this;
    }

	public function addAttachment($transport, $pdfString, $pdfFileName, $fileType)
    {
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $attachment = $_objectManager->get(\Zend\Mime\PartFactory::class)->create();
        $attachment->setContent($pdfString)
->setType('text/plain')
->setFileName($pdfFileName)
->setDisposition(\Zend_Mime::DISPOSITION_ATTACHMENT)
->setEncoding(\Zend_Mime::ENCODING_BASE64);

        $bodyHtml = $_objectManager->get(\Zend\Mime\PartFactory::class)->create();
        $bodyHtml->setContent($transport->getMessage()->getBody()->generateMessage())
->setType('text/csv');
        $bodyPart = $_objectManager->get(\Zend\Mime\Message::class);
        $bodyPart->addPart($bodyHtml);
        $bodyPart->addPart($attachment);
        $transport->getMessage()->setBody($bodyPart);
        return $transport;
    }
}