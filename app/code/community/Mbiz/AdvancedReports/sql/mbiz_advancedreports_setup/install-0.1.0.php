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

try {

    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = $this;
    $installer->startSetup();

    /*
     * Create missing indexes
     */

    // on invoice item
    $tableInvoiceItem = $installer->getTable('sales/invoice_item');
    $installer->getConnection()->addIndex(
        $tableInvoiceItem,
        $installer->getIdxName($tableInvoiceItem, 'product_id'),
        'product_id'
    );
    $installer->getConnection()->addIndex(
        $tableInvoiceItem,
        $installer->getIdxName($tableInvoiceItem, 'qty'),
        'qty'
    );

    // on order payment
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
