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
class Spletnisistemi_Bankart_Block_Form extends Mage_Payment_Block_Form_Cc {
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('bankart/form.phtml');

        // RM: Magento 1.4.1 clears the session order items list after place order. Store it for later use
        Mage::getSingleton('core/session')->setBankartQuote(Mage::getSingleton('checkout/session')->getQuote());
    }
}