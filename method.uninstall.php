<?php
if( !defined('CMS_VERSION') ) exit;
$this->RemovePermission(MANAGE_PERM);
$this->RemovePermission(CMSMSStripe::MANAGE_PERM);
$this->RemovePermission(CMSMSStripe::PRODUCTS_PERM);
$this->RemovePermission(CMSMSStripe::TRANSACTIONS_PERM);
$this->RemovePermission(CMSMSStripe::SUBSCRIPTIONS_PERM);

$this->RemoveEvent('SessionCreated');
$this->RemoveEvent('PaymentCompleted');
$this->RemoveEvent('PaymentFailed');
$this->RemoveEvent('SubscriptionCreated');
$this->RemoveEvent('SubscriptionUpdated');
$this->RemoveEvent('SubscriptionExpired');
$this->RemoveEvent('InvoicePaymentFailed');

$this->RemoveEventHandler('MAMS', 'OnLogin');
$this->RemoveEventHandler('MAMS', 'OnLogout');
$this->RemoveEventHandler('MAMS', 'OnCreateUser');
$this->RemoveEventHandler('MAMS', 'OnUpdateUser');
$this->RemoveEventHandler('MAMSRegistration', 'onUserRegistered');

// Remove all preferences for this module
$this->RemovePreference();
?>