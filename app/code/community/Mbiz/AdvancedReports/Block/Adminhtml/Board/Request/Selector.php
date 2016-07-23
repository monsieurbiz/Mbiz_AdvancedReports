<?php
/**
 * This file is part of Mbiz_AdvancedReports for Magento.
 *
 * @license MIT
 * @author Jacques Bodin-Hullin <j.bodinhullin@monsieurbiz.com> <@jacquesbh>
 * @category Mbiz
 * @package Mbiz_AdvancedReports
 * @copyright Copyright (c) 2015 Monsieur Biz (http://monsieurbiz.com)
 */

/**
 * Adminhtml_Board_Request_Selector Block
 * @package Mbiz_AdvancedReports
 */
class Mbiz_AdvancedReports_Block_Adminhtml_Board_Request_Selector extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'request_form',
            'action'    => '#',
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('request-selector', array(
            'legend' => Mage::helper('mbiz_advancedreports')->__('Request')
        ));

        // Request field
        $fieldset->addField('request_id', 'select', array(
            'name' => 'request_id',
            'label' => Mage::helper('mbiz_advancedreports')->__('Request'),
            'title' => Mage::helper('mbiz_advancedreports')->__('Request'),
            'options' => array('' => Mage::helper('adminhtml')->__("--Please Select--")) + $this->_getRequestAsArray(),
            'required' => false,
            'class' => 'js-no-autocomplete'
        ));

        $form->setUseContainer(false);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve the current board's requests
     * @return array
     */
    protected function _getRequestAsArray()
    {
        $board = Mage::registry('current_board');
        $requests = array();
        foreach ($board->getRequests() as $requestId => $request) {
            $requests[$requestId] = $request->getLabel();
        }
        return $requests;
    }

}