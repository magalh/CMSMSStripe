{if !empty($error) }
	<div class="warning">{$message}</div>
{else}
<table class="pagetable cms_sortable tablesorter" id="payments_table">
    <thead>
    <tr>
    <th class="alnright pagew10">{$mod->Lang('amount')}</th>
    <th></th>
    <th>{$mod->Lang('status')}</th>
    <th>{$mod->Lang('description')}</th>
    <th>{$mod->Lang('customer_name')}</th>
    <th>{$mod->Lang('date')}</th>
    <th>{$mod->Lang('notes')}</th>
    <th class="pageicon {literal}{sortList: false}{/literal}">&nbsp;</th>{* edit *}
    </tr>
    </thead>
    <tbody>
    {foreach $logs as $log}
    <tr class="{cycle values='row1,row2'}" id="{$log->txn_id}">
    <td class="show-spinner"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    </tr>
  {/foreach}
    </tbody>
  </table>
{/if}


<script>

$('#payments_table tbody tr').each(function(index) { 
      var txn_id = $(this).attr('id');
      var actionurl = "{cms_action_url action=ajax_get_trans_detail forjs=1}&m1_txn_id="+txn_id+"&showtemplate=false";
      //console.log(actionurl);

      return fetch(actionurl, {
					method: "POST",
            headers: {
            "Content-Type": "application/json",
            }
					})
        .then((response) => response.json())
				.then((payment) => {
          $("#payments_table tbody tr:eq("+index+") td:eq(0)").html(payment.amount).removeClass("show-spinner").addClass("alnright");
          $("#payments_table tbody tr:eq("+index+") td:eq(1)").html(payment.currency);
          $("#payments_table tbody tr:eq("+index+") td:eq(2)").html(payment.status);
          $("#payments_table tbody tr:eq("+index+") td:eq(3)").html(payment.description);
          $("#payments_table tbody tr:eq("+index+") td:eq(4)").html(payment.customer);
          $("#payments_table tbody tr:eq("+index+") td:eq(5)").html(payment.created);
          $("#payments_table tbody tr:eq("+index+") td:eq(6)").html(payment.outcome);
          $("#payments_table tbody tr:eq("+index+") td:eq(7)").html(payment.link);
        });

  });


</script>
