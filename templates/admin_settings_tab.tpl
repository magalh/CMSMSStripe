<h3>Stripe Settings</h3>
{form_start}

<fieldset>
	
  <div class="pageoverflow">
    <p class="pagetext"><label>Production Environment:</label></p>
    <p class="pageinput">
      <select name="{$actionid}cmsms_stripe_env">{cms_yesno selected=$cmsms_stripe->env}</select>
    </p>
  </div>
	
<div class="pageoverflow">
 <p class="pagetext"><label>Publishable key:</label></p>
 <p class="pageinput">
<input type="text" name="{$actionid}cmsms_stripe_publishable_key" value="{$cmsms_stripe->publishable_key}" size="50"/>
 </p>
</div>
<div class="pageoverflow">
 <p class="pagetext"><label>Secret:</label></p>
 <p class="pageinput">
<input type="text" id="stripe_secret" name="{$actionid}cmsms_stripe_secret" value="{$cmsms_stripe->secret}" size="50"/>
<button type="button" id="test_credentials" class="btn btn-secondary" style="margin-left:10px;">Test Connection</button>
<span id="test_result" style="margin-left:10px;"></span>
 </p>
</div>
<div class="pageoverflow">
 <p class="pagetext"><label>Webhook Secret:</label></p>
 <p class="pageinput">
<input type="text" name="{$actionid}cmsms_stripe_webhook_secret" value="{$cmsms_stripe->webhook_secret}" size="50"/>
<span class="helptext smallgrey">Get this from Stripe CLI or Stripe Dashboard webhook settings</span>
 </p>
</div>
<div class="pageoverflow">
 <p class="pagetext"><label>{$mod->Lang('currency_code')}:</label></p>
 <p class="pageinput">
 <select name="{$actionid}cmsms_stripe_currency_code">
  {html_options options=$currency_list selected=$cmsms_stripe->currency_code}
</select>
 </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"><label>{$mod->Lang('url_webhook_title')}:</label></p>
  <p class="pageinput">
  <input type="text" name="{$actionid}cmsms_stripe_url_webhook" value="{$cmsms_stripe->url_webhook}" size="50"/>
  </p>
  {$webhook_url}
  <span class="helptext smallgrey">{$mod->Lang('url_webhook_descr')}</span>
</div>
<div class="pageoverflow">
  <p class="pagetext"><label>{$mod->Lang('url_success_title')}:</label></p>
  <p class="pageinput">
  <input type="text" name="{$actionid}cmsms_stripe_url_success" value="{$cmsms_stripe->url_success}" size="50"/>
  </p>
  <span class="helptext smallgrey">{$mod->Lang('url_success_descr')}</span>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label>{$mod->Lang('success_page_title')}:</label></p>
  <p class="pageinput">
  <input type="text" name="{$actionid}cmsms_stripe_success_page" value="{$cmsms_stripe->success_page}" size="50" placeholder="page-alias or 123"/>
  </p>
  <span class="helptext smallgrey">{$mod->Lang('success_page_descr')}</span>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label>{$mod->Lang('url_cancel_title')}:</label></p>
  <p class="pageinput">
  <input type="text" name="{$actionid}cmsms_stripe_url_cancel" value="{$cmsms_stripe->url_cancel}" size="50"/>
  </p>
  <span class="helptext smallgrey">{$mod->Lang('url_cancel_descr')}</span>
</div>

<div class="pageoverflow">
 <p class="pageinput">
 <input type="submit" name="{$actionid}submit" value="Save"/>
 </p>
</div>
{cms_action_url action=webhook}
{form_end}

<script>
$('#test_credentials').on('click', function() {
	var secret = $('#stripe_secret').val();
	if(!secret) {
		$('#test_result').html('<span class="error">Secret key required</span>');
		return;
	}
	$('#test_result').html('<span>Testing...</span>');
	
	$.post('{cms_action_url action=ajax_test_credentials forjs=1}&showtemplate=false', 
		{ '{$actionid}secret': secret },
		function(data) {
			if(data.success) {
				$('#test_result').html('<span style="color:green;">✓ ' + data.message + '</span>');
			} else {
				$('#test_result').html('<span style="color:red;">✗ ' + data.message + '</span>');
			}
		}, 'json');
});
</script>