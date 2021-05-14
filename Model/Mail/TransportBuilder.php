<?php

namespace Excellence\Orderexport\Model\Mail;
 
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @param Api\AttachmentInterface $attachment
     */
    public function attachFile($files) {

        foreach ($files as $file) {
            if (!empty($file['filepath']) && file_exists($file['filepath'])) {
                // $this->message
                // ->createAttachment(
                //     file_get_contents($file['filepath']),
                //     ($file['type'] == null) ? \Zend_Mime::TYPE_OCTETSTREAM : "text/" . $file['type'],
                //     \Zend_Mime::DISPOSITION_ATTACHMENT,
                //     \Zend_Mime::ENCODING_BASE64,
                //     basename($file['filename'])
                //     );
            }
        }

            
        return $this;
    }
}