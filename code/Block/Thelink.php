<?php

class Digidennis_QuickpayDolink_Block_Thelink extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        return Mage::helper('digidennis_quickpaydolink')->paymentLink($this->getOrder());
    }
}