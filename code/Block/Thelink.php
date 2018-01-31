<?php

class Digidennis_QuickpayDolink_Block_Thelink extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('digidennis/quickpaydolink/thelink.phtml');
    }

    public function getTheLink()
    {
        return Mage::helper('digidennis_quickpaydolink')->paymentLink($this->getOrder());
    }
}