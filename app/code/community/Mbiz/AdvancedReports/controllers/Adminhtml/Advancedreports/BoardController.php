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
 * Adminhtml_Advancedreports_Board Controller
 * @package Mbiz_AdvancedReports
 */
class Mbiz_AdvancedReports_Adminhtml_Advancedreports_BoardController extends Mage_Adminhtml_Controller_Action
{

    /**
     * The current board
     * @var Mbiz_AdvancedReports_Model_Board
     */
    protected $_board = null;

    /**
     * {@inheritdoc}
     */
    public function preDispatch()
    {
        // Avoid checks if denied or noroute actions
        if (in_array($this->getRequest()->getActionName(), array('noroute', 'denied'))) {
            return parent::preDispatch();
        }

        // Check the existence of the board and set it in registry
        if ($boardId = $this->getRequest()->getParam('board', false)) {
            if ($this->_board = $this->_getConfig()->getBoard($boardId)) {
                Mage::register('current_board', $this->_board);
                return parent::preDispatch();
            }
        }

        // Nothing to do, board not found
        parent::preDispatch();
        $this->_forward('noroute');
        $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        return $this;
    }

    /**
     * Display a board
     */
    public function viewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Retrieve the form of a request, via ajax
     */
    public function requestFormAction()
    {
        // Get the current request
        $request = $this->_board->getRequest($this->getRequest()->getParam('request_id', false));
        if (!$request) {
            return $this->_forward('noroute');
        }
        Mage::register('current_board_request', $request);

        // Form
        /* @var $form Mbiz_AdvancedReports_Block_Adminhtml_Board_Request_Fields */
        $form = $this->getLayout()->createBlock('mbiz_advancedreports/adminhtml_board_request_fields');
        $form->setBoardRequest($request);

        $this->getResponse()->setBody($form->toHtml());
    }

    /**
     * Validate the request
     */
    public function validateAction()
    {
        // Get the current request
        /* @var $request Mbiz_AdvancedReports_Model_Request_Interface */
        $request = $this->_board->getRequest($this->getRequest()->getParam('request_id', false));
        if (!$request) {
            return $this->_forward('noroute');
        }

        /* @var $data array */
        $data = $this->getRequest()->getPost();

        // Init request
        $request->init(new Varien_Object($data));

        // Init response
        $response = new Varien_Object;
        $response->setError(0);

        // Validate the data
        $errors = $request->validate();
        if (count($errors)) {
            foreach ($errors as $error) {
                $this->_getSession()->addError($error);
            }
            $response->setError(1);
        }

        if ($response->getError()) {
            $this->_initLayoutMessages('adminhtml/session');
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Post the form and display or return the results (as csv)
     */
    public function resultAction()
    {
        // Get the current request
        $request = $this->_board->getRequest($this->getRequest()->getParam('request_id', false));
        if (!$request) {
            return $this->_forward('noroute');
        }
        Mage::register('current_board_request', $request);

        // Validate (to be sure)
        $data = $this->getRequest()->getQuery();
        $errors = $request->init(new Varien_Object($data))->validate();
        if (count($errors)) {
            foreach ($errors as $error) {
                $this->_getSession()->addError($error);
            }
            return $this->_redirectReferer();
        }

        /*
         * Export as CSV
         */
        if ((bool) $data['export_flag']) {

            // Denied?
            if (!$request->canExport()) {
                return $this->_forward('denied');
            }

            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', 'text/csv', true)
                //->setHeader('Content-Length', null, true)
                ->setHeader('Content-Disposition', 'attachment; filename="report.csv"', true)
                ->setHeader('Last-Modified', date('r'), true)
                ->sendHeaders()
            ;

            $request->processExportAndDisplay();
            exit;
        }

        /*
         * Display grid
         */
        // Denied?
        if (!$request->canViewResults()) {
            return $this->_forward('denied');
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Retrieve the config
     * @return Mbiz_AdvancedReports_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('mbiz_advancedreports/config');
    }

    /**
     * Is allowed?
     * @return bool
     */
    protected function _isAllowed()
    {
        if ($board = Mage::registry('current_board')) {
            return Mage::getSingleton('admin/session')->isAllowed('report/advancedreports_board_' . $board->getId());
        }

        return false;
    }

}