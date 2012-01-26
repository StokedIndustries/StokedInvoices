(function($) {

	function stripeResponseHandler(status, response) {
    	if (response.error) {
    		// re-enable the submit button
            $('.action').removeAttr("disabled");
        	//show the errors on the form
        	$(".payment-errors").html(response.error.message);
    	} else {
        	$('#Form_CreditCardForm_StripeToken').val(response['id']);
        	// and submit
        	$('#Form_CreditCardForm').submit();
    	}
	}

	$(document).ready(function() {
  		
  		$('#Form_CreditCardForm .action').click(function(event) {
  			event.preventDefault();
  		
    		// disable the submit button to prevent repeated clicks
    		$('.action').attr("disabled", "disabled");

    		Stripe.createToken({
        		number: $('#Form_CreditCardForm_CardNumber').val(),
        		cvc: $('#Form_CreditCardForm_CVC').val(),
        		exp_month: $('#Form_CreditCardForm_CardExpiryMonth').val(),
        		exp_year: $('#Form_CreditCardForm_CardExpiryYear').val()
    		}, pay_amount, stripeResponseHandler);

    		// prevent the form from submitting with the default action
    		return false;
  		});
  			
	});
	
})(jQuery);