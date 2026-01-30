<h3>Subscriptions</h3>

{if isset($subscriptions) && count($subscriptions) > 0}
<table class="pagetable">
  <thead>
    <tr>
      <th>Customer</th>
      <th>Status</th>
      <th>Plan</th>
      <th>Amount</th>
      <th>Interval</th>
      <th>Current Period</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    {foreach $subscriptions as $sub}
    <tr>
      <td>{if $sub->customer->email}{$sub->customer->email}{else}{$sub->customer}{/if}</td>
      <td>
        {if $sub->status == 'active'}
        <span class="badge badge-success">Active</span>
        {elseif $sub->status == 'trialing'}
        <span class="badge badge-info">Trial</span>
        {elseif $sub->status == 'canceled'}
        <span class="badge badge-secondary">Canceled</span>
        {elseif $sub->status == 'incomplete'}
        <span class="badge badge-warning">Incomplete</span>
        {else}
        <span class="badge badge-danger">{$sub->status|capitalize}</span>
        {/if}
      </td>
      <td>{if $sub->items->data[0]->price->product->name}{$sub->items->data[0]->price->product->name}{else}-{/if}</td>
      <td>{($sub->items->data[0]->price->unit_amount / 100)|string_format:'%.2f'} {$sub->items->data[0]->price->currency|upper}</td>
      <td>{$sub->items->data[0]->price->recurring->interval|capitalize}</td>
      <td>{$sub->current_period_start|date_format:'%Y-%m-%d'} - {$sub->current_period_end|date_format:'%Y-%m-%d'}</td>
      <td>
        <a href="https://dashboard.stripe.com/subscriptions/{$sub->id}" target="_blank" title="View in Stripe">
          {admin_icon icon='view.gif' alt='View'}
        </a>
      </td>
    </tr>
    {/foreach}
  </tbody>
</table>
{else}
<p>No subscriptions found.</p>
{/if}
