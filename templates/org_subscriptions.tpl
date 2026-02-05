{if isset($error)}
	<p class="error">Error: {$error}</p>
{elseif $has_subscription && $subscriptions}
	<div class="card card-transparent">
		<div class="card-header">
			<div class="card-title">Your Subscriptions</div>
		</div>
		<div class="card-body">
			<div class="row">
				{foreach $subscriptions as $sub}
					<div class="col-lg-4">
						<div class="card card-default bg-contrast-lower">
							<div class="card-header separator">
								<div class="card-title">{$sub->plan->product->name|default:$sub->plan->nickname}</div>
							</div>
							<div class="card-body">
								{if $sub->plan->product->images[0]}
									<img src="{$sub->plan->product->images[0]}" alt="{$sub->plan->product->name}" class="img-fluid mb-3 mt-3">
								{/if}
								{if $sub->plan->product->description}
									<p>{$sub->plan->product->description}</p>
								{/if}
								<p><strong>Status:</strong> <span class="badge badge-{if $sub->status == 'active'}success{else}secondary{/if}">{$sub->status}</span></p>
								{if $sub->cancel_at_period_end == 1}
									<p class="text-warning"><strong>Notice:</strong> Cancellation requested - subscription will end on {$sub->current_period_end|cms_date_format}</p>
								{/if}
								<p><strong>Amount:</strong> {($sub->plan->amount/100)|string_format:"%.2f"} {$sub->plan->currency|upper}/{$sub->plan->interval}</p>
								{if $sub->current_period_end}
									<p><strong>Next billing:</strong> {$sub->current_period_end|cms_date_format}</p>
								{/if}
								<div class="m-t-15">
									<a href="{cms_action_url module="CMSMSStripe" action="portal_access" cid=$stripe_customer_id link=1 returnto="{get_current_url}"}" class="btn btn-primary btn-cons btn-animated from-left">
										<span>Manage</span>
										<span class="hidden-block">
											<i class="pg-icon">settings</i>
										</span>
									</a>
								</div>
							</div>
							<div class="card-footer text-center">
								<p><strong>License Key:</strong> {ProductLicenses action="license" subscription_id="{$sub->id}" product_id="{$sub->plan->product->id}"}</p>
							</div>
						</div>
					</div>
				{/foreach}
			</div>
		</div>
	</div>
{else}
	<p>No subscriptions found.</p>
{/if}