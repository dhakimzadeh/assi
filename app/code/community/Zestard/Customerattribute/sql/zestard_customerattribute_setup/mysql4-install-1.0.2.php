<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $this->getTable('eav_attribute'),
    'send_order_email',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'default'	=>	0,
        'comment'   => 'Send Order Email'
    )
);

$installer->getConnection()->addColumn(
    $this->getTable('eav_attribute'),
    'send_register_email',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'default'	=>	0,
        'comment'   => 'Send Register Email'
    )
);

$installer->endSetup();