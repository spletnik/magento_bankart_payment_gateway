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
class Spletnisistemi_Bankart_Model_Bankart_SecureResource {
    protected $strResourcePath;
    protected $strAlias;
    protected $termID;
    protected $password;
    protected $passwordHash;
    protected $port;
    protected $context;
    protected $webAddress;
    protected $error;
    protected $bDebugOn;
    protected $debugMsg;
    protected $bSecureResourceDecoded;

    // Kljuc za odkriptiranje podatkov za dostop do placilnega serverja.
    protected $key = array(1416130419, 1696626536, 1864396914, 1868981619, 1931506799, 543580534, 1869967904,
        1718773093, 1685024032, 1634624544, 2036692000, 1684369522, 1701013857, 1952784481, 1734964321,
        1953066862, 543257189, 544040302, 544696431, 544694638, 1948283489, 1768824951, 1769236591,
        1970544756, 1752526436, 1701978209, 1852055660, 1768384628, 1852403303);

    function __construct() {
        $this->strResourcePath = "";
        $this->strAlias = "";
        $this->termID = "";
        $this->password = "";
        $this->passwordHash = "";
        $this->port = "";
        $this->context = "";
        $this->webAddress = "";
        $this->error = "";
        $this->bDebugOn = true;
    }

    // Metoda prebere podatke iz resource datoteke.
    function getSecureSettings() {
        if ($this->strResourcePath == "") {
            $this->error = "No resource path specified.";
            return false;
        }

        if (!$this->createReadableZip()) {
            return false;
        }

        $strData = $this->readZip($this->getAlias() . ".xml");
        $this->destroyUnSecureResource();
        if ($strData == "") {
            return false;
        }

        return $this->parseSettings($strData);
    }

    // Zbrise zacasno datoteko, ki je nastala pri dekripciji resource datoteke.

    function createReadableZip() {
        if ($this->bSecureResourceDecoded)
            return true;

        if ($this->bDebugOn)
            $this->addDebugMessage("Locating Secure Resource.");

        // 1. binarno preberemo podatke
        $fileInName = $this->getResourcePath() . "resource.cgn";
        $zip = fopen($fileInName, 'rb');
        if (!is_resource($zip)) {
            $this->error = "Unable to open resource file (" . $fileInName . ")!";
            return false;
        }
        // - byte po byte preberemo vsebino datoteke v niz $stringData
        $bin = fread($zip, 1);    // preberemo 1 stevilo
        $stringData = '';
        while (!feof($zip)) {
            $stringData = $stringData . $bin;
            $bin = fread($zip, 1);   // preberemo 1 stevilo
        }
        $numberOfBytes = strlen($stringData);
        // - odpakiramo znake v cela stevila
        $data = $this->odpakiraj($stringData);

        // 2. Naredimo XOR nad podatki
        $xorData = $this->simpleXOR($data);

        // 3. binarno zapisemo podatke, byte po byte
        $fileOutName = $this->getResourcePath() . "resource.cgz";
        $rzip = fopen($fileOutName, 'wb');
        if (!is_resource($rzip)) {
            $this->error = "Unable to open temporaray resource file (" . $fileOutName . ")! Check permissions for writting files.";
            return false;
        }
        // - zapakiramo cela stevila v znake
        $stringData = $this->zapakiraj($xorData);
        for ($i = 0; $i < $numberOfBytes; $i++) {
            if (fwrite($rzip, $stringData[$i], 1) === FALSE)
                echo "Cannot write to file ($fileOutName)";
        }
        fclose($rzip);

        $this->bSecureResourceDecoded = true;
        return true;
    }

    // Iz resource datoteke zgradi zacasno zip datoteko.

    function addDebugMessage($val) {
        if ($this->bDebugOn)
            $this->debugMsg .= $val;
    }

    function getResourcePath() {
        return $this->strResourcePath;
    }

    function odpakiraj($stringData) {
        // Dodamo prazne znake, da stevilo bayteov doseze veckratnik stevila 4, seveda ce to ze ni.
        while (strlen($stringData) % 4 != 0)
            $stringData .= ' ';

        // Po 4 znake skupaj odpakiramo v stevilo.
        for ($i = 0, $j = 0; $i < strlen($stringData); $i = $i + 4, $j++) {
            $y = unpack("Nx", substr($stringData, $i, 4)); // binarne podatke razpakiramo v tabelo; razporejeni so z "big endian order"
            $data[$j] = $y["x"];
        }
        return $data;
    }

    // Iz zacasne zip datoteke "izluscimo" vsebino datoteke v obliki enega niza.

    function simpleXOR($byteInput) {
        if ($this->bDebugOn)
            $this->addDebugMessage("Decoding Buffer.");

        $k = 0;
        for ($m = 0; $m < count($byteInput); $m++) {
            if ($k >= count($this->key))
                $k = 0;
            $result[$m] = $byteInput[$m] ^ $this->key[$k];
            $k++;
        }
        return $result;
    }

    // Niz pridobljen iz zacasne zip datoteke razparsamo.

    function zapakiraj($data) {
        $stringData = '';
        // Po 4 znake skupaj zapakiramo v stevilo.
        for ($i = 0; $i < count($data); $i++) {
            $bin = pack("N", $data[$i]); // iz tabele zapakiramo podatke nazaj v binarno obliko.
            $stringData = $stringData . $bin;
        }
        return $stringData;
    }

    // Nad binarnima podatkoma izvede xor funkcijo.

