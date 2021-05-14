<?php
namespace Excellence\Orderexport\Block\Adminhtml\Orderexport;

/**
 * Adminhtml Orderexport grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Excellence\Orderexport\Model\ResourceModel\Orderexport\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Excellence\Orderexport\Model\Orderexport
     */
    protected $_orderexport;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Excellence\Orderexport\Model\Orderexport $orderexportPage
     * @param \Excellence\Orderexport\Model\ResourceModel\Orderexport\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Excellence\Orderexport\Model\Orderexport $orderexport,
        \Excellence\Orderexport\Model\ResourceModel\Orderexport\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_orderexport = $orderexport;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderexportGrid');
        $this->setDefaultSort('profile_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Excellence\Orderexport\Model\ResourceModel\Orderexport\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('profile_id', [
            'header'    => __('ID'),
            'index'     => 'profile_id',
        ]);
        
        $this->addColumn(
            'file_name_prefix',
            [
                'header' => __('File Name'),
                'sortable' => true,
                'index' => 'file_name_prefix'
            ]
        );
        $this->addColumn(
            'file_type',
            [
                'header' => __('File type'),
                'align' => 'left',
                'index' => 'file_type',
                'type' => 'options',
                'options' => [
                    1 => 'csv',
                    2 => 'txt',
                    3 => 'xml'
                ]
            ]
        );

        $this->addColumn(
            'last_generated_file',
            [
            'header' => __('Last generated file'),
            'align' => 'left',
            'index' => 'last_generated_file',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Excellence\Orderexport\Block\Adminhtml\Profiles\Renderer\Link'
            ]
        );

        $this->addColumn(
            'last_exported_order_id',
            [
                'header' => __('Last exported order #'),
                'align' => 'left',
                'index' => 'last_exported_order_id',
            ]
        );

        $this->addColumn(
            'starting_order_id',
            [
                'header' => __('Starting with order #'),
                'align' => 'left',
                'index' => 'starting_order_id',
            ]
        );
        $this->addColumn(
            'last_update',
            [
                'header' => __('Last update'),
                'align' => 'left',
                'index' => 'last_update',
                'type' => 'datetime',
                'width' => '150px'
            ]
        );
        
        $this->addColumn(
            'action',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'profile_id'
                    ],
                    [
                        'url' => ["base" => '*/*/delete'],
                        'confirm' => __('Are you sure you want to delete this profile ?'),
                        'caption' => __('Delete'),
                        "field" => "profile_id"
                    ],
                    [
                        'url' => ["base" => '*/*/generate'],
                        'confirm' => __('Generating a export file can take a while. Are you sure you want to generate it now ?'),
                        'caption' => __('Generate'),
                        "field" => "profile_id"
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['profile_id' => $row->getId()]);
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
