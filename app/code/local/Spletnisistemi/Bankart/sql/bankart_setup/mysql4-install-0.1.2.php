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

$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE `{$this->getTable('bankart_api_debug')}` (
    `debug_id` int(10) unsigned NOT NULL auto_increment,
    `paymentID` VARCHAR(45) NOT NULL,
    `currency` VARCHAR(10) NOT NULL,
    `amount` DOUBLE NOT NULL,
    `orderDetails` VARCHAR(150) NOT NULL,
    `trackID` VARCHAR(45) NOT NULL,
    `tranDate` VARCHAR(45) NOT NULL,
    `name` VARCHAR(45) NOT NULL,
    `addr1` VARCHAR(45) NOT NULL,
    `addr2` VARCHAR(45) NOT NULL,
    `addr3` VARCHAR(45) NOT NULL,
    `city` VARCHAR(45) NOT NULL,
    `state` VARCHAR(45) NOT NULL,
    `postalCode` INTEGER UNSIGNED NOT NULL,
    `result` VARCHAR(45) NOT NULL,
    `auth` VARCHAR(45) NOT NULL,
    `ref` VARCHAR(45) NOT NULL,
    `tranID` VARCHAR(45) NOT NULL,
    `postDate` VARCHAR(45) NOT NULL,
    `udf1` VARCHAR(45) NOT NULL,
    `udf2` VARCHAR(45) NOT NULL,
    `udf3` VARCHAR(45) NOT NULL,
    `udf4` VARCHAR(45) NOT NULL,
    `udf5` VARCHAR(45) NOT NULL,
    `responseCode` VARCHAR(45) NOT NULL,
    `errMsg` VARCHAR(10) NOT NULL,
    `errText` VARCHAR(150) NOT NULL,
    `customerIP` VARCHAR(16) NOT NULL,
    `debug_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`debug_id`),
  KEY `ix_paymentID` (`paymentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
$installer->endSetup();