<?php
class MW_Affiliate_Adminhtml_Affiliate_AffiliatereportController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('affiliate/report');
    }
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/report')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_title($this->__('Reports'))
             ->_title($this->__('Result'))
             ->_title($this->__('Affiliate'));

        $this->_initAction()
            ->_setActiveMenu('affiliate/report')
            ->_addBreadcrumb(Mage::helper('affiliate')->__('Report Affiliate'), Mage::helper('affiliate')->__('Affiliate Sale Statistic by time range'))
            ->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatereport_result'))
            ->renderLayout();
	}
	public function referralAction() {
		$this->_title($this->__('Reports'))->_title($this->__('Affiliate Invitation Statistic'))->_title($this->__('Affiliate Invitation Statistic'));

        $this->_initAction()
            ->_setActiveMenu('affiliate/report')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Affiliate Invitation Statistic'), Mage::helper('adminhtml')->__('Affiliate Invitation Statistic'))
			->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatereport_referral'))
            ->renderLayout();
	}
	public function referralsiteAction() {
		$this->_title($this->__('Reports'))->_title($this->__('Affiliate Website(s) Statistic'))->_title($this->__('Affiliate Website(s) Statistic'));

        $this->_initAction()
            ->_setActiveMenu('affiliate/report')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Affiliate Website(s) Statistic'), Mage::helper('adminhtml')->__('Affiliate Website(s) Statistic'))
			->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatereport_referralsite'))
            ->renderLayout();
	}
	
	public function exportSalesCsvAction()
    {
        $fileName   = 'affiliate_sales.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatereport_result_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportSalesExcelAction()
    {
        $fileName   = 'affiliate_sales.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatereport_result_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
	public function exportReferralCsvAction()
    {
        $fileName   = 'affiliate_referral.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatereport_referral_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportReferralExcelAction()
    {
        $fileName   = 'affiliate_referral.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatereport_referral_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
	
    public function overviewAction(){
         if($this->getRequest()->getPost('ajax') == 'true'){
            $data = $this->getRequest()->getPost();

            switch($this->getRequest()->getPost('type'))
            {
                case 'dashboard':
                    print Mage::getModel('affiliate/report')->prepareCollection($data);
                break;
            }

            exit;
        }
        $this->_title($this->__('Reports'))
                ->_title($this->__('Result'))
                ->_title($this->__('Affiliate'));
        $this->_initAction()
                ->_setActiveMenu('affiliate/report')
                ->_addBreadcrumb(Mage::helper('affiliate')->__('Report affiliate'), Mage::helper('affiliate')->__('Affiliate Dashboard'))
                ->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatereport_dashboard'))
                ->renderLayout();
    }
}