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

try {

    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = $this;
    $installer->startSetup();

    // Create indexes ;)
    $tableOrderPayment = $installer->getTable('sales/order_payment');
    $installer->getConnection()->addIndex(
        $tableOrderPayment,
        $installer->getIdxName($tableOrderPayment, 'method'),
        'method'
    );

    $installer->endSetup();

} catch (Exception $e) {
    // Silence is golden
}
