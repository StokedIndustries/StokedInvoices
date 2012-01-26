<% require css(stokedinvoices/css/styles.css) %>

<div id="InvoiceView" class="typography stokedinvoices">
	<h1>$Title</h1>
	
	<p>&larr; <a href="invoices/">Return to invoices</a></p>
	
	<p>Thank you for your payment of $Invoice.GrandTotal. If you'd like, you can print this page as your receipt.</p>
	
	<a href="$Invoice.Link" class="button-action blue" title="Print this invoice" onclick="window.print(); return false;">Print Invoice</a><br><br>
	
	<div class="inv-header">
		<span class="status">Paid</span>
		<span class="ref">$Invoice.InvID</span>
		<span class="date">$Invoice.Created.Long</span>
	</div>
	<div class="clearfix" style="width:100%;">
		<div class="to contact">
			<strong>To:</strong>
			<p>$Invoice.RecipientName<br>
			$Invoice.RecipientAddr1<br>
			$Invoice.RecipientAddr2<br>
			$Invoice.RecipientCity, $Invoice.RecipientState $Invoice.RecipientPostal<br><br>
			<span>Contact: $Invoice.RecipientContact</span></p>
		</div>
		<div class="from contact">
			<strong>From:</strong>
			<p>$InvoiceCompanyName<br>
			$InvoiceCompanyAddr1<br>
			$InvoiceCompanyAddr2<br>
			$InvoiceCompanyCity, $InvoiceCompanyState $InvoiceCompanyPostal<br><br>
			<span>Contact: $InvoiceContactName</span></p>
		</div>
	</div>
			
	<div class="inv-line-item-header clearfix">
		<span class="desc">Description</span>
		<span class="rate">Price</span>
		<span class="qty">Qty</span>
		<span class="sub">Subtotal</span>
	</div>
			
	<ul class="inv-line-item clearfix">
	<% if Invoice.LineItems %>
		<% control Invoice.LineItems %>
		<li class="clearfix">
			<span class="desc">
				<strong>$Label</strong>
				<div>$Description</div>
			</span>
			<span class="rate">${$RateFormatted}</span>
			<span class="qty">$Qty</span>
			<span class="sub">${$Subtotal}</span>
		</li>
		<% end_control %>
	<% end_if %>
	</ul>
			
	<ul class="inv-totals clearfix">
		<li>
			<span class="left">
				Subtotal:
			</span>
			<span class="right">
				$Invoice.Subtotal
			</span>
		</li>
		<li>
			<span class="left">
				$InvoiceTaxLabel:
			</span>
			<span class="right">
				$Invoice.TaxAmount
			</span>
		</li>
		<li class="grand-total">
			<span class="left">
				<strong>Total:</strong>
			</span>
			<span class="right">
				$Invoice.GrandTotal
			</span>
		</li>
	</ul>
	
</div>