<?php
/******************************************************************************
 *
 * File:         e24PaymentPipe.php
 * Description:  Razred e24PaymentPipe je namenjen za posiljanje inicializacije
 *               placila preko POST zahtevka.
 * Author:       Igor Rozman
 * Created:      26.11.2008
 * Modified:     01.12.2008
 *               Razred pridobiva podatke o trgovcu z dekripcijo resource datoteke.
 *               06.05.2010
 *               Podprto je odkriptiranje resource datoteke za eCommerceGateway
 *               verzijo 3.2. Pri tem se namesto password posilja psswordhash.
 * Language:     PHP (v 5.2.6)
 *               Extensions: CURL, OPENSSL
 * Package:      paymentpipephp (testna koda ze testiranje spletnih transakcij)
 *
 * (c) Copyright 2008, Bankart d.o.o., all rights reserved.
 *
 ******************************************************************************/
// Razred za dekripcijo podatkov iz resource datoteke.
class Spletnisistemi_Bankart_Model_Bankart_E24PaymentPipe extends Mage_Core_Model_Abstract {
    var $SUCCESS = 0;
    var $FAILURE = -1;
    var $strIDOpen = "<id>";
    var $strPasswordOpen = "<password>";
    var $strWebAddressOpen = "<webaddress>";
    var $strPortOpen = "<port>";
    var $strContextOpen = "<context>";
    var $strIDClose = "</id>";
    var $strPasswordClose = "</password>";
    var $strWebAddressClose = "</webaddress>";
    var $strPortClose = "</port>";
    var $strContextClose = "</context>";
    var $webAddress;
    var $port;
    var $id;
    var $password;
    var $passwordhash;
    var $action;
    var $transId;
    var $amt;
    var $responseURL;
    var $trackId;
    var $udf1;
    var $udf2;
    var $udf3;
    var $udf4;
    var $udf5;
    var $paymentPage;
    var $paymentId;
    var $result;
    var $auth;
    var $ref;
    var $avr;
    var $date;
    var $currency;
    var $errorURL;
    var $language;
    var $context;
    var $resourcePath;
    var $alias;
    var $responseCode;
    var $cvv2Verification;
    var $error;
    var $rawResponse;
    var $debugOn;
    var $debugMsg;

    function e24PaymentPipe() {
        $this->webAddress = "";
        $this->port = "";
        $this->id = "";
        $this->password = "";
        $this->action = "";
        $this->transId = "";
        $this->amt = "";
        $this->responseURL = "";
        $this->trackId = "";
        $this->udf1 = "";
        $this->udf2 = "";
        $this->udf3 = "";
        $this->udf4 = "";
        $this->udf5 = "";
        $this->paymentPage = "";
        $this->paymentId = "";
        $this->result = "";
        $this->auth = "";
        $this->ref = "";
        $this->avr = "";
        $this->date = "";
        $this->currency = "";
        $this->errorURL = "";
        $this->language = "";
        $this->context = "";
        $this->resourcePath = "";
        $this->alias = "";
        $this->responseCode = "";
        $this->cvv2Verification = "";
        $this->error = "";
        $this->rawResponse = "";
        $this->debugOn = false;
    }

    function getWebAddress() {
        return $this->webAddress;
    }

    function setWebAddress($val) {
        $this->webAddress = $val;
    }

    function getPort() {
        return $this->port;
    }

    function setPort($val) {
        $this->port = $val;
    }

    function setTermId($val) {
        $this->id = $val;
    }

    function getTermId() {
        return $this->id;
    }

    function getPassword() {
        return $this->password;
    }

    function setPassword($val) {
        $this->password = $val;
    }

    function getAction() {
        return $this->action;
    }

    function setAction($val) {
        $this->action = $val;
    }

    function getCvv2Verification() {
        return $this->cvv2Verification;
    }

    function setTranId($val) {
        $this->transId = $val;
    }

    function getTranId() {
        return $this->transId;
    }

    function getAmt() {
        return $this->amt;
    }

    function setAmt($val) {
        $this->amt = $val;
    }

    function getResponseURL() {
        return $this->responseURL;
    }

    function setResponseURL($val) {
        $this->responseURL = $val;
    }

    function getTrackId() {
        return $this->trackId;
    }

    function setTrackId($val) {
        $this->trackId = $val;
    }

    function getUdf1() {
        return $this->udf1;
    }

    function setUdf1($val) {
        $this->udf1 = $val;
    }

    function getUdf2() {
        return $this->udf2;
    }

    function setUdf2($val) {
        $this->udf2 = $val;
    }

    function getUdf3() {
        return $this->udf3;
    }

    function setUdf3($val) {
        $this->udf3 = $val;
    }

    function getUdf4() {
        return $this->udf4;
    }

    function setUdf4($val) {
        $this->udf4 = $val;
    }

    function getUdf5() {
        return $this->udf5;
    }

    function setUdf5($val) {
        $this->udf5 = $val;
    }

    function getPaymentPage() {
        return $this->paymentPage;
    }

