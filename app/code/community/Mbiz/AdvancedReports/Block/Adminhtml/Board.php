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
 * Adminhtml_Board Block
 * @package Mbiz_AdvancedReports
 */
class Mbiz_AdvancedReports_Block_Adminhtml_Board extends Mage_Adminhtml_Block_Template
{

    /**
     * Retrieve the current board's label
     * @return string
     */
    public function getBoardLabel()
    {
        return Mage::helper('mbiz_advancedreports')->__($this->getCurrentBoard()->getLabel());
    }

    /**
     * Retrieve the board's ID
     * @return string
     */
    public function getBoardId()
    {
        return $this->getCurrentBoard()->getId();
    }

    /**
     * Retrieve the current board
     * @return Mbiz_AdvancedReports_Model_Board
     */
    public function getCurrentBoard()
    {
        return Mage::registry('current_board');
    }

}