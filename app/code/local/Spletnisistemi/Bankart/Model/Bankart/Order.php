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
class Spletnisistemi_Bankart_Model_Bankart_Order extends Mage_Core_Model_Session_Abstract {
    // Podatki o placniski transakciji.
    protected $paymentID;
    protected $currency;
    protected $amount;
    protected $trackID;
    protected $tranDate;
    protected $name;
    protected $addr1;
    protected $addr2;
    protected $addr3;
    protected $city;
    protected $state;
    protected $postalCode;
    protected $udf1;
    protected $udf2;
    protected $udf3;
    protected $udf4;
    protected $udf5;
    protected $orderDetails;
    protected $customerIP;

    protected $result;
    protected $auth;
    protected $ref;
    protected $tranID;
    protected $postDate;
    protected $responseCode;
    protected $errMsg;
    protected $errText;

    // Podatki o bazi.
    protected $db;
    protected $hostName;
    protected $hostPort;
    protected $baseName;
    protected $tabName;

    // Namen tega konstruktorja je inicializacija podatkov in povezava na bazo.
    function __construct() {
        $this->paymentID = '';
        $this->amount = '';
        $this->orderDetails = '';
        $this->trackID = '';
        $this->tranDate = '';
        $this->name = '';
        $this->addr1 = '';
        $this->addr2 = '';
        $this->addr3 = '';
        $this->city = '';
        $this->state = '';
        $this->postalCode = '';
        $this->result = '';
        $this->auth = '';
        $this->ref = '';
        $this->tranID = '';
        $this->postDate = '';
        $this->trackID = '';
        $this->udf1 = '';
        $this->udf2 = '';
        $this->udf3 = '';
        $this->udf4 = '';
        $this->udf5 = '';
        $this->responseCode = '';
        $this->currency = '';
        $this->errMsg = '';
        $this->errText = '';
    }

