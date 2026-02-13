{if isset($products) && count($products) > 0}
  <section>
      <div class="container">
          <div class="row">
              <div class="col-sm-12 text-center">
                  <h1 class="thin mb80 mb-xs-24">Choose Your Product</h1>
              </div>
          </div>
          <!--end of row-->
          <div class="row">
          {assign var="col_size" value=12/count($products)}
          {if $col_size > 4}{assign var="col_size" value=4}{/if}
          {assign var="offset" value=""}
          {if count($products) == 1}{assign var="offset" value=" col-md-offset-4"}{/if}
          {if count($products) == 2}{assign var="offset" value=" col-md-offset-2"}{/if}
          {foreach $products as $product}
              {if !empty($product->prices)}
              <div class="col-md-{$col_size} col-sm-6{if $product@first}{$offset}{/if}" id="{$product->id}">
                  <div class="pricing-table pt-1 text-center{if $highlight && $product->id == $highlight} emphasis{elseif $product@index == 1} boxed{elseif $product@index == 2} emphasis{/if}">
                      <h5 class="uppercase">{$product->name}</h5>
                      <div class="price comboblock">
                          <span class="pricing-1-table-dollar-sign">{$product->prices[0]->symbol}</span>
                          {$product->prices[0]->amount_integer}{if $product->prices[0]->amount_decimal}<span class="decimal">.{$product->prices[0]->amount_decimal}</span>{/if}
                      </div>
                      {if $product->prices[0]->recurring}
                      <p class="lead">Per {$product->prices[0]->interval|capitalize}</p>
                      {else}
                      <p class="lead">One-time Payment</p>
                      {/if}
                      <a class="btn btn-white btn-lg" href="{$product->prices[0]->checkout_url}">Get Started</a>
                      {if $product->description}
                      <p>{$product->description}</p>
                      {/if}
                  </div>
              </div>
              {/if}
          {/foreach}
          </div>
          <!--end of row-->
      </div>
  </section>
{/if}
