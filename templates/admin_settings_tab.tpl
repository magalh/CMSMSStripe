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
<input type="text" name="{$actionid}cmsms_stripe_secret" value="{$cmsms_stripe->secret}" size="50"/>
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
 <p class="pageinput">
 <input type="submit" name="{$actionid}submit" value="Save"/>
 </p>
</div>
{form_end}