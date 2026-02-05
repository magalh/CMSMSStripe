{if isset($products) && count($products) > 0}
  <section>
      <div class="container">
          <div class="row">
              <div class="col-sm-12 text-center">
                  <h1 class="thin mb80 mb-xs-24">Pricing? That's easy.</h1>
              </div>
          </div>
          <!--end of row-->
          <div class="row">
          {assign var="col_size" value=12/count($products)}
          {if $col_size > 4}{assign var="col_size" value=4}{/if}
          {foreach $products as $product}
              <div class="col-md-{$col_size} col-sm-6{if count($products) == 2 && $product@first} col-md-offset-2{/if}" id="{$product->id}">
                  <div class="pricing-table pt-1 text-center {if $product@index == 1} boxed{/if}{if $product@index == 2} emphasis{/if}">
                      <h5 class="uppercase">{$product->name}</h5>
                      <span class="price">{$product->currency_symbol}{$product->price_amount}</span>
                      {if $product->recurring}
                      <p class="lead">Per {$product->interval|capitalize}</p>
                      {else}
                      <p class="lead">One-time Payment</p>
                      {/if}
                      <a class="btn btn-lg {if $product@index == 2}btn-white{else}btn-filled{/if}" href="{$product->checkout_url}">Get Started</a>
                      {if $product->description}
                      <p>{$product->description}</p>
                      {/if}
                  </div>
                  <!--end of pricing table-->
              </div>
          {/foreach}
          </div>
          <!--end of row-->
      </div>
      <!--end of container-->
      <div class="embelish-icons">
          <i class="ti-marker"></i>
          <i class="ti-layout"></i>
          <i class="ti-ruler-alt-2"></i>
          <i class="ti-eye"></i>
          <i class="ti-signal"></i>
          <i class="ti-pulse"></i>
          <i class="ti-marker"></i>
          <i class="ti-layout"></i>
          <i class="ti-ruler-alt-2"></i>
          <i class="ti-eye"></i>
          <i class="ti-signal"></i>
          <i class="ti-pulse"></i>
      </div>
  </section>
{/if}          
   
