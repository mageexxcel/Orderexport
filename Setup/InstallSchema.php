<?php


namespace Excellence\Orderexport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();

		/**
		 * Creating table excellence_orderexport_profiles
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('excellence_orderexport_profiles')
		)->addColumn(
			'profile_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'file_name_prefix',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'file_name_prefix'
		)->addColumn(
			'file_type',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'File Type'
		)->addColumn(
			'file_name_format',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false],
			'File Name Format'
		)->addColumn(
			'last_generated_file',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Last Generated File'
		)->addColumn(
			'last_exported_order_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Last Exported Order Id'
		)->addColumn(
			'starting_order_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Starting Order Id'
		)->addColumn(
			'store_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Store Id'
		)->addColumn(
			'order_status',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Order Status'
		)->addColumn(
			'filter_by_customer_group',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'filter_by_customer_group'
		)->addColumn(
			'customer_group',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'customer_group'
		)->addColumn(
			'order_fields',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			10240,
			['nullable' => true,'default' => null],
			'order_fields'
		)->addColumn(
			'file_directory',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'File Directory'
		)->addColumn(
			'use_google_drive',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'use_google_drive'
		)->addColumn(
			'use_separate_directory',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'use_separate_directory'
		)->addColumn(
			'drive_folder_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'drive_folder_id'
		)->addColumn(
			'use_ftp',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'use_ftp'
		)->addColumn(
			'ftp_host',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'ftp_host'
		)->addColumn(
			'ftp_port',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'ftp_port'
		)->addColumn(
			'ftp_login',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'ftp_login'
		)->addColumn(
			'ftp_password',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'ftp_password'
		)->addColumn(
			'ftp_directory',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'FTP Directory'
		)->addColumn(
			'use_sftp',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'use_sftp'
		)->addColumn(
			'delete_local_file',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'delete_local_file'
		)->addColumn(
			'email_file',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'email_file'
		)->addColumn(
			'email_recipient',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'email_recipient'
		)->addColumn(
			'email_subject',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'email_subject'
		)->addColumn(
			'auto_cron',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'auto_cron'
		)->addColumn(
			'cron_period',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'cron_period'
		)->addColumn(
			'custom_period',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'custom_period'
		)->addColumn(
			'last_update',
			\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Profile Update Time'
		)->setComment(
			'Orderexport item'
		);
		$installer->getConnection()->createTable($table);
		
		//START table setup
		$table = $installer->getConnection()->newTable(
		            $installer->getTable('excellence_orderexport_googledrive')
		    )->addColumn(
		            'excellence_orderexport_googledrive_id',
		            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		            null,
		            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
		            'Entity ID'
		        )->addColumn(
		            'refresh_key',
		            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		            255,
		            [ 'nullable' => false, ],
		            'refresh_key'
		        )->addColumn(
		            'redirect_uri',
		            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		            255,
		            [ 'nullable' => false, ],
		            'redirect_uri'
		        )->addColumn(
		            'update_time',
		            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
		            null,
		            [ 'nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE, ],
		            'Modification Time'
		        );
		$installer->getConnection()->createTable($table);
		//END   table setup
		$installer->endSetup();
	}
}