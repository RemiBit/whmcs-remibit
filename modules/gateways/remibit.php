<?php
/**
 * WHMCS Sample Payment Gateway Module
 *
 * Payment Gateway modules allow you to integrate payment solutions with the
 * WHMCS platform.
 *
 * This sample file demonstrates how a payment gateway module for WHMCS should
 * be structured and all supported functionality it can contain.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "gatewaymodule" and therefore all functions
 * begin "gatewaymodule_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _config
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function remibit_MetaData()
{
    return array(
        'DisplayName' => 'RemiBit Payment Gateway',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function remibit_config()
{
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'RemiBit Payment Gateway',
        ),
        // a text field type allows for single line text input
        'loginID' => array(
            'FriendlyName' => 'Login ID',
            'Type' => 'text',
            'Size' => '255',
            'Default' => '',
            'Description' => 'Enter your login ID here',
        ),
        'transactionKey' => array(
            'FriendlyName' => 'Transaction Key',
            'Type' => 'text',
            'Size' => '255',
            'Default' => '',
            'Description' => 'Enter your transaction key here',
        ),
        'signatureKey' => array(
            'FriendlyName' => 'Signature Key',
            'Type' => 'text',
            'Size' => '255',
            'Default' => '',
            'Description' => 'Enter your signature key here',
        ),
        'md5Hash' => array(
            'FriendlyName' => 'MD5 Hash',
            'Type' => 'text',
            'Size' => '255',
            'Default' => '',
            'Description' => 'Enter your MD5 hash here',
        ),
        'url' => array(
            'FriendlyName' => 'RemiBit URL',
            'Type' => 'text',
            'Size' => '255',
            'Default' => 'https://app.remibit.com/pay',
            'Description' => 'Enter RemiBit URL endpoint here',
        ),
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 * @return string
 */
function remibit_link($params)
{
    // Gateway Configuration Parameters
    $loginID = $params['loginID'];
    $transactionKey = $params['transactionKey'];
    $url = $params['url'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    $timeStamp = time();
    if (function_exists('hash_hmac')) {
        $hash_d = hash_hmac('md5', sprintf('%s^%s^%s^%s^%s',
            $loginID,
            $invoiceId,
            $timeStamp,
            $amount,
            $currencyCode
        ), $transactionKey);
    } else
    {
        $hash_d = bin2hex(mhash(MHASH_MD5, sprintf('%s^%s^%s^%s^%s',
            $loginID,
            $invoiceId,
            $timeStamp,
            $amount,
            $currencyCode
        ), $transactionKey));
    }

    $postFields = array(
        'x_login' => $loginID,
        'x_amount' => $amount,
        'x_invoice_num' => $invoiceId,
        'x_relay_response' => "TRUE",
        'x_relay_url' => $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php',
        'x_fp_sequence' => $invoiceId,
        'x_fp_hash' => $hash_d,
        'x_show_form' => 'PAYMENT_FORM',
        'x_version' => $whmcsVersion,
        'x_fp_timestamp' => $timeStamp,
        'x_first_name' => $firstname,
        'x_last_name' => $lastname,
        'x_company' => $companyName,
        'x_address' => $address1 . ' ' . $address2,
        'x_country' => $country,
        'x_state' => $state,
        'x_city' => $city,
        'x_zip' => $postcode,
        'x_phone' => $phone,
        'x_email' => $email,
        'x_ship_to_first_name' => '',
        'x_ship_to_last_name' => '',
        'x_ship_to_company' => '',
        'x_ship_to_address' => '',
        'x_ship_to_country' => '',
        'x_ship_to_state' => '',
        'x_ship_to_city' => '',
        'x_ship_to_zip' => '',
        'x_cancel_url' => $returnUrl,
        'x_freight' => '',
        'x_cancel_url_text' => 'Cancel Payment',
        'x_currency_code' => $currencyCode
    );

    $htmlOutput = '<form method="post" action="' . $url . '">';
    foreach ($postFields as $k => $v) {
        $htmlOutput .= '<input type="hidden" name="' . $k . '" value="' . urlencode($v) . '" />';
    }
    $htmlOutput .= '<input type="submit" value="' . $langPayNow . '" />';
    $htmlOutput .= '</form>';

    return $htmlOutput;
}