    // Funkcija zapise podatke o transakciji v bazo narocil.
    function commit() {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = 'INSERT INTO ' . Mage::getSingleton('core/resource')->getTableName('bankart_api_debug') . '
                (paymentID, currency, amount, orderDetails, trackID, tranDate, name, addr1, addr2, addr3, city, state, postalCode, result, auth, ref, tranID, postDate, udf1, udf2, udf3, udf4, udf5, responseCode, errMsg, errText, customerIP) 
                VALUES 
                (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';

        $write->query($sql, array(
            $this->paymentID,
            $this->currency,
            $this->amount,
            $this->orderDetails,
            $this->trackID,
            $this->tranDate,
            $this->name,
            $this->addr1,
            $this->addr2,
            $this->addr3,
            $this->city,
            $this->state,
            $this->postalCode,
            $this->result,
            $this->auth,
            $this->ref,
            $this->tranID,
            $this->postDate,
            $this->udf1,
            $this->udf2,
            $this->udf3,
            $this->udf4,
            $this->udf5,
            $this->responseCode,
            $this->errMsg,
            $this->errText,
            $this->customerIP));

        return 0;
    }

    // Funkcija prebere podatke o transakciji iz baze narocil. Kjuc za narocila je PaymentID.
    function fetch($val) {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $result = $write->query("SELECT * FROM " . Mage::getSingleton('core/resource')->getTableName('bankart_api_debug') . " WHERE paymentID=?", array($val))->fetch();

        if ($result === false) {
            return 1;
        }

        $this->paymentID = $val;
        $this->setCurrency($result['currency']);
        $this->setAmount($result['amount']);
        $this->setOrderDetails($result['orderDetails']);
        $this->setTrackID($result['trackID']);
        $this->setTranDate($result['tranDate']);
        $this->setName($result['name']);
        $this->setAddr1($result['addr1']);
        $this->setAddr2($result['addr2']);
        $this->setAddr3($result['addr3']);
        $this->setCity($result['city']);
        $this->setState($result['state']);
        $this->setPostalCode($result['postalCode']);
        $this->setResult($result['result']);
        $this->setAuth($result['auth']);
        $this->setRef($result['ref']);
        $this->setTranID($result['tranID']);
        $this->setPostDate($result['postDate']);
        $this->setudf1($result['udf1']);
        $this->setudf2($result['udf2']);
        $this->setudf3($result['udf3']);
        $this->setudf4($result['udf4']);
        $this->setudf5($result['udf5']);
        $this->setResponseCode($result['responseCode']);
        $this->setErrMsg($result['errMsg']);
        $this->setErrText($result['errText']);
        $this->setCustomerIP($result['customerIP']);

        return 0;
    }

    // Funkcija dopolne podatke o transakciji v bazi narocil. Dopolnjeni podatki so
    // podatki o transakciji potem, ko je obdelana v placilnem sistemu.
    function update() {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "UPDATE " . Mage::getSingleton('core/resource')->getTableName('bankart_api_debug') . " SET result=?, auth=?, ref=?, tranID=?, postDate=?, udf1=?, udf2=?, udf3=?, udf4=?, udf5=?, responseCode=? WHERE paymentID = ?";

        $write->query($sql, array(
            $this->result,
            $this->auth,
            $this->ref,
            $this->tranID,
            $this->postDate,
            $this->udf1,
            $this->udf2,
            $this->udf3,
            $this->udf4,
            $this->udf5,
            $this->responseCode,
            $this->paymentID));

        return 0;
    }

    // Funkcija dopolne podatke o transakciji v bazi narocil, ce placilni streznik CGw javi napako.
    function updateErr() {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = "UPDATE " . Mage::getSingleton('core/resource')->getTableName('bankart_api_debug') . " SET errMsg=?, errText=? WHERE paymentID = ?";

        $write->query($sql, array($this->errMsg, $this->errText, $this->paymentID));

        return 0;
    }

    function getPaymentID() {
        return $this->paymentID;
    }

    function setPaymentID($val) {
        $this->paymentID = $val;
    }

    function getAmount() {
        return $this->amount;
    }

    function setAmount($val) {
        $this->amount = $val;
    }

    function getCurrency() {
        return $this->currency;
    }

    function setCurrency($val) {
        $this->currency = $val;
    }

    function getOrderDetails() {
        return $this->orderDetails;
    }

    function setOrderDetails($val) {
        $this->orderDetails = $val;
    }

    function getTrackID() {
        return $this->trackID;
    }

    function setTrackID($val) {
        $this->trackID = $val;
    }

    function getTranDate() {
        return $this->tranDate;
    }

    function setTranDate($val) {
        $this->tranDate = $val;
    }

    function getName() {
        return $this->name;
    }

    function setName($val) {
        $this->name = $val;
    }

    function getAddr1() {
        return $this->addr1;
    }

    function setAddr1($val) {
        $this->addr1 = $val;
    }

    function getAddr2() {
        return $this->addr2;
    }

    function setAddr2($val) {
        $this->addr2 = $val;
    }

    function getAddr3() {
        return $this->addr3;
    }

    function setAddr3($val) {
        $this->addr3 = $val;
    }

    function getCity() {
        return $this->city;
    }

    function setCity($val) {
        $this->city = $val;
    }

    function getState() {
        return $this->state;
    }

    function setState($val) {
        $this->state = $val;
    }

    function getPostalCode() {
        return $this->postalCode;
    }

    function setPostalCode($val) {
        $this->postalCode = $val;
    }

    function getResult() {
        return $this->result;
    }

    function setResult($val) {
        $this->result = $val;
    }

    function getAuth() {
        return $this->auth;
    }

    function setAuth($val) {
        $this->auth = $val;
    }

    function getRef() {
        return $this->ref;
    }

    function setRef($val) {
        $this->ref = $val;
    }

    function getTranID() {
        return $this->tranID;
    }

    function setTranID($val) {
        $this->tranID = $val;
    }

    function getPostDate() {
        return $this->postDate;
    }

    function setPostDate($val) {
        $this->postDate = $val;
    }

    function getudf1() {
        return $this->udf1;
    }

    function setudf1($val) {
        $this->udf1 = $val;
    }

    function getudf2() {
        return $this->udf2;
    }

    function setudf2($val) {
        $this->udf2 = $val;
    }

    function getudf3() {
        return $this->udf3;
    }

    function setudf3($val) {
        $this->udf3 = $val;
    }

    function getudf4() {
        return $this->udf4;
    }

    function setudf4($val) {
        $this->udf4 = $val;
    }

    function getudf5() {
        return $this->udf5;
    }

    function setudf5($val) {
        $this->udf5 = $val;
    }

    function getResponseCode() {
        return $this->responseCode;
    }

    function setResponseCode($val) {
        $this->responseCode = $val;
    }

    function getErrMsg() {
        return $this->errMsg;
    }

    function setErrMsg($val) {
        $this->errMsg = $val;
    }

    function getErrText() {
        return $this->errText;
    }

    function setErrText($val) {
        $this->errText = $val;
    }

    function getCustomerIP() {
        return $this->customerIP;
    }

    function setCustomerIP($val) {
        $this->customerIP = $val;
    }
}