<?php
if( !defined('CMS_VERSION') ) exit;
$this->RemovePermission(MANAGE_PERM);
$this->RemovePermission(CMSMSStripe::MANAGE_PERM);
$this->RemovePermission(CMSMSStripe::PRODUCTS_PERM);
$this->RemovePermission(CMSMSStripe::TRANSACTIONS_PERM);
$this->RemovePermission(CMSMSStripe::SUBSCRIPTIONS_PERM);

$this->RemoveEvent('SessionCreated'); // custom
$this->RemoveEvent('SessionCompleted'); // checkout.session.completed
$this->RemoveEvent('PaymentCompleted'); // payment_intent.succeeded
$this->RemoveEvent('PaymentFailed'); // payment_intent.payment_failed
$this->RemoveEvent('SubscriptionCreated'); // customer.subscription.created
$this->RemoveEvent('SubscriptionUpdated'); // customer.subscription.updated
$this->RemoveEvent('SubscriptionDeleted'); // customer.subscription.deleted
$this->RemoveEvent('SubscriptionPaused'); // customer.subscription.paused
$this->RemoveEvent('SubscriptionResumed'); // customer.subscription.resumed
$this->RemoveEvent('InvoicePaid'); // invoice.paid
$this->RemoveEvent('InvoicePaymentFailed'); // invoice.payment_failed
$this->RemoveEvent('InvoiceFinalized'); // invoice.finalized
$this->RemoveEvent('RefundIssued'); // charge.refunded

$this->RemoveEventHandler('MAMS', 'OnLogin');
$this->RemoveEventHandler('MAMS', 'OnLogout');
$this->RemoveEventHandler('MAMS', 'OnCreateUser');
$this->RemoveEventHandler('MAMS', 'OnUpdateUser');
$this->RemoveEventHandler('MAMSRegistration', 'onUserRegistered');

// Remove all preferences for this module
$this->RemovePreference();
?>