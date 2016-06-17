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
 * Adminhtml_Board_Request_Result Block
 * @package Mbiz_AdvancedReports
 */
class Mbiz_AdvancedReports_Block_Adminhtml_Board_Request_Result extends Mage_Adminhtml_Block_Widget_Grid_Container
{

// Mediapart Tag NEW_CONST

// Mediapart Tag NEW_VAR

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        // Init the grid
        $this->_blockGroup = 'mbiz_advancedreports';
        $this->_controller = 'adminhtml_board_request_result';

        // The request and the board
        /* @var $request Mbiz_AdvancedReports_Model_Request_Interface */
        $request = Mage::registry('current_board_request');
        /* @var $board Mbiz_AdvancedReports_Model_Board */
        $board = Mage::registry('current_board');

        /*
         * Update the buttons
         */
        // Remove add
        $this->_removeButton('add');

        // Add back
        $this->_addButton('back', array(
            'label'     => Mage::helper('mbiz_advancedreports')->__("Back to the board"),
            'onclick'   => 'setLocation(\'' . $board->getUrl($request) .'\')',
            'class'     => 'back',
        ));

        // The export button
        if ($request->canExport()) {
            $query = Mage::app()->getRequest()->getQuery();
            $query['export_flag'] = 1;
            $exportUrl = $this->getUrl('*/*/*', array(
                '_query' => $query
            ));
            $this->_addButton('export', array(
                'label'     => Mage::helper('mbiz_advancedreports')->__("Complete export in CSV"),
                'onclick'   => 'setLocation(\'' . $exportUrl .'\')',
                'class'     => 'success',
            ));
        }

        /*
         * Title
         */
        $this->_headerText = sprintf('%s - %s', Mage::helper('mbiz_advancedreports')->__($board->getLabel()), $request->getGridLabel());

        return parent::_prepareLayout();
    }

// Mediapart Tag NEW_METHOD

}