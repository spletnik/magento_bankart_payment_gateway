<?php

/**
 * Copyright Spletni sistemi, (c) 2009.
 *
 * This program is free software. You are allowed to use the software but NOT allowed to modify the software.
 * It is also not legal to do any changes to the software and distribute it in your own name / brand.
 *
 * @category   Spletnisistemi
 * @package    Spletnisistemi_Bankart
 * @copyright  Copyright (c) 2009 Spletni sistemi (http://spletnisistemi.si)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Spletnisistemi_Bankart_Block_Error extends Mage_Core_Block_Abstract {
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('bankart/error.phtml');
    }

    protected function _toHtml() {
        $html = "
        <div class='page-head'>
            <h3>{$this->__('SPLETNISISTEMI_BANKART_PAYMENT_ERROR')}</h3
        </div>
        <p><strong>" . htmlspecialchars_decode(urldecode(($this->getRequest()->getPost('error', false)))) . "</strong></p>
        <p><strong>" . htmlspecialchars_decode(urldecode(($this->getRequest()->getParam('error', false)))) . "</strong></p>
        
        <p>{$this->__('SPLETNISISTEMI_BANKART_FAILURE_CONTINUE_SHOPPING', $this->getContinueShoppingUrl())}</p>";

        return $html;
    }

    public function getContinueShoppingUrl() {
        return Mage::getUrl('checkout/cart');
    }
}