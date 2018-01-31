<?php

class Digidennis_QuickpayDolink_Block_Thelink extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        return Mage::getHelper('digidennis_quickpaydolink')->paymentLink($this->getOrder());
    }
}