{if isset($product)}
<div class="stripe-product-detail">
  {if $product->images && count($product->images) > 0}
  <div class="product-images">
    {foreach $product->images as $image}
    <img src="{$image}" alt="{$product->name}" class="product-image">
    {/foreach}
  </div>
  {/if}
  
  <h1>{$product->name}</h1>
  <p>{$product->description}</p>
  
  {if $product->default_price}
  <div class="product-price">
    <strong>{$product->price_formatted}</strong>
    {if $product->recurring}
    <span class="recurring-info">/ {$product->interval}</span>
    {/if}
  </div>
  
  <a href="{$checkout_url}" class="btn btn-primary">Purchase</a>
  {/if}
  
  <a href="{cms_action_url action='summary'}" class="btn">Back to Products</a>
</div>
{else}
<p>Product not found.</p>
{/if}
