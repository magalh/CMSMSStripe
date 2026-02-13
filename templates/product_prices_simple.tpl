{if isset($products) && count($products) == 1}
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center">
                <h4 class="uppercase mb16">{$products[0]->name}</h4>
                <p class="lead mb64">
                    {if $products[0]->description}{$products[0]->description}{/if}
                </p>
            </div>
        </div>
        <!--end of row-->
        <div class="row">
            {assign var="price_count" value=count($products[0]->prices)}
            {assign var="col_size" value=12/$price_count}
            {if $col_size > 4}{assign var="col_size" value=4}{/if}
            {if $col_size < 3}{assign var="col_size" value=3}{/if}
            {foreach $products[0]->prices as $price}
            <div class="col-md-{$col_size} col-sm-6">
                <div class="pricing-table pt-1 text-center{if $price@index == 1} boxed{elseif $price@index == 2} emphasis{/if}">
                    <h5 class="uppercase">{$price->nickname|default:'Plan'}</h5>
                    <span class="price">{$price->symbol}{$price->amount_integer}{if $price->amount_decimal}<span class="decimal">.{$price->amount_decimal}</span>{/if}</span>
                    {if $price->recurring}
                    <p class="lead">Per {$price->interval|capitalize}</p>
                    {else}
                    <p class="lead">One-time Payment</p>
                    {/if}
                    <a class="btn {if $price@index == 2}btn-white{else}btn-filled{/if} btn-lg" href="{$price->checkout_url}">Get Started</a>
                </div>
                <!--end of pricing table-->
            </div>
            {/foreach}
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
{elseif isset($products) && count($products) > 1}
<p class="error">This template only accepts one product. Please specify a single product ID.</p>
{/if}
