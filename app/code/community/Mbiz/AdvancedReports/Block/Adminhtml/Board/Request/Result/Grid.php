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
 * Adminhtml_Board_Request_Result_Grid Block
 * @package Mbiz_AdvancedReports
 */
class Mbiz_AdvancedReports_Block_Adminhtml_Board_Request_Result_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

// Mediapart Tag NEW_CONST

// Mediapart Tag NEW_VAR

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /* @var $request Mbiz_AdvancedReports_Model_Request_Interface */
        $request = Mage::registry('current_board_request');
        return $request->prepareGridLayout($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        /* @var $request Mbiz_AdvancedReports_Model_Request_Interface */
        $request = Mage::registry('current_board_request');
        foreach ($request->getGridColumns() as $code => $column) {
            $this->addColumn($code, $column);
        }

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        /* @var $request Mbiz_AdvancedReports_Model_Request_Interface */
        $request = Mage::registry('current_board_request');
        $collection = $request->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

// Mediapart Tag NEW_METHOD

}