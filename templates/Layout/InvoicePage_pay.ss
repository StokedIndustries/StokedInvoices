<% require javascript(https://js.stripe.com/v1/) %>
<% require javascript(https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js) %>

<script type="text/javascript">
	Stripe.setPublishableKey('YOUR_API_KEY_HERE');
	
	function stripeResponseHandler(status, response) {
    	if (response.error) {
    		// re-enable the submit button
            $('.submit-button').removeAttr("disabled");
        	//show the errors on the form
        	$(".payment-errors").html(response.error.message);
    	} else {
        	var form$ = $("#payment-form");
        	// token contains id, last4, and card type
        	var token = response['id'];
        	// insert the token into the form so it gets submitted to the server
        	form$.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
        	// and submit
        	form$.get(0).submit();
    	}
	}
	
	$(document).ready(function() {
  		$("#payment-form").submit(function(event) {
    		// disable the submit button to prevent repeated clicks
    		$('.submit-button').attr("disabled", "disabled");

    		var amount = $Invoice.GrandTotalInCents; //amount you want to charge in cents
    		Stripe.createToken({
        		number: $('.card-number').val(),
        		cvc: $('.card-cvc').val(),
        		exp_month: $('.card-expiry-month').val(),
        		exp_year: $('.card-expiry-year').val()
    		}, amount, stripeResponseHandler);

    		// prevent the form from submitting with the default action
    		return false;
  		});
	});
</script>

<% require css(stokedinvoices/css/styles.css) %>

<div id="InvoiceView" class="typography stokedinvoices">
	<h1>$Title</h1>
	
	<p>Amount to be charged: $Invoice.GrandTotal<br />
	In cents: $Invoice.GrandTotalInCents</p>
	
		<span class="payment-errors"></span>
        <form action="" method="post" id="payment-form">
        	<input type="hidden" name="charge-amt" value="$Invoice.GrandTotalInCents" />
            <div class="field">
                <label class="left">Card Number</label>
                <div class="middleColumn"><input type="text" size="20" autocomplete="off" class="card-number" /></div>
            </div>
            <div class="field">
                <label class="left">CVC</label>
                <div class="middleColumn"><input type="text" size="4" autocomplete="off" class="card-cvc d4" /></div>
            </div>
            <div class="field">
                <label class="left">Expiration (MM/YYYY)</label>
                <div class="middleColumn"><input type="text" size="2" class="card-expiry-month d2" />
                &nbsp; &nbsp; / &nbsp; &nbsp;
                <input type="text" size="4" class="card-expiry-year d4" /></div>
            </div>
            <div class="Actions">
	            <button type="submit" class="submit-button action">Submit Payment</button>
	        </div>
        </form>
	
</div>