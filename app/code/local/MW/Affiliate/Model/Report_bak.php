<?php

/**
 * @author Tuanlv
 * @copyright 2014
 */

class MW_Affiliate_Model_Report extends Mage_Core_Model_Abstract
{
    protected $all_months = 0;
    
    public function prepareCollectionFrontend($data){
        $resource = Mage::getModel('core/resource');
        $aff_customer_table = $resource->getTableName('affiliate/affiliatecustomers');
        $customer_table = Mage::getSingleton('core/resource')->getTableName('customer_entity');
        
        if($data['report_range'] == MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM)
        {
            if($this->_validationDate($data) == false)
            {
                return;
            }
            /** Get all month between two dates */
            $this->all_months = $this->_get_months( $data['from'], $data['to']);
        }
        /** Query to get my Sales */ 
        /** Query to get my Discount */ 
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->addFieldToFilter('main_table.customer_id', $data['customer_id']);
        $collection->getSelect()->group(array('main_table.order_id'));
        $collection->addExpressionFieldToSelect('total_sales_sum', 'SUM(total_amount)/count(distinct product_id)', 'total_sales_sum');
        $collection->addExpressionFieldToSelect('total_discount_sum', 'SUM(history_discount)/count(distinct product_id)', 'total_discount_sum');
        $this->_buildCollection($collection, $data, true, 'transaction_time');
        $collection_sales_sum = $collection;
        
        /** Query to get my Commission */ 
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->addFieldToFilter('main_table.customer_invited', $data['customer_id']);
        $collection->addExpressionFieldToSelect('total_commission_sum', 'SUM(history_commission)', 'total_commission_sum');
        $this->_buildCollection($collection, $data, true, 'transaction_time');
        $collection_commission_sum = $collection;
        
        /** Query to get My Total Sales*/
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->addFieldToFilter('main_table.customer_id', $data['customer_id']);
        $collection->getSelect()->group(array('main_table.order_id'));
        $collection->removeAllFieldsFromSelect();
        $collection->addExpressionFieldToSelect('total_affiliate_sales', 'if(sum(total_amount),sum(total_amount)/count(distinct product_id),0)','total_affiliate_sales');
        $this->_buildCollection($collection, $data, false, 'transaction_time');
        $collection_my_sales = $collection;
        
        /** Query to get My Total Commission*/
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->addFieldToFilter('main_table.customer_invited', $data['customer_id']);
        $collection->addExpressionFieldToSelect('total_affiliate_commission', 'if(sum(history_commission),sum(history_commission),0)','total_affiliate_commission');
        $this->_buildCollection($collection, $data, false, 'transaction_time');
        $collection_my_commission = $collection->getFirstItem();
        
        /** Query to get My Total Child*/
        $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', 1);
        $collection->addFieldToFilter('main_table.customer_invited', $data['customer_id']);
        $collection->addExpressionFieldToSelect('total_affiliate_child', 'count( distinct customer_id)','total_affiliate_child');
        $this->_buildCollection($collection, $data, false, 'customer_time');
        $collection_my_child = $collection->getFirstItem();
                
        switch($data['report_range'])
        {
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_24H:
                    $_time = $this->getPreviousDateTime(24);
                    $start_24h_time = Mage::helper('core')->formatDate(date('Y-m-d h:i:s', $_time), 'medium', true);
                    $start_24h_time = strtotime($start_24h_time);
                    $start_time = array(
                        'h'   => (int)date('H', $start_24h_time),
                        'd'   => (int)date('d', $start_24h_time),
                        'm'   => (int)date('m', $start_24h_time),
                        'y'   => (int)date('Y', $start_24h_time),
                    );
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_24H, $start_time['h'], $start_time['h'] + 24, $start_time);

                    $_data = $this->_buildResult($collection_sales_sum,$collection_commission_sum, 'hour', $rangeDate);
                    $_data['report']['date_start'] = $start_time;
            break;
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_WEEK:
                    $start_time = strtotime("-6 day", strtotime("Sunday Last Week"));
                    $startDay = date('d', $start_time);
                    $endDay = date('d',strtotime("Sunday Last Week"));
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_WEEK, $startDay, $rangeDate);
                    $_data = $this->_buildResult($collection_sales_sum,$collection_commission_sum, 'day', $rangeDate);
                    $_data['report']['date_start'] = array(
                        'd'   => (int)date('d', $start_time),
                        'm'   => (int)date('m', $start_time),
                        'y'   => (int)date('Y', $start_time),
                    );

