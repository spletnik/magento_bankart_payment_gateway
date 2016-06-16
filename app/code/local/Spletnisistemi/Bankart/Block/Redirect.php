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
class Spletnisistemi_Bankart_Block_Redirect extends Mage_Core_Block_Abstract {
    protected function _toHtml() {
        $bankartModel = Mage::getModel('bankart/bankart');
        $form = new Varien_Data_Form();

        $form->setAction($bankartModel->getBankartUrl())
            ->setId('bankart')
            ->setName('bankart')
            ->setMethod('POST')
            ->setCharSet('utf-8')
            ->setUseContainer(true);

        // Add fields need for proper request
        $fieldset = $form->addFieldset('bankart', array('legend' => ''));
        $fieldset->addField('vrednost', 'hidden', array('name' => 'vrednost', 'title' => 'vrednost', 'label' => 'vrednost', 'required' => true));
        $fieldset->addField('vrstaTran', 'hidden', array('name' => 'vrstaTran', 'title' => 'vrstaTran', 'label' => 'vrstaTran', 'required' => true));
        $fieldset->addField('valuta', 'hidden', array('name' => 'valuta', 'title' => 'valuta', 'label' => 'valuta', 'required' => true));
        $fieldset->addField('vrstaJezika', 'hidden', array('name' => 'vrstaJezika', 'title' => 'vrstaJezika', 'label' => 'vrstaJezika', 'required' => true));
        $fieldset->addField('responseURL', 'hidden', array('name' => 'responseURL', 'title' => 'responseURL', 'label' => 'responseURL', 'required' => true));
        $fieldset->addField('errorURL', 'hidden', array('name' => 'errorURL', 'title' => 'errorURL', 'label' => 'errorURL', 'required' => true));
        $fieldset->addField('payID', 'hidden', array('name' => 'payID', 'title' => 'payID', 'label' => 'payID', 'required' => true));
        $fieldset->addField('resourcePath', 'hidden', array('name' => 'resourcePath', 'title' => 'resourcePath', 'label' => 'resourcePath', 'required' => true));
        $fieldset->addField('terminalAlias', 'hidden', array('name' => 'terminalAlias', 'title' => 'terminalAlias', 'label' => 'terminalAlias', 'required' => true));
        $fieldset->addField('usr_id', 'hidden', array('name' => 'usr_id', 'title' => 'usr_id', 'label' => 'usr_id', 'required' => true));
        $fieldset->addField('usr_ime', 'hidden', array('name' => 'usr_ime', 'title' => 'usr_ime', 'label' => 'usr_ime', 'required' => true));
        $fieldset->addField('usr_priimek', 'hidden', array('name' => 'usr_priimek', 'title' => 'usr_priimek', 'label' => 'usr_priimek', 'required' => true));
        $fieldset->addField('usr_eposta', 'hidden', array('name' => 'usr_eposta', 'title' => 'usr_eposta', 'label' => 'usr_eposta', 'required' => true));
        $fieldset->addField('usr_ulica', 'hidden', array('name' => 'usr_ulica', 'title' => 'usr_ulica', 'label' => 'usr_ulica', 'required' => true));
        $fieldset->addField('usr_mesto', 'hidden', array('name' => 'usr_mesto', 'title' => 'usr_mesto', 'label' => 'usr_mesto', 'required' => true));

        $form->setUseContainer(true);
        $form->setValues($bankartModel->generateRequestData());

        $html = '';
        $html .= $this->__('SPLETNISISTEMI_BANKART_PAYMENT_START');
        $html .= $form->toHtml();
        $html .= '<script type="text/javascript">document.getElementById("bankart").submit();</script>';
        $html .= '<button onclick="document.getElementById("bankart").submit();" >' . $this->__("naprej") . '</button>';

        return $html;
    }
}