    function readZip($entryFileName) {
        $strData = "";
        $data = null;
        if ($this->getResourcePath() == null || $this->getResourcePath() == "") {
            $this->error = "Error Accessing Secure Resource. Resource Path not set.";
            return null;
        }
        if ($entryFileName == null || $entryFileName == "") {
            $this->error = "Error Accessing Secure Resource. Terminal Alias not set.";
            return null;
        }
        if ($this->bDebugOn)
            $this->addDebugMessage("Accessing Decoded Secure Resource.");

        $zipFile = zip_open($this->getResourcePath() . "resource.cgz");

        if (is_resource($zipFile)) {
            $zip_entry_exist = false;
            while (($zipEntry = zip_read($zipFile))) {
                // V zip datoteki iscemo XML datoteko z imenom trgovcevega terminala.
                if (zip_entry_name($zipEntry) === $entryFileName) {
                    $zip_entry_exist = true;
                    if (zip_entry_open($zipFile, $zipEntry)) {
                        if ($this->bDebugOn)
                            $this->addDebugMessage("Resource Entry Retrieved.");
                        $readStream = zip_entry_read($zipEntry);

                        // binarno preberemo podatke kot stevila
                        $data = unpack("N*", $readStream);
                        for ($i = 1; $i < count($data) + 1; $i++) {
                            $data1[$i - 1] = $data[$i];
                        }

                        // naredimo XOR nad podatki
                        $xorData = $this->simpleXOR($data1);

                        // celostevilske podatke zapisemo v binarni niz
                        $bin = null;
                        for ($i = 0; $i < count($xorData); $i++) {
                            $bin .= pack("N", $xorData[$i]);
                        }

                        // podatke iz binarnega niza pretvorimo v niz znakov
                        $decoded = unpack("C*", $bin);
                        $xmlString = "";
                        for ($i = 1; $i < count($decoded) + 1; $i++) {
                            $xmlString .= chr($decoded[$i]);
                        }
                        //echo $xmlString . "<br>";

                        // vrnem XML string
                        $strData = $xmlString;

                        if ($this->bDebugOn)
                            $this->addDebugMessage("Resource Entry Parsed.");

                        zip_entry_close($zipEntry);
                    }
                }
            }
            zip_close($zipFile);
        } else {
            $this->error = "Failed to read ZIP " . $this->getResourcePath() . "resource.cgz" . "\n";
            return null;
        }

        if ($zip_entry_exist == false) {
            $this->error = "The ZIP Entry " . $this->getAlias() . ".xml does not exist.";
            return null;
        }
        return $strData;
    }

    function getAlias() {
        return $this->strAlias;
    }

    function destroyUnSecureResource() {
        unlink($this->getResourcePath() . "resource.cgz");
        $this->bSecureResourceDecoded = false;
        if ($this->bDebugOn)
            $this->addDebugMessage("Decoded Resource Destroyed.");
    }

    function parseSettings($settings) {
        if ($this->bDebugOn)
            $this->addDebugMessage("Parsing Settings.");
        $begin = strpos($settings, "<id>") + strlen("<id>");
        $end = strpos($settings, "</id>");
        if ($begin === false || $end === false) {
            $this->error = "Error parsing internal settings file.";
            return false;
        }
        $this->setTermID(substr($settings, $begin, $end - $begin));

        $begin = strpos($settings, "<password>") + strlen("<password>");
        $end = strpos($settings, "</password>");
        if ($begin === false || $end === false) {
            $this->error = "Error parsing internal settings file.";
            return false;
        }
        $this->setPassword(substr($settings, $begin, $end - $begin));

        $begin = strpos($settings, "<passwordhash>") + strlen("<passwordhash>");
        $end = strpos($settings, "</passwordhash>");
        if ($begin === false || $end === false) {
            $this->error = "Error parsing internal settings file.";
            return false;
        }
        $this->setPasswordHash(substr($settings, $begin, $end - $begin));

        $begin = strpos($settings, "<webaddress>") + strlen("<webaddress>");
        $end = strpos($settings, "</webaddress>");
        if ($begin === false || $end === false) {
            $this->error = "Error parsing internal settings file.";
            return false;
        }
        $this->setWebAddress(substr($settings, $begin, $end - $begin));

        $begin = strpos($settings, "<port>") + strlen("<port>");
        $end = strpos($settings, "</port>");
        if ($begin === false || $end === false) {
            $this->error = "Error parsing internal settings file.";
            return false;
        }
        $this->setPort(substr($settings, $begin, $end - $begin));

        $begin = strpos($settings, "<context>") + strlen("<context>");
        $end = strpos($settings, "</context>");
        if ($begin === false || $end === false) {
            $this->error = "Error parsing internal settings file.";
            return false;
        }
        $this->setContext(substr($settings, $begin, $end - $begin));
        return true;
    }

    function isDebugOn() {
        return $this->bDebugOn;
    }

    function setDebugOn($val) {
        $this->bDebugOn = $val;
    }

    function getDebugMsg() {
        return $this->debugMsg . toString();
    }

    function setResourcePath($val) {
        $this->strResourcePath = $val;
    }

    function setAlias($val) {
        $this->strAlias = $val;
    }

    function getContext() {
        return $this->context;
    }

    function setContext($val) {
        $this->context = $val;
    }

    function getPassword() {
        return $this->password;
    }

    function setPassword($val) {
        $this->password = $val;
    }

    function getPasswordHash() {
        return $this->passwordHash;
    }

    function setPasswordHash($val) {
        $this->passwordHash = $val;
    }

    function getPort() {
        return $this->port;
    }

    function setPort($val) {
        $this->port = $val;
    }

    function getTermID() {
        return $this->termID;
    }

    function setTermID($val) {
        $this->termID = $val;
    }

    function getWebAddress() {
        return $this->webAddress;
    }

    function setWebAddress($val) {
        $this->webAddress = $val;
    }

    function getError() {
        return $this->error;
    }
}