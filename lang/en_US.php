<?php
#A
$lang['active'] = 'Active';
$lang['add_product'] = 'Add Product';
$lang['amount'] = 'Amount';
$lang['admindescription'] = 'A module for retrieving Logs data';
$lang['ask_uninstall'] = 'Are you sure you want to uninstall the CMSMSStripe module?';
$lang['admin_save'] = "Save";
#B
#C
$lang['cancel'] = 'Cancel';
$lang['confirm_delete_product'] = 'Are you sure you want to delete this product?';
$lang['current_image'] = 'Current image';
$lang['currency_code'] = 'Currency Code';
$lang['currency_symbol'] = 'Currency Symbol';
$lang['customer_name'] = 'Customer Name';
#D
$lang['date'] = 'Date';
$lang['day'] = 'Day';
$lang['delete'] = 'Delete';
$lang['description'] = 'Description';
#E
$lang['edit'] = 'Edit';
$lang['edit_product'] = 'Edit Product';
$lang['error'] = 'Error';
$lang['error_product_has_prices'] = 'This product cannot be deleted because it has one or more prices. Please delete all prices first.';
$lang['err_config_currency'] = 'The currency information specified is incomplete or invalid';
#F
$lang['friendlyname'] = 'CMSMSStripe';
$lang['file'] = 'File';
#G
#H
#I
$lang['image'] = 'Product Image';
$lang['image_help'] = 'Select an image for this product';
$lang['image_url'] = 'Image URL';
$lang['image_url_help'] = 'Full URL to product image (must be publicly accessible)';
$lang['interval'] = 'Billing Interval';
#L
$lang['logfilepath'] = 'Please provide your full Log File path, including filename';
$lang['line'] = 'Line';
$lang['length_units'] = 'Length Units';
#M
$lang['message'] = 'Message';
$lang['month'] = 'Month';
#N
$lang['name'] = 'Name';
$lang['notes'] = 'Notes';
#O
$lang['one_time'] = 'One-time Payment';
#P
$lang['price'] = 'Price';
$lang['price_help'] = 'Default price for this product (only for new products)';
$lang['price_type'] = 'Price Type';
$lang['product_created'] = 'Product created successfully';
$lang['product_archived'] = 'Product archived successfully';
$lang['product_deleted'] = 'Product deleted successfully';
$lang['product_updated'] = 'Product updated successfully';
$lang['prompt_email'] = 'Email Address';
$lang['param_cardtemplate'] = 'Applicable only in the default <em>comment form</em> action, this parameter specifies the name of a comment form template to use for generating the display.  If this parameter is not specified, the comment form template that is currently marked as &quot;default&quot; in the admin interface will be used.';
#Q
#R
$lang['recurring'] = 'Recurring Subscription';
$lang['reviews'] = 'Reviews';
#S
$lang['submit'] = 'Submit';
$lang['status'] = 'Status';
#T
$lang['type'] = 'Type';
$lang['type_CMSMSStripe'] = 'CMSMSStripe';
$lang['type_Detail'] = 'Detail';
$lang['type_Reviews'] = 'Reviews';
$lang['type_CMSMSStripe'] = 'CMSMSStripe';
$lang['type_simple_checkout'] = 'Simple Checkout';
$lang['type_product_list'] = 'Product List';
$lang['type_product_detail'] = 'Product Detail';
$lang['type_payment_success'] = 'Payment Success';
$lang['tpltype_product_list'] = 'Product List';
$lang['tpltype_product_detail'] = 'Product Detail';
$lang['tpltype_payment_success'] = 'Payment Success';
$lang['type_Ratings_View'] = 'Ratings View';
$lang['type_Summary_View'] = 'Summary View';
$lang['type_Card Form'] = 'Card Form';
$lang['type_Checkout'] = 'Checkout';
#U
$lang['units_centimeters'] = 'Centimeters';
$lang['units_inches'] = 'Inches';
$lang['unlimited'] = 'Unlimited';
$lang['unset'] = 'Unset';
$lang['url_success_title'] = 'Success Url';
$lang['url_success_descr'] = 'The URL the customer will be directed to after the payment or subscription creation is successful.';
$lang['success_page_title'] = 'Success Page ID/Alias';
$lang['success_page_descr'] = 'Page ID or alias where the success template will be rendered (e.g., "account" or "123")';
$lang['url_cancel_title'] = 'Cancel Url';
$lang['url_cancel_descr'] = 'If set, Checkout displays a back button and customers will be directed to this URL if they decide to cancel payment and return to your website.';
$lang['url_webhook_title'] = 'Webhook Endpoint';
$lang['url_webhook_descr'] = 'You can configure webhook endpoints via the API to be notified about events that happen in your Stripe account or connected accounts.';
#V
$lang['VALIDATION_ERROR'] = 'Please provide your Log File path';
$lang['view'] = 'View';
$lang['view_in_stripe'] = 'View in Stripe';
#W
$lang['week'] = 'Week';
$lang['weight_units'] = 'Weight Units';
$lang['wunit_lbs'] = 'Pounds (lbs)';
$lang['wunit_kg'] = 'Kilograms (kg)';
$lang['wunit_hg'] = 'Hectogram (hg)';
$lang['wunit_g'] = 'Grams (g)';
#Y
$lang['year'] = 'Year';
#Z

