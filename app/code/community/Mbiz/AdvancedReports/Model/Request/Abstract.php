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
 * Request_Abstract Model
 * @package Mbiz_AdvancedReports
 *
 * @method Mbiz_AdvancedReports_Model_Request_Abstract setRequest(Varien_Object $data) Set the requested data
 * @method Varien_Object getRequest() Get the requested data
 */
abstract class Mbiz_AdvancedReports_Model_Request_Abstract
    extends Mage_Core_Model_Abstract
    implements Mbiz_AdvancedReports_Model_Request_Interface
{

    /**
     * Label of the request
     * @var string
     */
    protected $_label = 'My Request';

    /**
     * Export Filename
     * @var string
     */
    protected $_exportFilename = 'report.csv';

    /**
     * Export MIME Type
     * @var string
     */
    protected $_exportMimeType = 'text/csv';

    /**
     * @inheritDoc
     */
    public function init(Varien_Object $data)
    {
        $this->setRequest($data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return Mage::helper('core')->__($this->_label);
    }

    /**
     * @inheritDoc
     */
    public function getExportFilename()
    {
        return $this->_exportFilename;
    }

    /**
     * @inheritDoc
     */
    public function getExportMimeType()
    {
        return $this->_exportMimeType;
    }

    /**
     * @inheritDoc
     */
    public function getGridLabel()
    {
        return $this->getLabel();
    }

    /**
     * Retrieve the dates as string according to the dates data
     * @return string
     */
    public function getDatesLabel()
    {
        $data   = $this->getData('_data');
        $label  = [];
        list($from, $to, $today) = $this->_getDates();

        /* @var $helper Mbiz_AdvancedReports_Helper_Data */
        $helper = Mage::helper('mbiz_advancedreports');

        // Generate
        $label[] = $helper->__("From %s", $from->toString(Zend_Date::DATE_MEDIUM));
        if ($from->compareDate($to) === 0) { // From and To are the same
            if ($from->compareDate($today) === 0) {
                $label = [$helper->__("Today")];
            } else {
                $label = [$from->toString(Zend_Date::DATE_MEDIUM)];
            }
        } elseif ($to->compareDate($today) === 0) { // To is today
            $label[] = $helper->__("to today");
        } else {
            $label[] = $helper->__("to %s", $to->toString(Zend_Date::DATE_MEDIUM));
        }

        return implode(' ', $label);
    }

    /**
     * @inheritDoc
     */
    public function getGridColumns()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function prepareGridLayout(Mage_Adminhtml_Block_Widget_Grid $grid)
    {
        return $grid;
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier($id)
    {
        // Call magic
        return parent::setIdentifier($id);
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        // Call magic
        return parent::getIdentifier();
    }

    /**
     * @inheritDoc
     */
    public function canExport()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canViewResults()
    {
        return true;
    }

    /**
     * Retrieve period fields
     * @return array
     */
    public function getPeriodFields($required = false)
    {
        return [
            'date_from' => [
                'type'  => 'date',
                'field' => [
                    'name'     => 'date_from',
                    'label'    => Mage::helper('mbiz_advancedreports')->__('From'),
                    'title'    => Mage::helper('mbiz_advancedreports')->__('From'),
                    'required' => (bool) $required,
                ],
            ],
            'date_to'   => [
                'type'  => 'date',
                'field' => [
                    'name'     => 'date_to',
                    'label'    => Mage::helper('mbiz_advancedreports')->__('To'),
                    'title'    => Mage::helper('mbiz_advancedreports')->__('To'),
                    'required' => (bool) $required,
                ],
            ],
        ];
    }

    /**
     * Get BOM for Excel, LibreOffice and co
     * <p>Used to open CSV files with ease.</p>
     * @return string
     */
    protected function _getBom()
    {
        return "\xEF\xBB\xBF";
    }

    /**
     * Transform period
     * @param Varien_Object $request The request to transform
     * @return Varien_Object
     */
    protected function _transformPeriod(Varien_Object $request)
    {
        return $this->_filterDates($request, [
            'date_from',
            'date_to',
        ]);
    }

    /**
     * Validate period
     * @param Varien_Object $request The requested data
     * @return array
     */
    protected function _validatePeriod(Varien_Object $request)
    {
        $error    = false;
        $request  = $this->_transformPeriod($request);

        $from     = Mage::app()->getLocale()->date($request->getDateFrom());
        $to       = Mage::app()->getLocale()->date($request->getDataTo());
        $tomorrow = Mage::app()->getLocale()->date()->addDay(1);

        // Is From after tomorrow? It's bad :/
        if ($from->compareDate($tomorrow) !== -1) {
            $error = true;
        }

        // Is To after tomorrow? It's bad :/
        elseif ($to->compareDate($tomorrow) !== -1) {
            $error = true;
        }

        // If From before To? It's bad :/
        elseif ($from->compareDate($to) === 1) {
            $error = true;
        }

        $errors = [];
        if ($error) {
            $errors['bad_period'] = Mage::helper('mbiz_advancedreports')->__("Period has to be in the past, today can be included. It has to be a correct period because we can't travel time. Of course I'm not Marty McFly.");
        }

        return $errors;
    }

    /**
     * Convert dates in array from localized to internal format
     * @param Varien_Object $request Request to filter
     * @param array $dateFields
     * @return array
     */
    protected function _filterDates(Varien_Object $request, array $dateFields)
    {
        if (empty($dateFields)) {
            return $request;
        }

        // Already filtered?
        if ($request->getData('_filter_dates_already_applied')) {
            return $request;
        }

        $filterInput    = new Zend_Filter_LocalizedToNormalized([
            'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ]);
        $filterInternal = new Zend_Filter_NormalizedToLocalized([
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ]);

        foreach ($dateFields as $dateField) {
            if ($request->getData($dateField)) {
                $request->setData($dateField, $filterInput->filter($request->getData($dateField)));
                $request->setData($dateField, $filterInternal->filter($request->getData($dateField)));
            }
        }

        // Avoid multiple process
        $request->setData('_filter_dates_already_applied', true);

        return $request;
    }

    /**
     * Retrieve dates in Zend_Date
     * @param bool $gmt Use GMT instead of local time
     * @return array[Zend_Date $from, Zend_Date $to, Zend_Date $today]
     */
    protected function _getDates($gmt = true)
    {
        $request = $this->getRequest();

        /* @var $locale Mage_Core_Model_Locale */
        $locale = Mage::app()->getLocale();

        // Dates
        $from  = $locale->date($request->getDateFrom(), null, null, !$gmt);
        $to    = $locale->date($request->getDateTo(), null, null, !$gmt);
        $today = $locale->date(null, null, null, !$gmt);
        
        return [$from, $to, $today];
    }

    /**
     * Print a csv line
     * @param array $line The line to display
     */
    protected function _printCsvLine(array $line)
    {
        $fp = fopen('php://output', 'a');
        fputcsv($fp, $line, ';', '"');
        fclose($fp);
    }

    /**
     * Retrieve the read adapter
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getReadAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }

    /**
     * Retrieve table name
     * @param string $modelEntity
     * @return string
     */
    protected function _getTableName($modelEntity)
    {
        /* @var $res Mage_Core_Model_Resource */
        $res = Mage::getSingleton('core/resource');

        return $res->getTableName($modelEntity);
    }

}
