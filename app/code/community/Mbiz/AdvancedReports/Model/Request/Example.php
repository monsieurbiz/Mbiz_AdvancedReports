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
 * Request_Example Model
 * @package Mbiz_AdvancedReports
 */
class Mbiz_AdvancedReports_Model_Request_Example
    extends Mbiz_AdvancedReports_Model_Request_Abstract
    implements Mbiz_AdvancedReports_Model_Request_Interface
{

    /**
     * Label of the request
     * @var string
     */
    protected $_label = 'Example';

    /**
     * Form fields
     * @var array|null
     */
    protected $_fields = null;

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        if (null === $this->_fields) {
            $this->_fields = $this->getPeriodFields(true) + array(
                'example' => array(
                    'type'  => 'select',
                    'field' => array(
                        'name'     => 'example',
                        'label'    => Mage::helper('mbiz_advancedreports')->__('Example?'),
                        'title'    => Mage::helper('mbiz_advancedreports')->__('Example?'),
                        'required' => false,
                        'options'  => [
                            ''        => Mage::helper('adminhtml')->__('--Please Select--'),
                            'example' => 'Example'
                        ]
                    ),
                ),
            );
        }

        return $this->_fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getGridLabel()
    {
        $helper = Mage::helper('mbiz_advancedreports');
        $data   = $this->getData('_data');
        $label  = array();

        // Dates
        $from  = Mage::app()->getLocale()->date($data['date_from']);
        $to    = Mage::app()->getLocale()->date($data['date_to']);
        $today = Mage::app()->getLocale()->date();

        // Generate
        $label[] = $helper->__("From %s", $from->toString(Zend_Date::DATE_MEDIUM));
        if ($from->compareDate($to) === 0) { // From and To are the same
            if ($from->compareDate($today) === 0) {
                $label = array($helper->__("Today"));
            } else {
                $label = array($from->toString(Zend_Date::DATE_MEDIUM));
            }
        } elseif ($to->compareDate($today) === 0) { // To is today
            $label[] = $helper->__("to today");
        } else {
            $label[] = $helper->__("to %s", $to->toString(Zend_Date::DATE_MEDIUM));
        }

        // Label with date(s)
        $label = $this->getLabel() . ' - ' . implode(" ", $label);

        return $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getGridColumns()
    {
        return array(
            'id' => array(
                'header'   => Mage::helper('mbiz_advancedreports')->__('ID'),
                'width'    => 1,
                'type'     => 'text',
                'index'    => 'id',
                'filter'   => false,
                'sortable' => false,
            ),
            'name' => array(
                'header'   => Mage::helper('mbiz_advancedreports')->__('Name'),
                'type'     => 'text',
                'index'    => 'name',
                'filter'   => false,
                'sortable' => false,
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function prepareGridLayout(Mage_Adminhtml_Block_Widget_Grid $grid)
    {
        $grid->setDefaultDir('desc');
        $grid->setDefaultSort('qty');
        $grid->setPagerVisibility(false);
        $grid->setDefaultLimit(null);
        return $grid;
    }

    /**
     * {@inheritdoc}
     */
    public function processExportAndDisplay()
    {
        $headerPrinted = false;
        foreach ($this->getCollection() as $item) {
            if (!$headerPrinted) {
                $this->_printCsvLine(array_keys($item->getData()));
                $headerPrinted = true;
            }
            $this->_printCsvLine($item->getData());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        $coll = new Varien_Data_Collection;
        $coll->addItem(new Varien_Object(['id' => 1, 'name' => 'Foo']));
        $coll->addItem(new Varien_Object(['id' => 2, 'name' => 'Bar']));

        return $coll;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $errors = array();
        $data = $this->getRequest();

        // Are the dates valid?
        $errors += $this->_validatePeriod($data);

        // Keep the data after validation
        $this->setData('_data', $this->_transformData($data));

        return $errors;
    }

    /**
     * Transform the data
     * <p>By example by changing dates from string to Zend_Date object.
     * Or parsing text…</p>
     * @param Varien_Object $data
     * @return array The updated data
     */
    protected function _transformData(Varien_Object $data)
    {
        $this->_transformPeriod($data);
        return $data;
    }

}
