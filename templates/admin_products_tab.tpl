<h3>Product Management</h3>

<div class="pageoptions">
  <a href="{cms_action_url action=admin_edit_product}">{admin_icon icon='newobject.gif'} {$mod->Lang('add_product')}</a>
</div>

{if !empty($products)}
<table class="pagetable" id="products_table">
  <thead>
    <tr>
      <th>Product ID</th>
      <th>Name</th>
      <th>Description</th>
      <th>Default Price</th>
      <th>Active</th>
      <th>Created</th>
      <th class="pageicon">&nbsp;</th>
      <th class="pageicon">&nbsp;</th>
      <th class="pageicon">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    {foreach $products as $product}
    <tr class="{cycle values='row1,row2'}">
      <td>{$product->id}</td>
      <td><a href="{cms_action_url action=admin_edit_product product_id=$product->id}">{$product->name}</a></td>
      <td>{$product->description|truncate:50}</td>
      <td>
        {if $product->default_price && $product->default_price->unit_amount}
          {($product->default_price->unit_amount / 100)|string_format:'%.2f'} {$product->default_price->currency|upper}
          {if $product->default_price->type == 'recurring'}
            / {$product->default_price->recurring->interval}
          {/if}
        {else}
          -
        {/if}
      </td>
      <td>{if $product->active}Yes{else}No{/if}</td>
      <td>{$product->created|date_format:'%Y-%m-%d %H:%M'}</td>
      <td>
        <a href="{cms_action_url action=admin_edit_product product_id=$product->id}" title="{$mod->Lang('edit')}">{admin_icon icon='edit.gif'}</a>
      </td>
      <td>
        <a href="{cms_action_url action=admin_delete_product product_id=$product->id}" onclick="return confirm('{$mod->Lang('confirm_delete_product')}');" title="{$mod->Lang('delete')}">{admin_icon icon='delete.gif'}</a>
      </td>
      <td>
        <a href="https://dashboard.stripe.com{if !$cmsms_stripe->env}/test{/if}/products/{$product->id}" target="_blank" title="{$mod->Lang('view_in_stripe')}">{admin_icon icon='view.gif'}</a>
      </td>
    </tr>
    {/foreach}
  </tbody>
</table>
{else}
<p>No products found.</p>
{/if}
