<% require css(stokedinvoices/css/styles.css) %>
<% require javascript(https://js.stripe.com/v1/) %>
<% require javascript(https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js) %>
<% require javascript(stokedinvoices/javascript/payment.js) %>

<script type="text/javascript">
	var pay_amount = $Invoice.GrandTotalInCents; //amount you want to charge in cents
</script>

<div id="InvoiceView" class="typography stokedinvoices">
	<h1>$Title</h1>
	
	<p>&larr; <a href="$Invoice.Link">Go back to the invoice</a></p>
	
	<p>Your card will be charged $Invoice.TotalWithCurrency for invoice $Invoice.InvID.</p>
	
	<div class="payment-icons"><span>We accept Visa, MasterCard and American Express</span></div>
	
	<p class="payment-errors"></p>
	$Form  
	
</div>