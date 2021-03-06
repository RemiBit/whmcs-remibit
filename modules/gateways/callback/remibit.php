<?php
/**
 * WHMCS Sample Payment Callback File
 *
 * This sample file demonstrates how a payment gateway callback should be
 * handled within WHMCS.
 *
 * It demonstrates verifying that the payment gateway module is active,
 * validating an Invoice ID, checking for the existence of a Transaction ID,
 * Logging the Transaction for debugging and Adding Payment to an Invoice.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/callbacks/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);

// Die if module is not active.
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

// Retrieve data returned in payment gateway callback
// Varies per payment gateway
$hashData = implode('^', [
    $_POST['x_trans_id'],
    $_POST['x_test_request'],
    $_POST['x_response_code'],
    $_POST['x_auth_code'],
    $_POST['x_cvv2_resp_code'],
    $_POST['x_cavv_response'],
    $_POST['x_avs_code'],
    $_POST['x_method'],
    $_POST['x_account_number'],
    $_POST['x_amount'],
    $_POST['x_company'],
    $_POST['x_first_name'],
    $_POST['x_last_name'],
    $_POST['x_address'],
    $_POST['x_city'],
    $_POST['x_state'],
    $_POST['x_zip'],
    $_POST['x_country'],
    $_POST['x_phone'],
    $_POST['x_fax'],
    $_POST['x_email'],
    $_POST['x_ship_to_company'],
    $_POST['x_ship_to_first_name'],
    $_POST['x_ship_to_last_name'],
    $_POST['x_ship_to_address'],
    $_POST['x_ship_to_city'],
    $_POST['x_ship_to_state'],
    $_POST['x_ship_to_zip'],
    $_POST['x_ship_to_country'],
    $_POST['x_invoice_num'],
]);

$invoiceId = $_POST["x_invoice_num"];
$transactionId = $_POST["x_trans_id"];
$paymentAmount = $_POST["x_amount"];
$paymentFee = 0;
$success = (bool) $_POST["x_response_code"] == 1;
$transactionStatus = $success ? 'Success' : 'Failure';

/**
 * Validate callback authenticity.
 *
 * Most payment gateways provide a method of verifying that a callback
 * originated from them. In the case of our example here, this is achieved by
 * way of a shared secret which is used to build and compare a hash.
 */

$signatureKey = $gatewayParams['signatureKey'];;
$digest = strtoupper(HASH_HMAC('sha512',"^".$hashData."^",hex2bin($signatureKey)));
if ($_POST['x_response_code'] == '' ||  (strtoupper($_POST['x_SHA2_Hash']) !==  $digest)) {
    $transactionStatus = 'Hash Verification Failure';
    $success = false;
}

/**
 * Validate Callback Invoice ID.
 *
 * Checks invoice ID is a valid invoice number. Note it will count an
 * invoice in any status as valid.
 *
 * Performs a die upon encountering an invalid Invoice ID.
 *
 * Returns a normalised invoice ID.
 *
 * @param int $invoiceId Invoice ID
 * @param string $gatewayName Gateway Name
 */
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['name']);

/**
 * Check Callback Transaction ID.
 *
 * Performs a check for any existing transactions with the same given
 * transaction number.
 *
 * Performs a die upon encountering a duplicate.
 *
 * @param string $transactionId Unique Transaction ID
 */
checkCbTransID($transactionId);

/**
 * Log Transaction.
 *
 * Add an entry to the Gateway Log for debugging purposes.
 *
 * The debug data can be a string or an array. In the case of an
 * array it will be
 *
 * @param string $gatewayName        Display label
 * @param string|array $debugData    Data to log
 * @param string $transactionStatus  Status
 */
logTransaction($gatewayParams['name'], $_POST, $transactionStatus);

if ($success) {

    /**
     * Add Invoice Payment.
     *
     * Applies a payment transaction entry to the given invoice ID.
     *
     * @param int $invoiceId         Invoice ID
     * @param string $transactionId  Transaction ID
     * @param float $paymentAmount   Amount paid (defaults to full balance)
     * @param float $paymentFee      Payment fee (optional)
     * @param string $gatewayModule  Gateway module name
     */
    addInvoicePayment(
        $invoiceId,
        $transactionId,
        $paymentAmount,
        $paymentFee,
        $gatewayModuleName
    );
}
