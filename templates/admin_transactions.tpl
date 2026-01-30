<h3>Transactions</h3>

{if isset($charges) && count($charges) > 0}
<table class="pagetable">
  <thead>
    <tr>
      <th>Date</th>
      <th>Amount</th>
      <th>Status</th>
      <th>Customer</th>
      <th>Description</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    {foreach $charges as $charge}
    <tr>
      <td>{$charge->created|date_format:'%Y-%m-%d %H:%M'}</td>
      <td>{($charge->amount / 100)|string_format:'%.2f'} {$charge->currency|upper}</td>
      <td>
        {if $charge->status == 'succeeded'}
        <span class="badge badge-success">Paid</span>
        {elseif $charge->status == 'pending'}
        <span class="badge badge-warning">Pending</span>
        {else}
        <span class="badge badge-danger">{$charge->status|capitalize}</span>
        {/if}
      </td>
      <td>{if $charge->billing_details && $charge->billing_details->email}{$charge->billing_details->email}{else}-{/if}</td>
      <td>{if $charge->description}{$charge->description}{else}-{/if}</td>
      <td>
        <a href="https://dashboard.stripe.com/payments/{$charge->id}" target="_blank" title="View in Stripe">
          {admin_icon icon='view.gif' alt='View'}
        </a>
      </td>
    </tr>
    {/foreach}
  </tbody>
</table>
{else}
<p>No transactions found.</p>
{/if}