$lang['event_info_SessionCreated'] = 'Fired when a Stripe checkout session is created';
$lang['event_info_SessionCompleted'] = 'Fired when a Stripe checkout session is completed successfully';
$lang['event_info_PaymentCompleted'] = 'Fired when a payment is successfully completed';
$lang['event_info_PaymentFailed'] = 'Fired when a payment attempt fails';
$lang['event_info_SubscriptionCreated'] = 'Fired when a new subscription is created';
$lang['event_info_SubscriptionUpdated'] = 'Fired when a subscription is updated or modified';
$lang['event_info_SubscriptionDeleted'] = 'Fired when a subscription is canceled or deleted';
$lang['event_info_SubscriptionPaused'] = 'Fired when a subscription is paused';
$lang['event_info_SubscriptionResumed'] = 'Fired when a paused subscription is resumed';
$lang['event_info_InvoicePaid'] = 'Fired when an invoice is successfully paid';
$lang['event_info_InvoicePaymentFailed'] = 'Fired when a recurring invoice payment fails';
$lang['event_info_InvoiceFinalized'] = 'Fired when an invoice is finalized and ready for payment';
$lang['event_info_RefundIssued'] = 'Fired when a refund is issued for a payment';

$lang['event_help_SessionCreated'] = '<p>Parameters: session_id, amount, currency, customer</p>';
$lang['event_help_SessionCompleted'] = '<p>Parameters: session_id, amount, currency, customer</p>';
$lang['event_help_PaymentCompleted'] = '<p>Parameters: session, customer_id, amount, payment_intent</p>';
$lang['event_help_PaymentFailed'] = '<p>Parameters: payment_intent_id, amount, currency, customer, error</p>';
$lang['event_help_SubscriptionCreated'] = '<p>Parameters: subscription_id, customer_id, mams_user_id, status</p>';
$lang['event_help_SubscriptionUpdated'] = '<p>Parameters: subscription_id, customer_id, status</p>';
$lang['event_help_SubscriptionDeleted'] = '<p>Parameters: subscription_id, customer_id, mams_user_id, status</p>';
$lang['event_help_SubscriptionPaused'] = '<p>Parameters: subscription_id, customer_id, status</p>';
$lang['event_help_SubscriptionResumed'] = '<p>Parameters: subscription_id, customer_id, status</p>';
$lang['event_help_InvoicePaid'] = '<p>Parameters: invoice_id, customer_id, amount_paid, subscription_id</p>';
$lang['event_help_InvoicePaymentFailed'] = '<p>Parameters: invoice_id, customer_id, amount_due, subscription_id</p>';
$lang['event_help_InvoiceFinalized'] = '<p>Parameters: invoice_id, customer_id, amount_due, subscription_id</p>';
$lang['event_help_RefundIssued'] = '<p>Parameters: refund_id, amount, currency, payment_intent</p>';

?>