            break;
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_MONTH:
                    $last_month_time = strtotime($this->_getLastMonthTime());
                    $last_month = date('m', $last_month_time);
                    $start_day = 1;
                    $end_day = $this->_days_in_month($last_month);
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_MONTH, $start_day, $end_day);

                    $_data = $this->_buildResult($collection_sales_sum,$collection_commission_sum, 'day', $rangeDate);
                    $_data['report']['date_start'] = array(
                        'd'   => $start_day,
                        'm'   => (int)$last_month,
                        'y'   => (int)date('Y', $last_month_time),
                        'total_day' => $end_day
                    );

            break;
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS:
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS:
                    if($data['report_range'] == MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS)
                    {
                        $last_x_day = 7;
                    }
                    else if($data['report_range'] == MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS)
                    {
                        $last_x_day = 30;
                    }

                    $start_day = date('Y-m-d h:i:s', strtotime('-'.$last_x_day.' day', Mage::getModel('core/date')->gmtTimestamp()));
                    $end_day = date('Y-m-d h:i:s', strtotime("-1 day"));

                    $original_time = array(
                        'from'  => $start_day,
                        'to'    => $end_day
                    );
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM, 0, 0, $original_time);

                    $_data = $this->_buildResult($collection_sales_sum,$collection_commission_sum, 'multiday', $rangeDate, $original_time);
            break;
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM:
                    $original_time = array(
                        'from'  => $data['from'],
                        'to'    => $data['to']
                    );
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM, 0, 0, $original_time);
                    $_data = $this->_buildResult($collection_sales_sum,$collection_commission_sum, 'multiday', $rangeDate, $original_time);
            break;
        }
        $total_affiliate_sales = null;
        $total_affiliate_sales = null;
        foreach($collection_my_sales as $order){
            $total_affiliate_sales += $order->getData('total_affiliate_sales');
            $total_affiliate_order += 1;
        }
        $_data['statistics']['total_affiliate_sales'] = ($total_affiliate_sales == null) ? Mage::helper('core')->currency(0) :  Mage::helper('core')->currency($total_affiliate_sales);   
        $_data['statistics']['total_affiliate_commission'] = ($collection_my_commission->getData('total_affiliate_commission') == null) ? Mage::helper('core')->currency(0) :  Mage::helper('core')->currency($collection_my_commission->getData('total_affiliate_commission'));
        $_data['statistics']['total_affiliate_order'] =  ($total_affiliate_order == null) ? 0 :  number_format($total_affiliate_order, 0, '.', ',');   
        $_data['statistics']['total_affiliate_child'] =  ($collection_my_child->getData('total_affiliate_child') == null) ? 0 :  number_format($collection_my_child->getData('total_affiliate_child'), 0, '.', ',');
                
        $piechart = $this->preapareCollectionPieChartFrontend($data);       
        $_data['report_commission_by_members'] = json_encode($piechart['commission_by_members']);
        $_data['report_commission_by_programs'] = json_encode($piechart['commission_by_programs']);
        $_data['report']['title'] = Mage::helper('affiliate')->__('Affiliate Sales / My Commission ');
        $_data['report']['curency'] = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        
        return json_encode($_data);
    }
    
    public function prepareCollection($data){
        $resource = Mage::getModel('core/resource');
        $aff_customer_table = $resource->getTableName('affiliate/affiliatecustomers');
        $customer_table = Mage::getSingleton('core/resource')->getTableName('customer_entity');
        $order_table =  Mage::getSingleton('core/resource')->getTableName('sales/order');
        
        if($data['report_range'] == MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM)
        {
            if($this->_validationDate($data) == false)
            {
                return;
            }
            /** Get all month between two dates */
            $this->all_months = $this->_get_months( $data['from'], $data['to']);
        }
        $users = array();
        $collection = Mage::getModel('customer/customer')->getCollection();
        foreach($collection->getData() as $user)
        {
            $users[] = $user['entity_id'];
        }
                
        /** Query to get Sales */ 
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->getSelect()->group(array('main_table.order_id'));
        $collection->getSelect()->join(array('sales_order' => $order_table), 'main_table.order_id = sales_order.increment_id', array());
        $collection->addExpressionFieldToSelect('total_sales_sum', 'sales_order.grand_total', 'total_sales_sum');
        $this->_buildCollection($collection, $data, true, 'transaction_time');
        $collection_sales_sum = $collection;
        
        /** Query to get Discount */
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->getSelect()->group(array('main_table.order_id'));
        $collection->getSelect()->group(array('main_table.product_id'));
        $collection->addExpressionFieldToSelect('total_discount_sum', 'history_discount', 'total_discount_sum');
        $this->_buildCollection($collection, $data, true, 'transaction_time');
        $collection_discount_sum = $collection;
        
        /** Query to get Commission */
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->addExpressionFieldToSelect('total_commission_sum', 'SUM(history_commission)', 'total_commission_sum');
        $this->_buildCollection($collection, $data, true, 'transaction_time');
        $collection_affiliate = $collection;
        
        /** Query to get Affiliate*/
        $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', 1);
        $this->_buildCollection($collection, $data, false, 'customer_time');
        $num_affiliate = (count($collection->getData()) == null) ? 0 : count($collection->getData());

        /** Query to statistic withdrawal*/
        $collection = Mage::getModel('affiliate/affiliatewithdrawn')->getCollection();
        $collection->removeAllFieldsFromSelect(); 
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->addExpressionFieldToSelect('total_withdrawn','if(sum(withdrawn_amount),sum(withdrawn_amount),0)','total_withdrawn');
        $collection->addExpressionFieldToSelect('total_fee','if(sum(fee),sum(fee),0)','total_fee');        
        
        $this->_buildCollection($collection, $data, false, 'withdrawn_time');
        $collection_stats_withdrawn = $collection->getFirstItem();
         
        /** Query to statistic total transactions*/   
        $collection = Mage::getModel('affiliate/affiliatetransaction')->getCollection();
        $collection->removeAllFieldsFromSelect(); 
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->addExpressionFieldToSelect('total_transaction','if(count(history_id),count(history_id)/2,0)','total_transaction');        
        
        $this->_buildCollection($collection, $data, false, 'transaction_time');
        $collection_stats_transaction = $collection->getFirstItem();
        
        /** Query to get Orders*/
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->getSelect()->group(array('order_id'));
        $this->_buildCollection($collection, $data, false, 'transaction_time');
        $array_orders = array();
        foreach($collection as $order){
            array_push($array_orders,$order->getData('order_id'));
        }        
        //Mage::log($array_orders,Zend_Log::DEBUG,'mw_debug.log');   
        /** Query to statistic top afiliate sales*/
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collectionn->addFieldToFilter('main_table.increment_id',array('in'=>$array_orders)); 
        $collection->getSelect()->group(array('main_table.customer_id'));
        $collection->addExpressionFieldToSelect('total_affiliate_sales','sum(main_table.grand_total)','total_affiliate_sales');   
        
        /** Query to statistic top afiliate sales*/
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->getSelect()->join(array('customer_entity' => $customer_table), 'main_table.customer_id = customer_entity.entity_id', array());
        $collection->getSelect()->join(array('sales_order' => $order_table), 'main_table.order_id = sales_order.increment_id', array());
        $collection->getSelect()->group(array('main_table.customer_id'));
        $collection->getSelect()->group(array('main_table.order_id'));
        $collection->addExpressionFieldToSelect('affiliate','customer_entity.email','affiliate');
        $collection->addExpressionFieldToSelect('total_affiliate_sales', 'sales_order.grand_total', 'total_affiliate_sales');
        $collection->addExpressionFieldToSelect('affiliate_id','main_table.customer_id','affiliate_id');
        $this->_buildCollection($collection, $data, false, 'transaction_time');
        $collection_top_affiliate = $collection;
        
        /** Query to statistic top afiliate commission*/
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->getSelect()->join(array('customer_entity' => $customer_table), 'main_table.customer_invited = customer_entity.entity_id', array());
        $collection->addExpressionFieldToSelect('affiliate','customer_entity.email','affiliate');
        $collection->addExpressionFieldToSelect('affiliate_id','main_table.customer_invited','affiliate_id');
        $collection->addExpressionFieldToSelect('total_affiliate_commission', 'if(sum(history_commission),sum(history_commission),0)','total_affiliate_commission');
        $collection->getSelect()->group(array('main_table.customer_invited'));
        $collection->getSelect()->order('total_affiliate_commission DESC');
        $this->_buildCollection($collection, $data, false, 'transaction_time');
        $collection_top_affiliate_commission = $collection;
                
        switch($data['report_range'])
        {
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_24H:
                    $_time = $this->getPreviousDateTime(24);
                    $start_24h_time = Mage::helper('core')->formatDate(date('Y-m-d h:i:s', $_time), 'medium', true);
                    $start_24h_time = strtotime($start_24h_time);
                    $start_time = array(
                        'h'   => (int)date('H', $start_24h_time),
                        'd'   => (int)date('d', $start_24h_time),
                        'm'   => (int)date('m', $start_24h_time),
                        'y'   => (int)date('Y', $start_24h_time),
                    );
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_24H, $start_time['h'], $start_time['h'] + 24, $start_time);

                    $_data = $this->_buildResult($collection_sales_sum,$collection_discount_sum,$collection_affiliate, 'hour', $rangeDate);
                    $_data['report']['date_start'] = $start_time;
            break;
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_WEEK:
                    $start_time = strtotime("-6 day", strtotime("Sunday Last Week"));
                    $startDay = date('d', $start_time);
                    $endDay = date('d',strtotime("Sunday Last Week"));
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_WEEK, $startDay, $rangeDate);
                    $_data = $this->_buildResult($collection_sales_sum,$collection_discount_sum,$collection_affiliate, 'day', $rangeDate);
                    $_data['report']['date_start'] = array(
                        'd'   => (int)date('d', $start_time),
                        'm'   => (int)date('m', $start_time),
                        'y'   => (int)date('Y', $start_time),
                    );

            break;
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_MONTH:
                    $last_month_time = strtotime($this->_getLastMonthTime());
                    $last_month = date('m', $last_month_time);
                    $start_day = 1;
                    $end_day = $this->_days_in_month($last_month);
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_MONTH, $start_day, $end_day);

                    $_data = $this->_buildResult($collection_sales_sum,$collection_discount_sum,$collection_affiliate, 'day', $rangeDate);
                    $_data['report']['date_start'] = array(
                        'd'   => $start_day,
                        'm'   => (int)$last_month,
                        'y'   => (int)date('Y', $last_month_time),
                        'total_day' => $end_day
                    );

            break;
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS:
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS:
                    if($data['report_range'] == MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS)
                    {
                        $last_x_day = 7;
                    }
                    else if($data['report_range'] == MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS)
                    {
                        $last_x_day = 30;
                    }

                    $start_day = date('Y-m-d h:i:s', strtotime('-'.$last_x_day.' day', Mage::getModel('core/date')->gmtTimestamp()));
                    $end_day = date('Y-m-d h:i:s', strtotime("-1 day"));

                    $original_time = array(
                        'from'  => $start_day,
                        'to'    => $end_day
                    );
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM, 0, 0, $original_time);

                    $_data = $this->_buildResult($collection_sales_sum,$collection_discount_sum,$collection_affiliate, 'multiday', $rangeDate, $original_time);
            break;
            case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM:
                    $original_time = array(
                        'from'  => $data['from'],
                        'to'    => $data['to']
                    );
                    $rangeDate = $this->_buildArrayDate(MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM, 0, 0, $original_time);
                    $_data = $this->_buildResult($collection_sales_sum,$collection_discount_sum,$collection_affiliate, 'multiday', $rangeDate, $original_time);
            break;
        }
        
        $_data['statistics']['total_affiliate_sales'] =  Mage::helper('core')->currency(0);   
        $_data['statistics']['total_affiliate_commission'] =  Mage::helper('core')->currency(0);
        $_data['statistics']['avg_commission_per_order'] =  Mage::helper('core')->currency(0);   
        $_data['statistics']['avg_commission_per_affiliate'] =  Mage::helper('core')->currency(0);
        $_data['statistics']['avg_sales_per_order'] =  Mage::helper('core')->currency(0);   
        $_data['statistics']['avg_sales_per_affiliate'] =  Mage::helper('core')->currency(0);
        $_data['statistics']['total_affiliate_order'] =  0;
        
        $collection_stats_sales = array();
        $collection_stats_sales['total_affiliate_order'] = (count($collection_sales_sum->getData()) == null) ? 0 : count($collection_sales_sum->getData());
        $collection_stats_sales['total_affiliate_sales'] = 0;
        $collection_stats_sales['total_affiliate_commission'] = 0;
        $collection_stats_sales['avg_commission_per_order'] = 0;
        $collection_stats_sales['avg_commission_per_affiliate'] = 0;
        $collection_stats_sales['avg_sales_per_order'] = 0;
        $collection_stats_sales['avg_sales_per_affiliate'] = 0;

        foreach($collection_sales_sum as $order){
            $collection_stats_sales['total_affiliate_sales'] += floatval($order->getTotalSalesSum());
        }
        foreach($collection_affiliate as $commission){
            $collection_stats_sales['total_affiliate_commission'] += floatval($commission->getTotalCommissionSum());
        } 
        $collection_stats_sales['avg_commission_per_order'] = $collection_stats_sales['total_affiliate_commission']/$collection_stats_sales['total_affiliate_order'];  
        $collection_stats_sales['avg_commission_per_affiliate'] = $collection_stats_sales['total_affiliate_commission']/$num_affiliate;
        $collection_stats_sales['avg_sales_per_order'] = $collection_stats_sales['total_affiliate_sales']/$collection_stats_sales['total_affiliate_order'];  
        $collection_stats_sales['avg_sales_per_affiliate'] = $collection_stats_sales['total_affiliate_sales']/$num_affiliate;
        
        foreach($collection_stats_sales as $key => $stat)
        {            
            switch($key){
                case "total_affiliate_order":
                    $_data['statistics'][$key] = ($stat == null) ? 0 : number_format($stat, 0, '.', ',');
                break;
                case "total_affiliate_sales":
                case "total_affiliate_commission":
                case "avg_commission_per_order":
                case "avg_commission_per_affiliate":
                case "avg_sales_per_order":
                case "avg_sales_per_affiliate":
                    $_data['statistics'][$key] = ($stat == null) ?  Mage::helper('core')->currency(0) :  Mage::helper('core')->currency($stat);//number_format($stat, 0, '.', ',');
                break;
            }
        }
        $_data['statistics']['total_withdrawn'] =  Mage::helper('core')->currency(0);   
        $_data['statistics']['total_fee'] =  Mage::helper('core')->currency(0);       
        foreach($collection_stats_withdrawn->getData() as $key => $stat)
        {
            switch($key){
                case "total_withdrawn":
                case "total_fee":
                    $_data['statistics'][$key] = ($stat == null) ?  Mage::helper('core')->currency(0) : Mage::helper('core')->currency($stat);//number_format($stat, 2, '.', ',');
                break;   
            }                
        } 
        
        $_data['topaffiliate'] = null;
        $count = 0;
        foreach($collection_top_affiliate as $affiliate){
            foreach($affiliate->getData() as $key => $value){
                switch($key){
                    case "affiliate":
                        $_data['topaffiliate'][$affiliate->getData('affiliate_id')][$key] = ($value == null) ? '' : $value;
                    break;
                    case "total_affiliate_sales":
                        $_data['topaffiliate'][$affiliate->getData('affiliate_id')][$key] += $value;
                    break;                                   
                }
                if(!isset($_data['topaffiliate'][$affiliate->getData('affiliate_id')]['total_affiliate_commission'])){
                    $total_commission = 0;
                    foreach($collection_top_affiliate_commission as $aff){
                        if($affiliate->getData('affiliate_id') == $aff->getData('affiliate_id')){
                            $total_commission = $aff->getData('total_affiliate_commission');
                            break;
                        }
                    }
                    $_data['topaffiliate'][$affiliate->getData('affiliate_id')]['total_affiliate_commission'] = Mage::helper('core')->currency($total_commission);
                }
            }
            
            $count = $count +1;
            if(count == 10) break;
        }
        if($_data['topaffiliate'] != null){
            foreach($_data['topaffiliate'] as $key => $aff){
                $_data['topaffiliate'][$key]['total_affiliate_sales'] = Mage::helper('core')->currency($aff['total_affiliate_sales']);
            }        
        } 
        $piechart = $this->preapareCollectionPieChart($data);       
        $_data['statistics']['total_transaction'] =  number_format($collection_stats_transaction->getData('total_transaction'), 0, '.', ',');
        $_data['report_sales_by_programs'] = json_encode($piechart['sales']);
        $_data['report_commission_by_programs'] = json_encode($piechart['commission']);
        $_data['report']['title'] = Mage::helper('affiliate')->__('Affiliate Sales / Commission / Discount');
        $_data['report']['curency'] = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        
        return json_encode($_data);
    }
    protected function _buildResult($collection_sales_sum,$collection_discount_sum,$collection_affiliate,$type, $rangeDate, $original_time = null)
    {
            $_data = array();
            try
            {
                if($type == 'multiday')
                {
                    foreach($rangeDate as $year => $months)
                    {
                        foreach($months as $month => $days)
                        {
                            foreach($days as $day)
                            {
                                $_data['report']['commission'][$year."-".$month."-".$day]  = array($year, $month, $day, 0);
                            }
                            foreach($days as $day)
                            {
                                $_data['report']['sales'][$year."-".$month."-".$day]  = array($year, $month, $day, 0);
                            }
                            foreach($days as $day)
                            {
                                $_data['report']['discount'][$year."-".$month."-".$day]  = array($year, $month, $day, 0);
                            }

                            foreach($collection_affiliate as $commission)
                            {
                                if($commission->getMonth() == $month)
                                {
                                    foreach($days as $day)
                                    {

                                        if($commission->getDay() == $day)
                                        {
                                            $_data['report']['commission'][$year."-".$month."-".$day]  = array($year, $month, $day, floatval($commission->getTotalCommissionSum()));
                                        }
                                    }
                                }
                            }
                            foreach($collection_sales_sum as $sale)
                            {
                                if($sale->getMonth() == $month)
                                {
                                    foreach($days as $day)
                                    {
                                        if($sale->getDay() == $day)
                                        {   
                                            $_data['report']['sales'][$year."-".$month."-".$day][3] += floatval($sale->getTotalSalesSum());
                                        }
                                    }
                                }
                            }
                            foreach($collection_discount_sum as $discount)
                            {
                                if($discount->getMonth() == $month)
                                {
                                    foreach($days as $day)
                                    {
                                        if($discount->getDay() == $day)
                                        {
                                            $_data['report']['discount'][$year."-".$month."-".$day][3]  += floatval($discount->getTotalDiscountSum());
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    switch($type )
                    {
                        case 'hour':
                            $rangeTempDate = reset($rangeDate);
                            $i = $rangeTempDate['incr_hour'];
                            break;
                        case 'day':
                            $rangeTempDate = reset($rangeDate);
                            $i = $rangeTempDate['count_day'];
                            break;
                    }

                    foreach($rangeDate as $date)
                    {
                        switch($type )
                        {
                            case 'hour':
                                $count = $date['native_hour'];
                                break;
                            case 'day':
                                $count = $date['native_day'];
                                break;
                        }

                        $_data['report']['commission'][$i] = 0;
                        $_data['report']['sales'][$i] = 0;
                        $_data['report']['discount'][$i] = 0;
                        foreach($collection_affiliate as $commission)
                        {
                            if((int)$commission->{"get$type"}() == $count)
                            {
                                if(isset($date['day']) && $date['day'] == (int)$commission->getDay())
                                {
                                    $_data['report']['commission'][$i] = floatval($commission->getTotalCommissionSum());
                                }
                                else if(!isset($date['day']))
                                {
                                    $_data['report']['commission'][$i] = floatval($commission->getTotalCommissionSum());
                                }
                            }
                        }
                        foreach($collection_sales_sum as $sale)
                        {
                            if((int)$sale->{"get$type"}() == $count)
                            {
                                if(isset($date['day']) && $date['day'] == (int)$sale->getDay())
                                {
                                    $_data['report']['sales'][$i] += floatval($sale->getTotalSalesSum());
                                }
                                else if(!isset($date['day']))
                                {
                                    $_data['report']['sales'][$i] += floatval($sale->getTotalSalesSum());
                                }
                            }
                        }
                        
                        foreach($collection_discount_sum as $discount)
                        {
                            if((int)$discount->{"get$type"}() == $count)
                            {
                                if(isset($date['day']) && $date['day'] == (int)$discount->getDay())
                                {
                                    $_data['report']['discount'][$i] += floatval($discount->getTotalDiscountSum());
                                }
                                else if(!isset($date['day']))
                                {
                                    $_data['report']['discount'][$i] += floatval($discount->getTotalDiscountSum());
                                }
                            }
                        }

                        $i++;
                    }
                }

                $_data['report']['commission'] = array_values($_data['report']['commission']);
                $_data['report']['sales'] = array_values($_data['report']['sales']);
                $_data['report']['discount'] = array_values($_data['report']['discount']);
            }
            catch(Exception $e){}

            return $_data;
    }
    protected function _buildCollection(&$collection, $data, $group = true, $str_time)
    {
            switch($data['report_range'])
            {
                case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_24H:
                    /* Last 24h */
                    $_hour = date('Y-m-d h:i:s', strtotime('-1 day', Mage::getModel('core/date')->gmtTimestamp()));
                    $start_hour = Mage::helper('core')->formatDate($_hour, 'medium', true);
                    $_hour = date('Y-m-d h:i:s', strtotime("now"));
                    $end_hour = Mage::helper('core')->formatDate($_hour, 'medium', true);

                    if($group == true)
                    {
                        $collection->addExpressionFieldToSelect('hour', 'HOUR(CONVERT_TZ('.$str_time.', \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\'))', 'hour');
                        $collection->addExpressionFieldToSelect('day', 'DAY(CONVERT_TZ('.$str_time.', \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\'))', 'day');
                        $collection->getSelect()->group(array('hour'));
                    }

                    $collection->addFieldToFilter('CONVERT_TZ(main_table.'.$str_time.', \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_hour, 'to' => $end_hour, 'datetime' => true));
                    break;
                case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_WEEK:
                    /* Last week */
                    $start_day = date('Y-m-d',strtotime("-7 day", strtotime("Sunday Last Week")));
                    $end_day = date('Y-m-d',strtotime("Sunday Last Week"));
                    if($group == true)
                    {
                        $collection->addExpressionFieldToSelect('day', 'DAY('.$str_time.')', 'day');
                        $collection->getSelect()->group(array('day'));
                    }

                    $collection->addFieldToFilter('CONVERT_TZ(main_table.'.$str_time.', \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_day, 'to' => $end_day, 'datetime' => true));
                    break;
                case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_MONTH:
                    /* Last month */
                    $last_month_time = $this->_getLastMonthTime();
                    $last_month = date('m', strtotime($last_month_time));
                    $start_day = date('Y', strtotime($last_month_time))."-".$last_month."-1";
                    $end_day = date('Y', strtotime($last_month_time))."-".$last_month."-".$this->_days_in_month($last_month);

                    /** Fix bug next one day */
                    $end_day = strtotime($end_day.' +1 day');
                    $end_day = date('Y', $end_day)."-".date('m', $end_day)."-".date('d', $end_day);

                    if($group == true)
                    {
                        $collection->addExpressionFieldToSelect('day', 'DAY('.$str_time.')', 'day');
                        $collection->getSelect()->group(array('day'));
                    }

                    $collection->addFieldToFilter('CONVERT_TZ(main_table.'.$str_time.', \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_day, 'to' => $end_day, 'datetime' => true));
                    break;
                case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS:
                case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS:
                    /** Last X days */
                    if($data['report_range'] == MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS)
                    {
                        $last_x_day = 7;
                    }
                    else if($data['report_range'] == MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS)
                    {
                        $last_x_day = 30;
                    }
                    $start_day = date('Y-m-d h:i:s', strtotime('-'.$last_x_day.' day', Mage::getModel('core/date')->gmtTimestamp()));
                    $end_day = date('Y-m-d h:i:s', strtotime("-1 day"));
                    if($group == true)
                    {
                        $collection->getSelect()->group(array('day'));
                    }

                    $collection->addExpressionFieldToSelect('month', 'MONTH('.$str_time.')', 'month');
                    $collection->addExpressionFieldToSelect('day', 'DAY('.$str_time.')', 'day');
                    $collection->addExpressionFieldToSelect('year', 'YEAR('.$str_time.')', 'year');


                    $collection->addFieldToFilter('CONVERT_TZ(main_table.'.$str_time.', \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_day, 'to' => $end_day, 'datetime' => true));
                    break;
                case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM:
                    /* Custom range */

                    if($group == true)
                    {
                        $collection->addExpressionFieldToSelect('month', 'MONTH('.$str_time.')', 'month');
                        $collection->addExpressionFieldToSelect('day', 'DAY('.$str_time.')', 'day');
                        $collection->addExpressionFieldToSelect('year', 'YEAR('.$str_time.')', 'year');
                        $collection->getSelect()->group(array('day'));
                    }

                    $collection->addFieldToFilter('CONVERT_TZ(main_table.'.$str_time.', \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $data['from'], 'to' => $data['to'], 'datetime' => true));
                    break;
            }
    }
    
    protected function _buildArrayDate($type, $from = 0, $to = 23, $original_time = null)
    {
            switch($type)
            {
                case MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_24H:
                    $start_day = $original_time['d'];
                    for($i = $from; $i <= $to; $i++)
                    {
                        $data[$i]['incr_hour'] = $i;
                        $data[$i]['native_hour'] = ($i > 24) ? $i - 24 : $i;
                        $data[$i]['day'] = $start_day;

                        if($i == 23)
                        {
                            $start_day++;
                        }
                    }
                    break;
                case  MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_WEEK:
                    $data = array();
                    $day_in_month = $this->_days_in_month(date('m'), date('Y'));
                    $clone_from = $from;
                    $reset = false;
                    for($i = 1; $i <=7; $i++)
                    {
                        if($from > $day_in_month && !$reset){
                            $clone_from = 1;
                            $reset = true;
                        }
                        $data[$i]['count_day'] = $from;
                        $data[$i]['native_day'] = $clone_from;
                        $from++;
                        $clone_from++;
                    }

                    break;
                case  MW_Affiliate_Model_Admin_Const::REPORT_RAGE_LAST_MONTH:
                    for($i = (int)$from; $i <= $to; $i++)
                    {
                        $data[$i]['native_day'] = (int)$i;
                    }
                    break;
                case  MW_Affiliate_Model_Admin_Const::REPORT_RAGE_CUSTOM:
                    $total_days = $this->_dateDiff($original_time['from'], $original_time['to']);
                    if($total_days > 365)
                    {

                    }
                    else
                    {
                        $all_months = $this->_get_months($original_time['from'], $original_time['to']);
                        $start_time = strtotime($original_time['from']);
                        $start_day  = (int)date('d', $start_time);
                        $year       = (int)date('Y', $start_time);
                        $count      = 0;
                        $data       = array();

                        $end_day_time = strtotime($original_time['to']);

                        $end_day = array(
                            'm' => (int)date('m', $end_day_time),
                            'd' => (int)date('d', $end_day_time)
                        );

                        foreach($all_months as $month)
                        {
                            $day_in_month = $this->_days_in_month($month, (int)date('Y', $start_time));

                            for($day = ($count == 0 ? $start_day : 1); $day <= $day_in_month; $day++)
                            {
                                if($day > $end_day['d'] && $month == $end_day['m']){
                                    continue;
                                }
                                $data[$year][$month][$day] = $day;
                            }
                            $count++;
                        }
                    }
                    break;
            }
            return $data;
    }
    
    protected function _get_months($start, $end){
        $start = $start=='' ? time() : strtotime($start);
        $end = $end=='' ? time() : strtotime($end);
        $months = array();

        for ($i = $start; $i <= $end; $i = $this->get_next_month($i)) {
            $months[] = (int)date('m', $i);
        }

        return $months;
    }
    
    protected function get_next_month($tstamp) {
        return (strtotime('+1 months', strtotime(date('Y-m-01', $tstamp))));
    }
    
    protected function _calOffsetHourGMT()
    {
        return Mage::getSingleton('core/date')->calculateOffset(Mage::app()->getStore()->getConfig('general/locale/timezone'))/60/60;
    }
    
    protected function _getLastMonthTime()
    {
        return  date('Y-m-d', strtotime("-1 month"));
    }
    
    protected function _days_in_month($month, $year)
    {
        $year = (!$year) ? date('Y', Mage::getSingleton('core/date')->gmtTimestamp()) : $year;
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }
    
    protected function _validationDate($data)
    {
        if(strtotime($data['from']) > strtotime($data['to']))
            return false;
        return true;
    }
    
    protected function getPreviousDateTime($hour)
    {
        return Mage::getModel('core/date')->gmtTimestamp() - (3600 * $hour);
    }
    
    protected function _dateDiff($d1, $d2)
    {
        // Return the number of days between the two dates:
        return round(abs(strtotime($d1) - strtotime($d2))/86400);
    }
    
    protected function preapareCollectionPieChart($data){ 
        $resource = Mage::getModel('core/resource');
        $program_table = $resource->getTableName('affiliate/affiliateprogram');
         /** Query to piechart */
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->getSelect()->join(array('programs_entity' => $program_table), 'main_table.program_id = programs_entity.program_id', array());
        $collection->addExpressionFieldToSelect('program','programs_entity.program_name','program');
        $collection->addExpressionFieldToSelect('total_affiliate_sales', 'if(sum(main_table.total_amount),sum(main_table.total_amount),0)','total_affiliate_sales');
        $collection->addExpressionFieldToSelect('total_affiliate_commission', 'if(sum(main_table.history_commission),sum(main_table.history_commission),0)', 'total_affiliate_commission');
        $collection->getSelect()->group(array('main_table.program_id'));
        $this->_buildCollection($collection, $data, false, 'transaction_time');
        $collection_piechart = $collection;
        $totalsales = 0;
        $totalcommission = 0;
        $dta_sales = array();
        $dta_commission = array();
        foreach($collection_piechart as $program){
            $totalsales += $program->getData('total_affiliate_sales');
            $totalcommission += $program->getData('total_affiliate_commission');
        }
        foreach($collection_piechart as $key => $program){
            $dta_sales[$program->getData('program')] = $program->getData('total_affiliate_sales')/$totalsales * 100;
            $dta_commission[$program->getData('program')] = $program->getData('total_affiliate_commission')/$totalcommission * 100;
        }
        $sales = array();
        $commission = array();
        foreach($dta_sales as $key => $percent ){
            if($percent > 0.1){
                $sales[]= array(Mage::helper('affiliate')->__(ucfirst($key)), $percent);
            }
        }
        foreach($dta_commission as $key => $percent ){
            if($percent > 0.1){
                $commission[]= array(Mage::helper('affiliate')->__(ucfirst($key)), $percent);
            }
        }
        
        $_data['sales'] = $sales;
        $_data['commission'] = $commission;
        return $_data;
    }
    
    protected function preapareCollectionPieChartFrontend($data){
        $resource = Mage::getModel('core/resource');
        $program_table = $resource->getTableName('affiliate/affiliateprogram');
         /** Query to piechart */        
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->addFieldToFilter('main_table.customer_invited', $data['customer_id']);
        $collection->getSelect()->join(array('programs_entity' => $program_table), 'main_table.program_id = programs_entity.program_id', array());
        $collection->addExpressionFieldToSelect('program','programs_entity.program_name','program');
        $collection->addExpressionFieldToSelect('total_affiliate_commission', 'if(sum(history_commission),sum(history_commission),0)','total_affiliate_commission');
        $collection->getSelect()->group(array('main_table.program_id'));
        $this->_buildCollection($collection, $data, false, 'transaction_time');
        
        $collection_piechart_by_programs = $collection;
        
        /** Query Commission by Self */
        $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToFilter('main_table.status', MW_Affiliate_Model_Status::COMPLETE);
        $collection->addFieldToFilter('main_table.customer_invited', $data['customer_id']);
        $collection->addFieldToFilter('main_table.invitation_type', MW_Affiliate_Model_Typeinvitation::NON_REFERRAL);
        $collection->addExpressionFieldToSelect('self_commission', 'if(sum(history_commission),sum(history_commission),0)','self_commission');
        $this->_buildCollection($collection, $data, false, 'transaction_time');
        $collection_self_commission = $collection->getFirstItem();
        $totalcommission = 0;

        $dta_commission_by_programs = array();
        $dta_commission_by_members = array();       
        
        foreach($collection_piechart_by_programs as $program){
            $totalcommission += $program->getData('total_affiliate_commission');
        }
        foreach($collection_piechart_by_programs as $key => $program){
            $dta_commission_by_programs[$program->getData('program')] = $program->getData('total_affiliate_commission')/$totalcommission * 100;
        }
        if($totalcommission > 0){
            $dta_commission_by_members['From Me'] = $collection_self_commission->getData('self_commission')/$totalcommission * 100;
            $dta_commission_by_members['From Childs'] = 100 - $dta_commission_by_members['From Me'];
        }
        $commission_by_programs = array();
        $commission_by_members = array();
        
        foreach($dta_commission_by_members as $key => $percent ){
            if($percent > 0.1){
                $commission_by_members[]= array(Mage::helper('affiliate')->__(ucfirst($key)), $percent);
            }
        }
        foreach($dta_commission_by_programs as $key => $percent ){
            if($percent > 0.1){
                $commission_by_programs[]= array(Mage::helper('affiliate')->__(ucfirst($key)), $percent);
            }
        }
        $_data['commission_by_members'] = $commission_by_members;
        $_data['commission_by_programs'] = $commission_by_programs;
        return $_data;
    }
}
