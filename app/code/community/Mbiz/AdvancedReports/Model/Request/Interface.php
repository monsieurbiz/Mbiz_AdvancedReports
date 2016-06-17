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
 * Request_Interface Model
 * @package Mbiz_AdvancedReports
 */
interface Mbiz_AdvancedReports_Model_Request_Interface
{

    /**
     * Init the request
     * @param Varien_Object $data The request's data
     * @return Mbiz_AdvancedReports_Model_Request_Interface
     */
    public function init(Varien_Object $data);

    /**
     * Retrieve the label of the request
     * @return string
     */
    public function getLabel();

    /**
     * Retrieve the label of the request in a grid
     * @return string
     */
    public function getGridLabel();

    /**
     * Retrieve the grid's columns
     * @return array
     */
    public function getGridColumns();

    /**
     * Prepare the layout of the grid
     * @param Mage_Adminhtml_Block_Widget_Grid $grid
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    public function prepareGridLayout(Mage_Adminhtml_Block_Widget_Grid $grid);

    /**
     * Retrieve the fields of the request
     * @return array
     */
    public function getFields();

    /**
     * Set the request identifier
     * @param string $id
     * @return Mbiz_AdvancedReports_Model_Request_Interface
     */
    public function setIdentifier($id);

    /**
     * Retrieve the request identifier
     * @return string
     */
    public function getIdentifier();

    /**
     * Validate the requested data
     * @return array The error messages. Empty array if no error.
     */
    public function validate();

    /**
     * Process the export (CSV format)
     * <p>This method display the result as CSV format.</p>
     * @return void
     */
    public function processExportAndDisplay();

    /**
     * Retrieve the results as collection
     * @return Varien_Data_Collection
     */
    public function getCollection();

    /**
     * Can export to csv?
     * @return bool
     */
    public function canExport();

    /**
     * Can view results as grid?
     * @return bool
     */
    public function canViewResults();

}