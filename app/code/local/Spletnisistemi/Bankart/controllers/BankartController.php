<?php

/**
 * Copyright TR splet, (c) 2009.
 *
 * This program is free software. You are allowed to use the software but NOT allowed to modify the software.
 * It is also not legal to do any changes to the software and distribute it in your own name / brand.
 *
 * @category   Spletnisistemi
 * @package    Spletnisistemi_Bankart
 * @copyright  Copyright (c) 2009 TR splet (http://spletnisistemi.si)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Spletnisistemi_Bankart_BankartController extends Mage_Core_Controller_Front_Action {
    protected $_redirectBlockType = 'bankart/redirect';

    /**
     * Get the Singleton of current Quote
     *
     * @return  Singleton Quote
     */
    public function getQuote() {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Get the Singleton of current Checkout Session
     *
     * @return  Singleton Checkout Session
     */
    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Redirect Action
     *
     * Redirects the customer to start payment process
     *
     * @param   none
     * @return  void
     */
    public function redirectAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('bankart/redirect'));
        $this->renderLayout();
    }

    /**
     * Payment Action
     *
     * Redirects the customer to Bankart to fulfill the payment.
     *
     * @param   none
     * @return  void
     */
    public function paymentAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('bankart/payment'));
        $this->renderLayout();
    }

    /**
     * Cancel Action
     *
     * is called if the customer cancels his payment on Bankart payment page
     *
     * @param   none
     * @return  void
     */
    public function errorAction() {
        $session = $this->getCheckout();

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());

        $order->setState(Mage_Sales_Model_Order::STATE_HOLDED);
        $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_HOLDED, Mage::helper('bankart')->__('SPLETNISISTEMI_BANKART_PAYMENT_ERROR'));
        $order->save();

        $session->unsQuoteId();
        $session->getQuote()->setIsActive(false)->save();

        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('bankart/error'));
        $this->renderLayout();
    }

    /**
     * Update Action
     *
     * This URL is called from Bankart after customer is taken to Bankart
     * */
    public function updateAction() {
        $model = Mage::getModel('bankart/bankart');
        echo $model->updateRequest();
        exit;
    }

    /**
     * successAction
     * */
    public function statusAction() {
        $bankart = Mage::getModel('bankart/bankart');

        //
        // Load the order object from the get orderid parameter
        //
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($bankart->getCheckout()->getLastRealOrderId());

        if (!$order->getId()) {
            $this->_redirect('checkout/cart');
            return null;
        }

        // Complete bankart request
        $status = $bankart->completeRequest();

        // Done
        if ($status === true) {
            // Set quote not $status
            $session = $this->getCheckout();
            $session->getQuote()->setIsActive(($status))->save();

            //
            // Send email order confirmation (if enabled). May be done only once!
            //
            $payment = $order->getPayment()->getMethodInstance();
            if (((int)$payment->getConfigData('sendmailorderconfirmation')) == 1) {
                $order->sendNewOrderEmail();
            }

            //
            // Set the status to the new epay status after payment
            // and save to database
            //
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
            $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, Mage::helper('bankart')->__('SPLETNISISTEMI_BANKART_PAYMENT_SUCC'));
            $order->save();

            $this->_redirect('checkout/onepage/success');
        } else {
            $this->_redirect('bankart/bankart/error/', array('error' => $status));
        }

        return true;
    }

    /**
     * checks if the session is still valid
     */
    protected function _expireAjax() {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit();
        }
    }

    /**
     * Printing simple HTMl response.
     */
    protected function _printResponse($message) {
        $html = '<html><body>';
        $html .= $message;
        $html .= '</body></html>';
        echo $html;
    }
}

?>