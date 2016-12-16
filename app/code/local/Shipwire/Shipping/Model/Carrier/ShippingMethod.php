<?php

/**
 * Shipwire real-time rating method for Magento
 *
 * Copyright (C) 2013, Shipwire Inc.
 */
class Shipwire_Shipping_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'shipwire_shipping';

    protected $_version = 'Magento Rating Module 1.3.3';

    protected $_apiEndpoint = 'https://api.shipwire.com/exec/RateServices.php';

    protected $_rawRequest = NULL;
    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_rawRequest = $request;
        
        // skip if not enabled
        if (!$this->getConfigFlag('active')) {
            return FALSE;
        }

        /*
         * Ensure we have a meaningful amount of information to rate with
         */
        $shipToCountry = $request->getDestCountryId();
        if (empty($shipToCountry)) {
            return FALSE;
        }
        $requestItems = $request->getAllItems();
        if (count($requestItems) == 0) {
            return FALSE;
        }

        $response = $this->_submitRequest($request);

        if (empty($response)) {
            /**
             * @var $error Mage_Shipping_Model_Rate_Result_Error
             */
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage('No shipping methods are available');
            return $error;
        }

        /**
         * @var $result Mage_Shipping_Model_Rate_Result
         */
        $result = Mage::getModel('shipping/rate_result');
        foreach ($response as $rMethod) {
            /**
             * @var $method Mage_Shipping_Model_Rate_Result_Method
             */
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
            
            // This hook allows pricing overrides, free shipping, handeling, etc...
            $price = $this->getMethodPrice($rMethod['amount'], $rMethod['code']);
            
            $method->setMethod($rMethod['code']);
            $method->setMethodTitle($rMethod['title']);
            $method->setCost($rMethod['amount']);
            $method->setPrice($price);

            $result->append($method);
        }
        $this->_result = $result;
        $this->_updateFreeMethodQuote($request);
        return $this->_result;
    }
    public function getMethodPricee($cost, $method='')
    {
        if ($method == $this->getConfigData($this->_freeMethod) && $this->getConfigData('free_shipping_enable')
            && $this->getConfigData('free_shipping_subtotal') <= $this->_rawRequest->getBaseSubtotalInclTax()
        ) {
            $price = '0.00';
        } else {
            $price = $cost;
        }
        return $price;
    }

    /**
     * Override this for Shipwire.  We only quote $0 shipping cost if all the items in the cart qualify for free shipping
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return null
     */
    protected function _updateFreeMethodQuote($request)
    {
//      echo "getFreeMethodWeight: " . $request->getFreeMethodWeight();
//      echo "hasFreeMethodWeight: " . $request->hasFreeMethodWeight();
//      echo "getPackageWeight: " . $request->getPackageWeight();
        
        if ($request->getFreeMethodWeight() == $request->getPackageWeight() || !$request->hasFreeMethodWeight()) {
            return;
        }
        
        $freeMethod = $this->getConfigData($this->_freeMethod);
        if (!$freeMethod) {
            return;
        }
        $freeRateId = false;

        if (is_object($this->_result)) {
            foreach ($this->_result->getAllRates() as $i=>$item) {
                if ($item->getMethod() == $freeMethod) {
                    $freeRateId = $i;
                    break;
                }
            }
        }

        if ($freeRateId === false) {
            return;
        }
        
        if ($request->getFreeMethodWeight() == 0) {        // If the entire order has no weight because it is free then set the price to 0
            $this->_result->getRateById($freeRateId)->setPrice(0);
        }

    }

    public function getAllowedMethods()
    {
        return array('shipwire_shipping'=> $this->getConfigData('name'));
    }

    public function isTrackingAvailable()
    {
        return TRUE;
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $requestVar
     * @return array
     */
    private function _submitRequest(Mage_Shipping_Model_Rate_Request $requestVar)
    {

        $shipwireAvailableServices = $this->getConfigData('availableservices');

        $shipwireUsername = $this->getConfigData('shipwire_email');
        $shipwirePassword = $this->getConfigData('shipwire_password');

        $orderCurrencyCode = 'USD';
        /**
         * @var $orderCurrency Mage_Directory_Model_Currency
         */
        $orderCurrency = $requestVar->getBaseCurrency();
        if (!empty($orderCurrency)) {
            $orderCurrencyCode = $orderCurrency->getCode();
        }

        $orderNumber = uniqid('magento', TRUE);

        $shipToAddress1   = $requestVar->getDestStreet();
        $shipToCity       = $requestVar->getDestCity();
        $shipToRegion     = $requestVar->getDestRegionCode();
        $shipToCountry    = $requestVar->getDestCountryId();
        $shiptoPostalCode = $requestVar->getDestPostcode();

        $requestItems = $requestVar->getAllItems();

        $itemXml = $this->_buildRequestItemXml($requestItems);

        $rateRequestXml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE RateRequest SYSTEM "http://www.shipwire.com/exec/download/RateRequest.dtd">
<RateRequest currency="$orderCurrencyCode">
  <Username><![CDATA[$shipwireUsername]]></Username>
  <Password><![CDATA[$shipwirePassword]]></Password>
  <Source>{$this->_version}</Source>
  <Order id="$orderNumber">
    <AddressInfo type="ship">
      <Address1><![CDATA[$shipToAddress1]]></Address1>
      <City><![CDATA[$shipToCity]]></City>
      <State><![CDATA[$shipToRegion]]></State>
      <Country><![CDATA[$shipToCountry]]></Country>
      <Zip><![CDATA[$shiptoPostalCode]]></Zip>
    </AddressInfo>
    $itemXml
  </Order>
</RateRequest>
XML;

        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $this->_apiEndpoint);
        curl_setopt($curlSession, CURLOPT_POST, true);
        curl_setopt($curlSession, CURLOPT_HTTPHEADER,
                    array('Content-Type: application/xml'));
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $rateRequestXml);
        curl_setopt($curlSession, CURLOPT_HEADER, false);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_TIMEOUT, 60);
        $response = curl_exec($curlSession);

        $emptyRateResult = array();

        if (FALSE === $response) {
            return $emptyRateResult;
        }

        $parser = xml_parser_create();

        xml_parse_into_struct($parser, $response, $xmlVals, $xmlIndex);

        xml_parser_free($parser);

        foreach ($xmlVals as $key) {
            if ($key['tag'] == 'STATUS') {
                if ($key['value'] != 'OK') {
                    return $emptyRateResult;
                }

            }
        }

        $code              = array();
        $method            = array();
        $cost              = array();
        $supportedServices = explode(',', $shipwireAvailableServices);

        foreach ($xmlVals as $key) {
            if ($key['tag'] == 'QUOTE' && $key['type'] == 'open'
                && $key['level'] == 4
            ) {
                $code[] = $key['attributes']['METHOD'];
            }
            if ($key['tag'] == 'SERVICE' && $key['type'] == 'complete'
                && $key['level'] == 5
            ) {
                $method[] = $key['value'];
            }
            if ($key['tag'] == 'COST' && $key['type'] == 'complete'
                && $key['level'] == 5
            ) {
                $cost[] = $key['value'];
            }
        }

        $la = count($code);
        $lb = count($method);
        $lc = count($cost);

        $rateResult = array();
        if ($la == $lb && $lb == $lc) {
            foreach ($code as $index => $value) {
                if (in_array($value, $supportedServices)) {
                    $rateResult[] = array(
                        'code'   => $code[$index],
                        'title'  => $method[$index],
                        'amount' => $cost[$index]
                    );
                }
            }
        }
        return $rateResult;

    }

    /**
     * @param array $requestItems
     * @return string
     */
    private function _buildRequestItemXml(array $requestItems)
    {
        $itemXml = '';
        $itemNum = 1;
        if (count($requestItems) > 0) {
            $itemIncluded = array();
            $index        = 0;
            // First match up Configurable's with likely simples, mark off the items as included or not included.  Any simple types left over will be included
            foreach ($requestItems as $requestItem) {
                if (!array_key_exists($index, $itemIncluded)
                    && $requestItem->product_type == 'configurable'
                ) { // See if we have a configurable SKU
                    // Next see if the next item is the same SKU, simple, and with quantity 1
                    if (array_key_exists($index + 1, $requestItems)
                        && $requestItems[$index + 1]->product_type == 'simple'
                        && $requestItems[$index + 1]->sku == $requestItem->sku
                        && $requestItems[$index + 1]->qty == 1
                    ) {
                        $itemIncluded[$index] = 1; // Include the configurable
                        $itemIncluded[$index
                                      + 1]    = 0; // Don't include the following simple
                    } else {
                        $foundSimple = 0;
                        // If we can't find a simple one after then see if we have and matching simple that is not already used.
                        for ($i = 0; $i < count($requestItems); $i++) {
                            if ($requestItems[$i]->product_type == 'simple'
                                && !array_key_exists($i, $itemIncluded)
                                && // Only consider items that are otherwise not yet evaluated
                                $requestItems[$i]->sku == $requestItem->sku
                                && $requestItems[$i]->qty == 1
                            ) {
                                $itemIncluded[$index] = 1; // Include configurable
                                $itemIncluded[$i]     = 0; // Don't include matching simple
                                $foundSimple          = 1;
                                break;
                            }
                        }
                        if (!$foundSimple) { // If we can't find a matching simple then don't consider the configurable
                            $itemIncluded[$index] = 0; //  Don't include this configurable
                        }
                    }

                }
                $index++;
            }
            // Now spin through again using the $itemIncluded array to determine what goes into rating
            $index = 0;
            foreach ($requestItems as $requestItem) {
                if (!array_key_exists($index, $itemIncluded)
                    || $itemIncluded[$index] == 1
                ) { // We need detached singles (no array key, or included items
                    $itemXml .= '<Item num="' . $itemNum++ . '">';
                    $itemXml .= '<Code>' . $requestItem->sku . '</Code>';
                    $itemXml .=
                        '<Quantity>' . $requestItem->qty . '</Quantity>';
                    $itemXml .= '</Item>';
                }
                $index++;

            }
            return $itemXml;
        }

        return $itemXml;
    }
}