    function setPaymentPage($val) {
        $this->paymentPage = $val;
    }

    function getPaymentId() {
        return $this->paymentId;
    }

    function setPaymentId($val) {
        $this->paymentId = $val;
    }

    function getResult() {
        return $this->result;
    }

    function getResponseCode() {
        return $this->responseCode;
    }

    function getAuth() {
        return $this->auth;
    }

    function getAvr() {
        return $this->avr;
    }

    function getDate() {
        return $this->date;
    }

    function getRef() {
        return $this->ref;
    }

    function getContext() {
        return $this->context;
    }

    function setContext($val) {
        $this->context = $val;
    }

    function getCurrency() {
        return $this->currency;
    }

    function setCurrency($val) {
        $this->currency = $val;
    }

    function getLanguage() {
        return $this->language;
    }

    function setLanguage($val) {
        $this->language = $val;
    }

    function getErrorURL() {
        return $this->errorURL;
    }

    function setErrorURL($val) {
        $this->errorURL = $val;
    }

    function getResourcePath() {
        return $this->resourcePath;
    }

    function setResourcePath($val) {
        $this->resourcePath = $val;
    }

    function getAlias() {
        return $this->alias;
    }

    function setAlias($val) {
        $this->alias = $val;
    }

    function getErrorMsg() {
        return $this->error;
    }

    function getRawResponse() {
        return $this->rawResponse;
    }

    // Funkcija pripravi incializacijsko sporocilo za placilno transakcijo
    // (PURCHASE, AUTHORIZATION) in ga poslje placilnemu strezniku preko POST metode.
    function performPaymentInitialization() {
        $buf = "";

        // Preberemo se podatke iz resource datoteke.
        $secResource = Mage::getModel('bankart/bankart_SecureResource');
        $secResource->setDebugOn($this->debugOn);
        $secResource->setAlias($this->alias);
        $secResource->setResourcePath($this->resourcePath);
        if (!$secResource->getSecureSettings()) {
            if ($this->debugOn)
                $this->debugMsg->append($secResource->getDebugMsg());
            $this->error = $secResource->getError();
            return -1;
        }
        $this->id = $secResource->getTermID();
        $this->password = $secResource->getPassword();
        $this->passwordhash = $secResource->getPasswordHash();
        $this->port = $secResource->getPort();
        $this->webAddress = $secResource->getWebAddress();
        $this->context = $secResource->getContext();

        // Sestavimo vsebino POST telesa, ki se ga poslje Commerce Gateway serverju.
        if (strlen($this->id) > 0)
            $buf = $buf . "id=" . $this->id . "&";
        if (strlen($this->password) > 0)
            $buf = $buf . "password=" . $this->password . "&";
        if (strlen($this->passwordhash) > 0)
            $buf = $buf . "passwordhash=" . $this->passwordhash . "&";
        if (strlen($this->amt) > 0)
            $buf = $buf . "amt=" . $this->amt . "&";
        if (strlen($this->currency) > 0)
            $buf = $buf . "currencycode=" . $this->currency . "&";
        if (strlen($this->action) > 0)
            $buf = $buf . "action=" . $this->action . "&";
        if (strlen($this->language) > 0)
            $buf = $buf . "langid=" . $this->language . "&";
        if (strlen($this->responseURL) > 0)
            $buf = $buf . "responseURL=" . $this->responseURL . "&";
        if (strlen($this->errorURL) > 0)
            $buf = $buf . "errorURL=" . $this->errorURL . "&";
        if (strlen($this->trackId) > 0)
            $buf = $buf . "trackid=" . $this->trackId . "&";
        if (strlen($this->udf1) > 0)
            $buf = $buf . "udf1=" . $this->udf1 . "&";
        if (strlen($this->udf2) > 0)
            $buf = $buf . "udf2=" . $this->udf2 . "&";
        if (strlen($this->udf3) > 0)
            $buf = $buf . "udf3=" . $this->udf3 . "&";
        if (strlen($this->udf4) > 0)
            $buf = $buf . "udf4=" . $this->udf4 . "&";
        if (strlen($this->udf5) > 0)
            $buf = $buf . "udf5=" . $this->udf5;

        // Placilnemu serverju se poslje inicializacijsko sporocilo z uporabo POST metode.
        $output = $this->sendMessage($buf, "PaymentInitHTTPServlet");
        if ($output === $this->FAILURE)
            return $this->FAILURE;

        // Odgovor placilnega serverja razparsamo.
        $colonIdx = strpos($this->rawResponse, ":");
        if ($colonIdx === FALSE) {
            $this->error = "Payment Initialization returned an invalid response: " . $this->rawResponse;
            return $this->FAILURE;
        } else {
            $this->paymentId = substr($this->rawResponse, 0, $colonIdx);
            $this->paymentPage = substr($this->rawResponse, $colonIdx + 1);
            return $this->SUCCESS;
        }
    }

    // Funkcija pripravi sporocilo za zaporedno placilno transakcijo
    // (CAPTURE) in ga poslje placilnemu strezniku preko POST metode.

