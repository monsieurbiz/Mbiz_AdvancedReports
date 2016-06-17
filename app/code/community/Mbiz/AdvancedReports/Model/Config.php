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
 * Config Model
 * @package Mbiz_AdvancedReports
 */
class Mbiz_AdvancedReports_Model_Config
{

    /**
     * Config path to the boards
     * @const string
     */
    const CONFIG_PATH_BOARDS = 'mbiz_advancedreports/boards';

    /**
     * Config path to the requests
     * @const string
     */
    const CONFIG_PATH_REQUESTS = 'mbiz_advancedreports/requests';

    /**
     * The boards
     * @var array
     */
    protected $_boards = array();

    /**
     * The requests
     * @var array
     */
    protected $_requests = array();

    /**
     * Check if board exists
     * @param string $boardId
     * @return bool TRUE if exists
     */
    public function boardExists($boardId)
    {
        $boardNode = Mage::app()->getConfig()->getNode(self::CONFIG_PATH_BOARDS . '/' . $boardId);
        return !(false === $boardNode);
    }

    /**
     * Retrieve a board
     * @param string $boardId Board's identifier
     * @return Mbiz_AdvancedReports_Model_Board|FALSE
     */
    public function getBoard($boardId)
    {
        if (!isset($this->_boards[$boardId])) {
            if (!$this->boardExists($boardId)) {
                return false;
            }
            $board = Mage::getModel('mbiz_advancedreports/board');
            $boardNode = Mage::app()->getConfig()->getNode(self::CONFIG_PATH_BOARDS . '/' . $boardId);
            $board->init($boardNode);
            $this->_boards[$boardId] = $board;
        }
        return $this->_boards[$boardId];
    }

    /**
     * Check if request exists
     * @param string $requestId
     * @return bool TRUE if exists
     */
    public function requestExists($requestId)
    {
        $requestNode = Mage::app()->getConfig()->getNode(self::CONFIG_PATH_REQUESTS . '/' . $requestId);
        return !(false === $requestNode);
    }

    /**
     * Retrieve a request model
     * @param string $requestId Request's identifier
     * <p>If the request is a config element we don't retrieve it in the configuration.</p>
     * @return Mbiz_AdvancedReports_Model_Request_Interface|FALSE
     */
    public function getRequestModel($requestId)
    {
        if (!isset($this->_requests[$requestId])) {
            if (!$this->requestExists($requestId)) {
                return false;
            }
            $requestNode = Mage::app()->getConfig()->getNode(self::CONFIG_PATH_REQUESTS . '/' . $requestId);
            $request = Mage::getModel($requestNode->getClassName());
            $request->setIdentifier($requestId);
            $this->_requests[$requestId] = $request;
        }
        return $this->_requests[$requestId];
    }

// Mediapart Tag NEW_METHOD

}