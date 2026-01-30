<div class="card card-transparent">
  <div class="card-header">
    <div class="card-title">Products</div>
  </div>
  <div class="card-body">
    {if isset($products) && count($products) > 0}
    <div class="row">
      {foreach $products as $product}
      <div class="col-lg-4">
        <div class="card card-default">
          <div class="card-header">
            <div class="card-title">{$product->name}</div>
          </div>
          <div class="card-body">
            {if $product->images && count($product->images) > 0}
            <img src="{$product->images[0]}" alt="{$product->name}" class="img-fluid mb-3">
            {/if}
            <p>{$product->description}</p>
            {if $product->default_price}
            <h4 class="text-primary">{$product->price_formatted}
            {if $product->recurring}
            <span class="text-muted">/ {$product->interval}</span>
            {/if}
            </h4>
            <div class="mt-3">
              <a href="{$product->checkout_url}" class="btn btn-primary">Purchase</a>
              <a href="{cms_action_url action='detail' product_id=$product->id}" class="btn btn-default">View Details</a>
            </div>
            {/if}
          </div>
        </div>
      </div>
      {/foreach}
    </div>
    {else}
    <p>No products available.</p>
    {/if}
  </div>
</div>
