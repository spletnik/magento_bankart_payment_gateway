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
class Spletnisistemi_Bankart_Model_Bankart_Payment {
    public function initPayment($customerData, $orderId) {
        $paymentPipe = Mage::getModel('bankart/bankart_e24PaymentPipe');
        $nar = Mage::getModel('bankart/bankart_order');

        // Pot do resource datoteke, ki jo trgovec dobi na svoji spletni strani sistema CGw. V njej
        // so zakodirani podatki potrebni za povezavo na CGw sistem. Ker se resource datoteka razpakira
        // v zacasno datoteko, morajo biti dovoljenja za mapo v kateri se nahaja resource datoteka
        // nastavljena polega branja tudi na pisanje.
        $paymentPipe->setResourcePath($_POST['resourcePath']);
        // Alias na trgovcev terminal. Ta podatek se dobi na trgovcevi spletni strani v CGw.
        $paymentPipe->setAlias($_POST['terminalAlias']);
        // Zajamemo POST podatke o transakciji in nastavitvi HPP strani.
        // Tip transakcije.
        $paymentPipe->setAction($_POST['vrstaTran']);
        // Vsota nakupa.
        $paymentPipe->setAmt($_POST['vrednost']);

        // V primeru transakcije Purchase ali Authorization zgradimo Payment Init Message in izvedemo
        // tok placevanja.
        if ($paymentPipe->getAction() == '1' || $paymentPipe->getAction() == '4') {
            // Denarna valuta, 978 - Euro.
            $paymentPipe->setCurrency($_POST['valuta']);
            // Jezik, ki naj se prikaze na HPP strani.
            $paymentPipe->setLanguage($_POST['vrstaJezika']);
            // Trgovcev URL naslov na katerega se poslje podatke o stanju placila potem, ko kupec vnese podatke o
            // placilni kartici na HPP strani.
            $paymentPipe->setResponseURL($_POST['responseURL']);
            // Trgovcev URL naslov na katerega se preusmeri kupca v primeru sistemske napake.
            $paymentPipe->setErrorURL($_POST['errorURL']);
            // Dodatna polja, ki treunto niso namenjena uporabi.
            $paymentPipe->setUdf1('YourUserDefinedField1');
            $paymentPipe->setUdf2('YourUserDefinedField2');
            $paymentPipe->setUdf3('YourUserDefinedField3');
            $paymentPipe->setUdf4('YourUserDefinedField4');
            $paymentPipe->setUdf5('YourUserDefinedField5');
            $paymentPipe->setTrackId(md5(uniqid()));

            // --------- Posljemo zahtevo po inicalizaciji nakupa oz. HPP strani. ------------------------
            // neuspesna inicializacija
            if ($paymentPipe->performPaymentInitialization() != $paymentPipe->SUCCESS) {
                // Izvede se preusmeritev na trgovcev URL o obvestilu o napaki.   
                return array('url' => $paymentPipe->getErrorURL(), 'data' => array('msg' => $paymentPipe->getErrorMsg()));
                // uspesna inicializacija
            } else {
                // Narocilo zapisemo v bazo.
                $nar->setPaymentID($paymentPipe->getPaymentID());
                $nar->setCurrency($paymentPipe->getCurrency());
                $nar->setAmount($paymentPipe->getAmt());
                $nar->setOrderDetails($orderId);
                $nar->setTrackID($paymentPipe->getTrackID()); // enolicen ID, ki je odvisen od casa kdaj je bila funkcija klicana
                $nar->setTranDate(date('d.m.Y H:i:s'));
                $nar->setName($customerData->getData('firstname') . ' ' . $customerData->getData('lastname'));
                $nar->setAddr1($customerData->getData('street'));
                $nar->setAddr2('-');
                $nar->setAddr3('-');
                $nar->setCity($customerData->getData('city'));
                $nar->setState('Slovenija');
                $nar->setPostalCode($customerData->getData('postcode'));
                $nar->setCustomerIP($this->getRealIpAddr());

                // Narocilo zapisemo v bazo.
                if ($nar->commit() != 0) {
                    // Izvede se preusmeritev na trgovcev URL o obvestilu o napaki.
                    return array('url' => $paymentPipe->getErrorURL(), 'data' => array('msg' => 'Narocilo+se+ni+izvedlo.+Tezave+pri+zapisu+v+bazo+narocil.'));
                }

                // Kupca preusmerimo na HPP stran, katere URL smo dobili iz inicializacijskega sporocila.
                return array('url' => $paymentPipe->getPaymentPage() . '?PaymentID=' . $paymentPipe->getPaymentId(), 'data' => array('msg' => ''));
            }
        }

        // V primeru zaporedne transakcije Capture, ki sledi transakciji Authorization,
        // zgradimo Payment Message ter ga posljemo placilnemu strezniku CGw.
        if ($paymentPipe->getAction() == '5') {
            // Enolicen trgovcev ID transakcije preko katerega trgovec lahko v svojem sistemu poisce podatke
            // o nakupu.
            $paymentPipe->setPaymentId($_POST['payID']);

            // Preberemo podatke o narocilu iz baze.
            if ($nar->fetch($paymentPipe->getPaymentId()) != 0) {
                return array('url' => $paymentPipe->getErrorURL(), 'data' => array('msg' => 'Neuspesno+branje+podatkov+o+predhodni+transakciji+iz+baze.'));
            }

            // Iz baze narocil potrebujemo naslednja dva podatka TransactionID in TrackID.
            $paymentPipe->setTranID($nar->getTranID());
            $paymentPipe->setTrackID($nar->getTrackID());

            // Posljemo zahtevo po po CAPTURE transakciji.
            if ($paymentPipe->performPayment() != $paymentPipe->SUCCESS) {
                $nar->setResult('NOT CAPTURED');
                $msgStr = 'Zajem transakcije, paymentID = ' . $paymentPipe->getPaymentID() . ', ni izveden. (NOT CAPTURED)<b>' . $paymentPipe->getErrorMsg() . '</b>';
            } else {
                $nar->setResult('CAPTURED');
                $msgStr = 'Zajem transakcije, paymentID = ' . $paymentPipe->getPaymentID() . ', je uspesno izveden. (CAPTURED)';
            }

            // Capture transakcijo zapisemo v bazo narocil kot novo transakcijo. Vecino podatkov vzamemo iz pripadajoce
            // authorization transakcije. Nov je datum transakcije, trgovcev trackID transakcije in vsota pri Capture, ki je
            // lahko manjsa kot je bila pri Authorization.
            $nar->setTranDate(date('d.m.Y H:i:s'));
            $nar->setTrackId(md5(uniqid()));
            $nar->setAmount($paymentPipe->getAmt());
            $nar->setCustomerIP($this->getRealIpAddr());

            if ($nar->commit() != 0) {
                // Izvede se preusmeritev na trgovcev URL o obvestilu o napaki.
                return array('url' => $paymentPipe->getErrorURL(), 'data' => array('msg' => 'Transakcija+se+ni+zapisala+v+bazo.'));
            }

            return array('url' => $paymentPipe->getErrorURL(), 'data' => array('msg' => $msgStr));
        }

        return 'bankart/bankart/redirect';
    }

    protected function getRealIpAddr() {
        //check ip from share internet
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
            //to check ip is pass from proxy
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}

?>