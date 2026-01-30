<h3>{if $product}{$mod->Lang('edit_product')}{else}{$mod->Lang('add_product')}{/if}</h3>

{form_start}

<div class="pageoverflow">
  <p class="pagetext"><label for="name">{$mod->Lang('name')}:*</label></p>
  <p class="pageinput">
    <input type="text" id="name" name="{$actionid}name" value="{if $product}{$product->name}{/if}" size="50" required/>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label for="description">{$mod->Lang('description')}:</label></p>
  <p class="pageinput">
    <textarea id="description" name="{$actionid}description" rows="4" cols="50">{if $product}{$product->description}{/if}</textarea>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label for="image">{$mod->Lang('image')}:</label></p>
  <p class="pageinput">
    {xt_filepicker name="{$actionid}image" profile='images'}
    {if $product && $product->images}
      <br/><img src="{$product->images[0]}" alt="Product image" style="max-width:200px;margin-top:10px;"/>
      <br/><small>{$mod->Lang('current_image')}: <a href="{$product->images[0]}" target="_blank">{$mod->Lang('view')}</a></small>
    {/if}
    <span class="helptext smallgrey">{$mod->Lang('image_help')}</span>
  </p>
</div>

{if !$product}
<div class="pageoverflow">
  <p class="pagetext"><label for="price">{$mod->Lang('price')}:</label></p>
  <p class="pageinput">
    <input type="number" id="price" name="{$actionid}price" step="0.01" min="0" size="20"/>
    <span class="helptext smallgrey">{$mod->Lang('price_help')}</span>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label>{$mod->Lang('price_type')}:</label></p>
  <p class="pageinput">
    <input type="radio" id="one_time" name="{$actionid}price_type" value="one_time" checked/>
    <label for="one_time">{$mod->Lang('one_time')}</label>
    <input type="radio" id="recurring" name="{$actionid}price_type" value="recurring" style="margin-left:20px;"/>
    <label for="recurring">{$mod->Lang('recurring')}</label>
  </p>
</div>

<div class="pageoverflow" id="interval_field" style="display:none;">
  <p class="pagetext"><label for="interval">{$mod->Lang('interval')}:</label></p>
  <p class="pageinput">
    <select id="interval" name="{$actionid}interval">
      <option value="day">{$mod->Lang('day')}</option>
      <option value="week">{$mod->Lang('week')}</option>
      <option value="month" selected>{$mod->Lang('month')}</option>
      <option value="year">{$mod->Lang('year')}</option>
    </select>
  </p>
</div>
{/if}

<script>
$(document).ready(function() {
  $('input[name="{$actionid}price_type"]').change(function() {
    if($(this).val() === 'recurring') {
      $('#interval_field').show();
    } else {
      $('#interval_field').hide();
    }
  });
});
</script>

<div class="pageoverflow">
  <p class="pagetext"><label for="active">{$mod->Lang('active')}:</label></p>
  <p class="pageinput">
    <input type="checkbox" id="active" name="{$actionid}active" value="1" {if !$product || $product->active}checked{/if}/>
  </p>
</div>

{if $product}
<input type="hidden" name="{$actionid}product_id" value="{$product->id}"/>
{/if}

<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}" formnovalidate/>
  </p>
</div>

{form_end}
