<?php

require_once(Mage::getModuleDir('controllers','Quickpay_Payment').DS.'PaymentController.php');


class Digidennis_QuickpayDolink_PaymentController extends Quickpay_Payment_PaymentController
{
    /**
     * Handle customer being redirected from QuickPay
     */
    public function linksuccessAction()
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($this->_getSession()->getLastRealOrderId());

        $payment = Mage::getModel('quickpaypayment/payment');

        $quoteID = $this->_getSession()->getLastQuoteId();
        $this->_getSession()->setLastSuccessQuoteId($quoteID);

        if ($quoteID) {
            $quote = Mage::getModel('sales/quote')->load($quoteID);
            $quote->setIsActive(false)->save();
        }

        // CREATES INVOICE if payment instantcapture is ON
        if ((int)$payment->getConfigData('instantcapture') == 1 && (int)$payment->getConfigData('instantinvoice') == 1) {
            if ($order->canInvoice()) {
                $invoice = $order->prepareInvoice();
                $invoice->register();
                $invoice->setEmailSent(true);
                $invoice->getOrder()->setCustomerNoteNotify(true);
                $invoice->sendEmail(true, '');
                Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder())->save();

                $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_COMPLETE);
                $order->save();
            }
        } else {
            if (((int)$payment->getConfigData('sendmailorderconfirmationbefore')) == 1) {
                $this->sendEmail($order);
            }
        }

        $this->_redirect('checkout/onepage/success');
    }

    public function dolinkAction()
    {
        $orderid = base64_decode($this->getRequest()->getParam('link_id'));
        $this->_getSession()->setLastRealOrderId($orderid);
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
        $this->_getSession()->setLastOrderId($order->getId());
        $this->_getSession()->setLastQuoteId($order->getQuoteId());
        $paymentlink = Mage::helper('digidennis_quickpaydolink')->paymentLink($order);
        $this->_redirectUrl($paymentlink);
    }

}
