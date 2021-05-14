<?php

namespace Excellence\Orderexport\Model\Adminhtml\Config\Source;
 
class OrderFields implements \Magento\Framework\Option\ArrayInterface
{

    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
    }

    public function toOptionArray()
    {
        $optionArray = array();
        $columns = [];
        $describe = $this->connection->describeTable(
            $this->connection->getTableName('sales_order')
        );
        foreach ($describe as $column) {
            $label = explode('_',$column['COLUMN_NAME']);
            $label = implode(' ', $label);
            $label = ucwords($label);
            $optionArray[] = ['value' => $column['COLUMN_NAME'], 'label' => $label];
        }
        return $optionArray; 
    }   
}