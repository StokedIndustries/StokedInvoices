<% require css(stokedinvoices/css/styles.css) %>

<div id="InvoiceList" class="typography stokedinvoices">
	<h1>$Title</h1>
	
	<% if Invoices %>
	<ul class="invoice-list">
		<% control Invoices %>
		<li>
			<span class="status">
				<% if InvPaid = false %>
					<span class="unpaid">Unpaid</span>
				<% else %>
					<span class="paid">Paid</span>
				<% end_if %>
			</span>
			<span class="id">$InvID</span>
			<span class="recipient">$RecipientName</span>
			<span class="actions">
				<a href="$Link">View Invoice</a><% if InvPaid = false %> | 
				<a href="$PayLink">Pay Invoice</a><% end_if %>
			</span>
		</li>
		<% end_control %>
	</ul>
	<% end_if %>
</div>