<?php
/**
 * This file is part of Mbiz_AdvancedReports for Magento.
 *
 * @license GPL-3.0+
 * @author Jacques Bodin-Hullin <j.bodinhullin@monsieurbiz.com> <@jacquesbh>
 * @category Mbiz
 * @package Mbiz_AdvancedReports
 * @copyright Copyright (c) 2015 Monsieur Biz (http://monsieurbiz.com)
 */

/**
 * Adminhtml_Board_Request_Fields Block
 * @package Mbiz_AdvancedReports
 *
 * @method Mbiz_AdvancedReports_Block_Adminhtml_Board_Request_Fields setBoardRequest(Mbiz_AdvancedReports_Model_Request_Interface $request) Set the request
 * @method Mbiz_AdvancedReports_Model_Request_Interface getBoardRequest() Retrieve the request
 */
class Mbiz_AdvancedReports_Block_Adminhtml_Board_Request_Fields extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'request_fields_form',
            'action'    => $this->getUrl('*/*/result'),
            'method'    => 'get',
            'enctype'   => 'multipart/form-data'
        ));

        $request = $this->getBoardRequest();

        $fieldset = $form->addFieldset('request-selector', array(
            'legend' => $request->getLabel(),
        ));

        // Request and board identifiers
        $fieldset->addField('request_id', 'hidden', array(
            'name'  => 'request_id',
            'value' => $request->getIdentifier()
        ));
        $fieldset->addField('board', 'hidden', array(
            'name'  => 'board',
            'value' => $request->getBoard()->getId()
        ));

        // Add request fields
        $fields = $request->getFields();
        foreach ($fields as $fieldName => $field) {
            $this->_addField($fieldset, $fieldName, $field);
        }

        // Add export flag field
        $fieldset->addField('export_flag', 'hidden', array(
            'name'  => 'export_flag',
            'value' => 0,
        ));

        // Add button to export/view the results
        $buttonsHtml = '';
        if ($request->canExport()) {
            $buttonsHtml .= $this->getLayout()->createBlock('adminhtml/widget_button', 'export_button', array(
                'label' => $this->__("Export in CSV"),
                'type' => 'button',
                'onclick' => "Board.export(); return false;",
            ))->toHtml();
        }
        if ($request->canViewResults()) {
            $buttonsHtml .= ' ' . $this->getLayout()->createBlock('adminhtml/widget_button', 'submit_button', array(
                'label' => $this->__("View results"),
                'type' => 'button',
                'onclick' => "Board.submit(); return false;",
            ))->toHtml();
        }
        $fieldset->addField('buttons', 'note', array(
            'text' => $buttonsHtml,
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add a field to the specified fieldset
     * @param Varien_Data_Form_Element_Fieldset $fieldset The fieldset
     * @param string $fieldName Name of the field
     * @param array $field The field to add
     */
    protected function _addField(Varien_Data_Form_Element_Fieldset $fieldset, $fieldName, array $field)
    {
        switch ($field['type']) {
            case 'date':
                $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                $field['field'] += array(
                    'image'  => $this->getSkinUrl('images/grid-cal.gif'),
                    'format' => $dateFormatIso,
                );
                break;
            default:
                break;
        }

        $fieldset->addField($fieldName, $field['type'], $field['field']);
    }

}