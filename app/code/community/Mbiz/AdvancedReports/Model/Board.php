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
 * Board Model
 * @package Mbiz_AdvancedReports
 *
 * @method Mbiz_AdvancedReports_Model_Board setLabel(string $label) Set the board's label
 * @method string getLabel() Retrieve the label
 * @method Mbiz_AdvancedReports_Model_Board setId(string $id) Set the board's ID
 * @method string getId() Retrieve the ID
 * @method Mbiz_AdvancedReports_Model_Board setRequestIds(array $ids) Set the requests identifiers
 * @method array getRequestIds() Retrieve the request identifiers
 */
class Mbiz_AdvancedReports_Model_Board extends Varien_Object
{

    /**
     * The associated requests
     * @var array
     */
    protected $_requests = null;

    /**
     * Init a board with the config
     * @param Mage_Core_Model_Config_Element $boardNode The config node
     * @return Mbiz_AdvancedReports_Model_Board
     */
    public function init(Mage_Core_Model_Config_Element $boardNode)
    {
        // Get the request identifiers
        $requestIds = array();
        foreach ($boardNode->requests->children() as $requestNode) {
            $requestIds[] = $requestNode->getName();
        }

        // Get translated label
        if ($helperName = $boardNode->getAttribute('helper')) {
            $label = Mage::helper((string) $helperName)->__((string) $boardNode->label);
        } else {
            $label = (string) $boardNode->label;
        }

        // init
        $this
            ->setId($boardNode->getName())
            ->setLabel($label)
            ->setRequestIds($requestIds)
        ;

        return $this;
    }

    /**
     * Retrieve the associated requests
     * @return array
     */
    public function getRequests()
    {
        if (null === $this->_requests) {
            // Load the requests model
            $requestIds = $this->getRequestIds();
            $this->_requests = array();
            foreach ($requestIds as $requestId) {
                $request = Mage::getSingleton('mbiz_advancedreports/config')->getRequestModel($requestId);
                if ($request === false) {
                    Mage::throwException(sprintf(
                        "The request '%s' doesn't exist for the board '%s'.",
                        $requestId,
                        $this->getId()
                    ));
                }
                $this->_requests[$requestId] = $request->setBoard($this);
            }
        }
        return $this->_requests;
    }

    /**
     * Get a request (of the board)
     * @param string $requestId The request's ID
     * @return FALSE|Mbiz_AdvancedReports_Model_Request_Interface
     */
    public function getRequest($requestId)
    {
        $requests = $this->getRequests();
        if (isset($requests[$requestId])) {
            return $requests[$requestId];
        }
        return false;
    }

    /**
     * Retrieve the board URL
     * <p>Use only in admin…</p>
     * @param Mbiz_AdvancedReports_Model_Request_Interface $request The request if it exists
     * @return string
     */
    public function getUrl(Mbiz_AdvancedReports_Model_Request_Interface $request = null)
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/advancedreports_board/view', array(
            'board' => $this->getId(),
            '_query' => [
                'request' => $request !== null ? $request->getIdentifier() : null,
            ]
        ));
    }

}