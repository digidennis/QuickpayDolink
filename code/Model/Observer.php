<?php
class Digidennis_QuickpayDolink_Model_Observer extends Quickpay_Payment_Model_Observer
{

    /**
     * Send payment link to customer when admin creates an order
     *
     * @param Varien_Event_Observer $observer
     */
    public function onCheckoutSubmitAllAfter(Varien_Event_Observer $observer)
    {

        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        if( $order->getPayment()->getMethod() != 'quickpaypayment_payment' )
            return;

        $payment = Mage::getSingleton('quickpaypayment/payment');
        $parameters = array(
            "agreement_id"                 => $payment->getConfigData("agreementid"),
            "amount"                       => $order->getTotalDue() * 100,
            "continueurl"                  => $this->getSuccessUrl($order->getStore()),
            "cancelurl"                    => $this->getCancelUrl($order->getStore()),
            "callbackurl"                  => $this->getCallbackUrl($order->getStore()),
            "language"                     => $payment->calcLanguage(Mage::app()->getLocale()->getLocaleCode()),
            "autocapture"                  => $payment->getConfigData('instantcapture'),
            "autofee"                      => $payment->getConfigData('transactionfee'),
            "payment_methods"              => $order->getPayment()->getMethodInstance()->getPaymentMethods(),
            "google_analytics_tracking_id" => $payment->getConfigData('googleanalyticstracking'),
            "google_analytics_client_id"   => $payment->getConfigData('googleanalyticsclientid'),
            "customer_email"               => $order->getCustomerEmail() ?: '',
        );

        $result = Mage::helper('quickpaypayment')->qpCreatePayment($order);
        $result = Mage::helper('quickpaypayment')->qpCreatePaymentLink($result->id, $parameters);
        Mage::log($order->debug(), null, 'qp_order.log');
    }

}
