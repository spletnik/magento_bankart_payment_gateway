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
class Spletnisistemi_Bankart_Helper_Data extends Mage_Payment_Helper_Data {
    /**
     * Format amount value (2 digits after the decimal point)
     *
     * @param float $amount
     * @return float
     */
    public function formatAmount($amount) {
        return round($amount, 2);
    }

    public function getPost($key) {
        return $this->getRequest()->getPost($key);
    }

    public function getRequest() {
        if ($controller = Mage::app()->getFrontController()) {
            $this->_request = $controller->getRequest();
        } else {
            throw new Exception(Mage::helper('core')->__("Can't retrieve request object"));
        }
        return $this->_request;
    }
}