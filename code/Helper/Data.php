<?php
class Digidennis_QuickpayDolink_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $apiUrl = "https://api.quickpay.net";
    protected $apiVersion = 'v10';
    protected $apiKey = "";
    // Loaded from the configuration
    protected $format = "application/json";


    public function paymentLink($order)
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $this->apiKey = Mage::getStoreConfig('payment/quickpaypayment_payment/apikey', $storeId);
        $client = new Zend_Http_Client();
        $headers = array(
            'Authorization'  => 'Basic ' . base64_encode(":" . $this->apiKey),
            'Accept-Version' => $this->apiVersion,
            'Accept'         => $this->format,
            'Content-Type'   => 'application/json'
        );
        $client->setUri($this->apiUrl . "?order_id={$order->getId()}");
        $client->setMethod(Zend_Http_Client::GET);
        $request = $client->request();

        if (! in_array($request->getStatus(), array(200, 201, 202))) {
            Mage::throwException($request->getBody());
        }

        $result = json_decode($request->getBody())[0];
        if( $result && count($result) && $result['state'] === 'initial' && count($result['link']))
        {
            return $result['link']['url'];
        }
        return '';
    }
}
