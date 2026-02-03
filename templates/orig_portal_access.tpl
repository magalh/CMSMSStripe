<div class="stripe-portal-access">
	<h2>Access Your Subscription</h2>
	
	{if $success}
		<div class="alert alert-success">{$success}</div>
	{/if}
	
	{if $error}
		<div class="alert alert-error">{$error}</div>
	{/if}
	
	<form method="post" action="{$formstart}">
		<div class="form-group">
			<label for="email">Email Address</label>
			<input type="email" name="{$actionid}email" id="email" value="{$email}" required placeholder="your@email.com">
			<small>Enter the email address associated with your subscription</small>
		</div>
		
		<button type="submit" name="{$actionid}submit">Send Magic Link</button>
	</form>
</div>
