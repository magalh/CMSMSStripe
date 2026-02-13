{if isset($error)}
	<p class="error">Error: {$error}</p>
{elseif $has_purchases && $purchases}
	<div class="card card-transparent">
		<div class="card-header">
			<div class="card-title">Your Purchases</div>
		</div>
		<div class="card-body">
			<div class="row">
				{foreach $purchases as $purchase}
					<div class="col-lg-4">
						<div class="card card-default bg-contrast-lower">
							<div class="card-header separator">
								<div class="card-title">{$purchase->product_name}</div>
								<span class="badge badge-{if $purchase->status == 'paid'}success{else}secondary{/if}">{$purchase->status}</span>
							</div>
							<div class="card-body">
								{if $purchase->product_image}
									<img src="{$purchase->product_image}" alt="{$purchase->product_name}" class="img-fluid mb-3">
								{/if}
								{if $purchase->product_description}
									<p class="text-muted">{$purchase->product_description}</p>
								{/if}
								<p><strong>Price:</strong> {$purchase->price_amount}</p>
								<p><strong>Total Paid:</strong> {$purchase->currency}{$purchase->amount}</p>
								<p><strong>Date:</strong> {$purchase->created|cms_date_format}</p>
								<div class="m-t-15">
									{if $purchase->receipt_url}
										<a href="{$purchase->receipt_url}" target="_blank" class="btn btn-primary btn-cons m-r-10">
											<span>View Invoice</span>
										</a>
									{/if}
									{if $purchase->invoice_pdf}
										<a href="{$purchase->invoice_pdf}" target="_blank" class="btn btn-secondary btn-cons">
											<span>Download PDF</span>
										</a>
									{/if}
								</div>
							</div>
						</div>
					</div>
				{/foreach}
			</div>
		</div>
	</div>
{else}
	<p>No purchases found.</p>
{/if}