    function sendMessage($msg, $servletName) {
        $urlBuf = "";

        if (!(strlen($this->webAddress) > 0)) {
            $this->error = "No URL specified.";
            return $this->FAILURE;
        }

        if ($this->port == "443")
            $urlBuf = "https://";
        else
            $urlBuf = "http://";
        $urlBuf = $urlBuf . $this->webAddress;
        if (strlen($this->port) > 0)
            $urlBuf = $urlBuf . ":" . $this->port;
        if (strlen($this->context) > 0) {
            if ($this->context[0] != "/")
                $urlBuf = $urlBuf . "/";
            $urlBuf = $urlBuf . $this->context;
            if (!$this->context[strlen($this->context) - 1] != "/")
                $urlBuf = $urlBuf . "/";
        } else {
            $urlBuf = $urlBuf . "/";
        }
        $urlBuf = $urlBuf . "servlet/" . $servletName;

        if ($this->do_post_request($urlBuf, $msg))
            return $this->SUCCESS;
        else
            return $this->FAILURE;
    }

    // Funkcija je namenjena posiljanju POST zahtevka. Pri cemer se URL naslov sestavi
    // iz podatkov v resource datoteki.

    function do_post_request($url, $data) {
        $ch = curl_init($url);
        if (is_resource($ch) === false) {
            $this->error = "Failed to initialize connection for sending POST Request!";
            return false;
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 45);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 45);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

        $this->rawResponse = curl_exec($ch);
        $info = curl_getinfo($ch);
        if ($this->rawResponse === false || $info['http_code'] != 200) {
            $this->error = "Failed to read data from " . $url . "!";
            return false;
        }

        curl_close($ch);
        return true;
    }

    // Funkcija poslje POST sporocilo z podatki $data na spletni naslov podan z $url.

    function performPayment() {
        $buf = "";

        // Preberemo se podatke iz resource datoteke.
        $secResource = Mage::getModel('bankart/bankart_SecureResource');
        $secResource->setDebugOn($this->debugOn);
        $secResource->setAlias($this->alias);
        $secResource->setResourcePath($this->resourcePath);
        if (!$secResource->getSecureSettings()) {
            if ($this->debugOn)
                $this->debugMsg->append($secResource->getDebugMsg());
            $this->error = $secResource->getError();
            return -1;
        }
        $this->id = $secResource->getTermID();
        $this->password = $secResource->getPassword();
        $this->passwordhash = $secResource->getPasswordHash();
        $this->port = $secResource->getPort();
        $this->webAddress = $secResource->getWebAddress();
        $this->context = $secResource->getContext();

        // Sestavimo vsebino POST telesa, ki se ga poslje Commerce Gateway serverju.
        if (strlen($this->transId) > 0)
            $buf = $buf . "transid=" . $this->transId . "&";
        if (strlen($this->paymentId) > 0)
            $buf = $buf . "paymentid=" . $this->paymentId . "&";
        if (strlen($this->id) > 0)
            $buf = $buf . "id=" . $this->id . "&";
        if (strlen($this->password) > 0)
            $buf = $buf . "password=" . $this->password . "&";
        if (strlen($this->passwordhash) > 0)
            $buf = $buf . "passwordhash=" . $this->passwordhash . "&";
        if (strlen($this->action) > 0)
            $buf = $buf . "action=" . $this->action . "&";
        if (strlen($this->amt) > 0)
            $buf = $buf . "amt=" . $this->amt . "&";
        if (strlen($this->trackId) > 0)
            $buf = $buf . "trackid=" . $this->trackId . "&";
        if (strlen($this->udf1) > 0)
            $buf = $buf . "udf1=" . $this->udf1 . "&";
        if (strlen($this->udf2) > 0)
            $buf = $buf . "udf2=" . $this->udf2 . "&";
        if (strlen($this->udf3) > 0)
            $buf = $buf . "udf3=" . $this->udf3 . "&";
        if (strlen($this->udf4) > 0)
            $buf = $buf . "udf4=" . $this->udf4 . "&";
        if (strlen($this->udf5) > 0)
            $buf = $buf . "udf5=" . $this->udf5;

        // Placilnemu serverju se poslje sporocilo z uporabo POST metode.
        $output = $this->sendMessage($buf, "PaymentTranHTTPServlet");
        if ($output === $this->FAILURE)
            return $this->FAILURE;

        // Odgovor placilnega serverja razparsamo.
        $colonIdx = strpos($this->rawResponse, "CAPTURED");
        if ($colonIdx === FALSE) {
            $this->error = $this->rawResponse;
            return $this->FAILURE;
        } else {
            return $this->SUCCESS;
        }
    }

    function clearFields() {
        $this->error = "";
        $this->paymentPage = "";
        $this->paymentId = "";
        $this->responseCode = "";
    }

    function getDebugOn() {
        return $this->debugOn;
    }

    function setDebug($val) {
        $this->debugOn = $val;
    }

    function getDebugMsg() {
        if ($this->debugOn && $this->debugMsg != null)
            return $this->debugMsg;
        else
            return "";
    }
}