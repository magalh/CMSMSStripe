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
								{if $product->price_nickname}
									<span class="badge badge-primary">{$product->price_nickname}</span>
								{/if}
							</div>
							<div class="card-body">
								{if $product->images && count($product->images) > 0}
									<img src="{$product->images[0]}" alt="{$product->name}" class="img-fluid mb-3">
								{/if}
								<p>{$product->description|regex_replace:"/[\r\n]/" : "<br>"}</p>
								
								{if $product->price_metadata}
									{if isset($product->price_metadata->credits)}
										<p class="text-muted"><strong>Credits:</strong> {$product->price_metadata->credits}</p>
									{/if}
									{if isset($product->price_metadata->covers)}
										<p class="text-muted">{$product->price_metadata->covers}</p>
									{/if}
								{/if}
								
								<h4 class="text-primary">
									{$product->formatted}
									{if $product->recurring}
										<span class="text-muted">/ {$product->interval}</span>
									{/if}
								</h4>
								
								{if !$product->recurring}
									<p class="text-muted">One-time Payment</p>
								{/if}
								
								<div class="mt-3">
									<a href="{$product->checkout_url}" class="btn btn-primary">Purchase</a>
								</div>
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
