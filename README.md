WHMCS REMIBIT PLUGIN

INSTALLATION AND CONFIGURATION

## Requirements

* RemiBit merchant account
* WHMCS 8 or higher 

## Installation

1. Fetch the plugin from RemiBit github:

https://github.com/RemiBit/whmcs-remibit/releases/

Right click on whmcs-remibit.zip and save it on your computer.
  
2. Install the plugin in WHMCS:


To install the new module, upload it to the /modules/gateways/ folder of your WHMCS installation.

If the module includes a callback file, that should be uploaded to the /modules/gateways/callback/ folder.

Once uploaded, navigate to Setup > Payment Gateways to activate and configure the new module.

Important Note The process of activating a module detects the type of module that has been created. Therefore if you experience unexpected behaviours, please try deactivating and reactivating your module before continuing.
Troubleshooting errors during activation

If you receive a blank page or error message within the Setup > Payment Gateways page upon uploading your new payment gateway module, this indicates there could be a syntax error within the new code.

To debug this, you can turn on error reporting. To do this, navigate to Setup > General Settings > Other and check the Display Errors setting.

This enables PHP error reporting and should show the cause of any issues. Once resolved, remember to disable Display Errors again.


## Configuration

To configure "RemiBit Payment Gateway For WHMCS":

Go to System Settings page, scroll down to Payment Gateways

Click on [Manage Existing Gateways] tab

Fill up the setup form with the following data:

Show on Order Form - ticked
Display Name - RemiBit Payment Gateway
Login ID - from RemiBit Gateway settings
Transaction Key - from RemiBit Gateway settings
Signature Key - from RemiBit Gateway settings
MD5 Hash - from RemiBit Gateway settings
RemiBit URL - from RemiBit Gateway settings

Click on [Save Changes]




# WHMCS Sample Third Party Payment Gateway Module #

## Summary ##

Payment Gateway modules allow you to integrate payment solutions with the WHMCS
platform.

There are two types of gateway module:

* Third Party Gateways - these are payment solutions where checkout occurs
on a remote website, usually hosted by the payment gateway themselves.

* Merchant Gateways - these are payment solutions where credit card details
are collected - usually within the WHMCS application, though more and more
often this will be done remotely, typically via an iframe, with a page hosted
remotely by the payment gateway enabling tokenised storage.

The sample files here demonstrate how we suggest a Third Party Payment Gateway
module for WHMCS be structured and implemented.

For more information, please refer to the documentation at:
https://developers.whmcs.com/payment-gateways/

## Recommended Module Content ##

The recommended structure of a third party gateway module is as follows.

```
 modules/gateways/
  |- callback/gatewaymodule.php
  |  gatewaymodule.php
```

## Minimum Requirements ##

For the latest WHMCS minimum system requirements, please refer to
https://docs.whmcs.com/System_Requirements

We recommend your module follows the same minimum requirements wherever
possible.

## Useful Resources
* [Developer Resources](https://developers.whmcs.com/)
* [Hook Documentation](https://developers.whmcs.com/hooks/)
* [API Documentation](https://developers.whmcs.com/api/)

[WHMCS Limited](https://www.whmcs.com)
