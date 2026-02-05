{if !empty($error) }
	<div class="warning">{$message}</div>
{else}
<table class="pagetable cms_sortable tablesorter" id="audit_table">
    <thead>
    <tr>
    <th>ID</th>
    <th>Subscription ID</th>
    <th>Event ID</th>
    <th>Module</th>
    <th>User ID</th>
    <th>Action</th>
    <th>Date</th>
    </tr>
    </thead>
    <tbody>
    {foreach $logs as $log}
    <tr class="{cycle values='row1,row2'}">
    <td>{$log->id}</td>
    <td>{$log->subscription_id}</td>
    <td>{$log->event_id|truncate:'30'}</td>
    <td>{$log->module_name}</td>
    <td>{$log->user_id}</td>
    <td>{$log->action}</td>
    <td>{$log->created_at|date_format:"%Y-%m-%d %H:%M:%S"}</td>
    </tr>
  {foreachelse}
    <tr><td colspan="7">No audit logs found</td></tr>
  {/foreach}
    </tbody>
  </table>
{/if}
