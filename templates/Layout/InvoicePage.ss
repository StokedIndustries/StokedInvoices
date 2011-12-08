<% require css(stokedinvoices/css/styles.css) %>

<div id="InvoiceList" class="typography stokedinvoices">
	<h1>$Title</h1>
	
	<% if Invoices %>
	<p>Sort by: <a href="{$BaseHref}{$URLSegment}/sort/paid">Paid</a> |
	<a href="{$BaseHref}{$URLSegment}/sort/unpaid">Unpaid</a> |
	<a href="{$BaseHref}{$URLSegment}/sort/recipient-desc">Recipient Name DESC</a> |
	<a href="{$BaseHref}{$URLSegment}/sort/recipient-asc">Recipient Name ASC</a></p>
	
	<table class="invoice-list">
		<% control Invoices %>
		<tr>
			<td class="status">
				<% if InvPaid = 0 %>
					<span class="unpaid">Unpaid</span>
				<% else %>
					<span class="paid">Paid</span>
				<% end_if %>
			</td>
			<td class="id">$InvID</td>
			<td class="recipient">$RecipientName</td>
			<td class="actions">
				<a href="$Link">View Invoice</a><% if InvPaid = 0 %> | 
				<a href="$PayLink">Pay Invoice</a><% end_if %>
			</td>
		</tr>
		<% end_control %>
	</table>
	<% end_if %>
</div>