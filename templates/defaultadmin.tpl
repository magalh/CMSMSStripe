<h3>Stripe Settings:</h3>
{form_start}

<fieldset>
	
  <div class="pageoverflow">
    <p class="pagetext"><label for="allowuninstall">Production Environment:</label></p>
    <p class="pageinput">
      <select name="{$actionid}stripe_env">{cms_yesno selected=$stripe_env}</select>
    </p>
  </div>
	
<div class="pageoverflow">
 <p class="pagetext">Publishable key:</p>
 <p class="pageinput">
<input type="text" name="{$actionid}stripe_publishable_key" value="{$stripe_publishable_key}" size="50"/>
 </p>
</div>
<div class="pageoverflow">
 <p class="pagetext">Secret:</p>
 <p class="pageinput">
<input type="text" name="{$actionid}stripe_secret" value="{$stripe_secret}" size="50"/>
 </p>
</div>
</fieldset>

<div class="pageoverflow">
 <p class="pageinput">
 <input type="submit" name="{$actionid}submit" value="Save"/>
 </p>
</div>
{form_end}
{*get_template_vars*}