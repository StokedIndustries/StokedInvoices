<?php
/**
 * 	Invoice Page
 * 
 * 	@package stokedinvoices
 */

class InvoicePage extends Page {

	static $db = array(
		'InvoiceCompanyName' => 'Varchar(255)',
		'InvoiceContactName' => 'Varchar(255)',
		'InvoiceCompanyAddr1' => 'Varchar(255)',
		'InvoiceCompanyAddr2' => 'Varchar(255)',
		'InvoiceCompanyCity' => 'Varchar(255)',
		'InvoiceCompanyState' => 'Varchar(255)',
		'InvoiceCompanyCountry' => 'Varchar(255)',
		'InvoiceCompanyPostal' => 'Varchar(255)',
		'InvoicePaymentTerms' => 'Text',
		'InvoiceTaxRate' => 'Varchar(15)',
		'InvoiceTaxLabel' => 'Varchar(255)',
		"InvoiceCurrency" => "Enum('USD,EUR,JPY,GBP,CHF,AUS,CAD,SEK,HKD,NOK,NZD,MXN,SGD,BRL,CNY,CZK,DKK,HUF,ILS,INR,MYR,PHP,PLN,THB,TWD')",
		'PublicPaymentAPIKey' => 'Varchar(255)',
		'PrivatePaymentAPIKey' => 'Varchar(255)',
		'EnableOnlinePayments' => 'Boolean',
		'EnableTax' => 'Boolean'
	);

	static $has_one = array(
	);
	
	static $has_many = array(
	);
	
	/**
	 * Create default invoice page.
	 */
	function requireDefaultRecords() {
	
		parent::requireDefaultRecords();

		$inv_page = DataObject::get_one('InvoicePage');
		
		if(!$inv_page) {
			$inv_page = new InvoicePage();
			$inv_page->Title = "Invoices";
			$inv_page->URLSegment = "invoices";
			
			$inv_page->write();
			$inv_page->publish("Stage", "Live");

			DB::alteration_message("Invoices page created","created");
		}
		
	}
	
	/**
	 * CMSFields.
	 */
	function getCMSFields() {
	
		$f = parent::getCMSFields();
		
		$f->removeFieldFromTab('Root.Content.Main','Content');
		$f->fieldByName('Root.Content.Main')->setTitle('Page Options');
		
		$f->addFieldToTab('Root.Content.ContactInfo', new TextField('InvoiceCompanyName', 'Company/Organization Name'));
		$f->addFieldToTab('Root.Content.ContactInfo', new TextField('InvoiceContactName', 'Main Contact Name'));
		$f->addFieldToTab('Root.Content.ContactInfo', new TextField('InvoiceCompanyAddr1', 'Address 1'));
		$f->addFieldToTab('Root.Content.ContactInfo', new TextField('InvoiceCompanyAddr2', 'Address 2'));
		$f->addFieldToTab('Root.Content.ContactInfo', new TextField('InvoiceCompanyCity', 'City'));
		$f->addFieldToTab('Root.Content.ContactInfo', new TextField('InvoiceCompanyState', 'State/Province'));
		$f->addFieldToTab('Root.Content.ContactInfo', new DropdownField('InvoiceCompanyCountry','Country', Geoip::getCountryDropDown(), Geoip::visitor_country()));
		$f->addFieldToTab('Root.Content.ContactInfo', new TextField('InvoiceCompanyPostal', 'Zip or Postal Code'));
		
		$f->addFieldToTab('Root.Content.PaymentOptions', new CheckboxField('EnableOnlinePayments', 'Enable online payments with Stripe'));
		$f->addFieldToTab('Root.Content.PaymentOptions', new LiteralField('warn', '<p>Caution: Stripe only accepts USD currency at the moment.</p>'));
		$f->addFieldToTab('Root.Content.PaymentOptions', new TextField('PublicPaymentAPIKey', 'Stripe Public API Key'));
		$f->addFieldToTab('Root.Content.PaymentOptions', new TextField('PrivatePaymentAPIKey', 'Stripe Private API Key'));
		
		$f->addFieldToTab('Root.Content.PaymentOptions', new LiteralField('break', '<br><br><br>'));
		
		$f->addFieldToTab('Root.Content.PaymentOptions', new TextareaField('InvoicePaymentTerms', 'Payment Terms'));
		
		$f->addFieldToTab('Root.Content.PaymentOptions', new LiteralField('break', '<br><br><br>'));
		
		$f->addFieldToTab('Root.Content.PaymentOptions', new CheckboxField('EnableTax', 'Collect tax on invoices?'));
		$f->addFieldToTab('Root.Content.PaymentOptions', new TextField('InvoiceTaxRate', 'Tax Rate (decimal number representing a percentage)'));
		$f->addFieldToTab('Root.Content.PaymentOptions', new TextField('InvoiceTaxLabel', 'Tax Label'));
		$f->addFieldToTab('Root.Content.PaymentOptions', new DropdownField('InvoiceCurrency', 'Currency', $this->dbObject('InvoiceCurrency')->enumValues()));

		return $f;
		
	}
	
	public function getCurrencySymbol() {
	
		$currency_select = array('USD' => array('title' => 'U.S. Dollar', 'code' => 'USD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
							'EUR' => array('title' => 'Euro', 'code' => 'EUR', 'symbol_left' => '€', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'JPY' => array('title' => 'Japanese Yen', 'code' => 'JPY', 'symbol_left' => '¥', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'GBP' => array('title' => 'Pounds Sterling', 'code' => 'GBP', 'symbol_left' => '£', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'CHF' => array('title' => 'Swiss Franc', 'code' => 'CHF', 'symbol_left' => '', 'symbol_right' => 'CHF', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                            'AUS' => array('title' => 'Australian Dollar', 'code' => 'AUS', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'CAD' => array('title' => 'Canadian Dollar', 'code' => 'CAD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'SEK' => array('title' => 'Swedish Krona', 'code' => 'SEK', 'symbol_left' => '', 'symbol_right' => 'kr', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                            'HKD' => array('title' => 'Hong Kong Dollar', 'code' => 'HKD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'NOK' => array('title' => 'Norwegian Krone', 'code' => 'NOK', 'symbol_left' => 'kr', 'symbol_right' => '', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                            'NZD' => array('title' => 'New Zealand Dollar', 'code' => 'NZD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'MXN' => array('title' => 'Mexican Peso', 'code' => 'MXN', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'SGD' => array('title' => 'Singapore Dollar', 'code' => 'SGD', 'symbol_left' => '$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'BRL' => array('title' => 'Brazilian Real', 'code' => 'BRL', 'symbol_left' => 'R$', 'symbol_right' => '', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                            'CNY' => array('title' => 'Chinese RMB', 'code' => 'CNY', 'symbol_left' => '￥', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'CZK' => array('title' => 'Czech Koruna', 'code' => 'CZK', 'symbol_left' => '', 'symbol_right' => 'Kč', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                            'DKK' => array('title' => 'Danish Krone', 'code' => 'DKK', 'symbol_left' => '', 'symbol_right' => 'kr', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                            'HUF' => array('title' => 'Hungarian Forint', 'code' => 'HUF', 'symbol_left' => '', 'symbol_right' => 'Ft', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'ILS' => array('title' => 'Israeli New Shekel', 'code' => 'ILS', 'symbol_left' => '₪', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'INR' => array('title' => 'Indian Rupee', 'code' => 'INR', 'symbol_left' => 'Rs.', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'MYR' => array('title' => 'Malaysian Ringgit', 'code' => 'MYR', 'symbol_left' => 'RM', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'PHP' => array('title' => 'Philippine Peso', 'code' => 'PHP', 'symbol_left' => 'Php', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'PLN' => array('title' => 'Polish Zloty', 'code' => 'PLN', 'symbol_left' => '', 'symbol_right' => 'zł', 'decimal_point' => ',', 'thousands_point' => '.', 'decimal_places' => '2'),
                            'THB' => array('title' => 'Thai Baht', 'code' => 'THB', 'symbol_left' => '', 'symbol_right' => '฿', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'),
                            'TWD' => array('title' => 'Taiwan New Dollar', 'code' => 'TWD', 'symbol_left' => 'NT$', 'symbol_right' => '', 'decimal_point' => '.', 'thousands_point' => ',', 'decimal_places' => '2'));
                            
    	if($currency_select[$this->InvoiceCurrency]['symbol_left']) {
    		return array('pos' => 'left', 'symbol' => $currency_select[$this->InvoiceCurrency]['symbol_left']);
    	} else {
    		return array('pos' => 'right', 'symbol' => $currency_select[$this->InvoiceCurrency]['symbol_right']);
    	}
    	
	}
	
}

class InvoicePage_Controller extends Page_Controller {

	public static $allowed_actions = array (
		'view',
		'pay',
		'index',
		'sort',
		'ccpayment',
		'CreditCardForm',
		'thanks'
	);

	public function init() {
		parent::init();
	}
	
	public function index() {
		
		if(!Member::currentUserID()) Security::permissionFailure($this,"You must be logged in to view your invoices.");
		
		$invs = DataObject::get('Invoice', '', 'Created DESC');
		
		return $this->customise(array(
			'Invoices' => $invs,
			'Title' => 'Listing Invoices'
		))->renderWith(array('InvoicePage','Page'));
		
	}
	
	public function view() {
		
		$inv = $this->getInvoice();
		
		Requirements::css('stokedinvoices/css/print-invoice.css','print');
		
		return $this->customise(array(
			'Invoice' => $inv,
			'Title' => 'Viewing Invoice'
		))->renderWith(array('InvoicePage_view','Page'));
		
	}
	
	public function pay() {
	
		$inv = $this->getInvoice();
		
		if($this->EnableOnlinePayments) {
			Requirements::customScript("Stripe.setPublishableKey('" . $this->PublicPaymentAPIKey . "');");
		}
		
		return $this->customise(array(
			'Title' => 'Make a Payment',
			'Invoice' => $inv,
			'Form' => $this->CreditCardForm($inv->InvID)
		))->renderWith(array('InvoicePage_pay','Page'));
		
	}
	
	public function CreditCardForm($inv_id) {
	
		$cvc = new TextField('CVC');
		$cem = new TextField('CardExpiryMonth', 'Expiry Date');
		$cey = new TextField('CardExpiryYear', '(mm/yyyy format)');
		
		$pf = new FieldSet(
			new TextField('CardNumber', 'Card Number'),
			$cvc,
			$cem,
			$cey,
			new HiddenField('StripeToken'),
			new HiddenField('InvID', 'InvID', $inv_id)
		);
		
		$pa = new FieldSet(
			new FormAction('ccpayment', 'Submit Payment')
		);
		
		return new Form($this, 'CreditCardForm', $pf, $pa);
		
	}
	
	public function ccpayment($data, $form) {
	
		$inv = $this->getInvoice($data['InvID']);
		$inv->InvPaid = true;
		$inv->PaymentToken = $data['StripeToken'];
		$inv->write();
		
		Director::redirect($this->Link('thanks/' . $inv->InvID));
		
	}
	
	public function sort() {
		
		$param = Director::URLParam('ID');
		$sort = 'Created DESC';

		if($param) {
			switch($param) {
				case 'paid':
					$sort = 'InvPaid DESC';
					break;
					
				case 'unpaid':
					$sort = 'InvPaid ASC';
					break;
				
				case 'recipient-desc':
					$sort = 'RecipientName DESC';
					break;
					
				case 'recipient-asc':
					$sort = 'RecipientName ASC';
					break;
			}
		}
		
		$invs = DataObject::get('Invoice', '', $sort);
		
		return $this->customise(array(
			'Invoices' => $invs,
			'Title' => 'Listing Invoices: ' . ucwords($param)
		))->renderWith(array('InvoicePage','Page'));
		
	}
	
	public function thanks() {
		
		Requirements::css('stokedinvoices/css/print-invoice.css','print');
		
		$inv = $this->getInvoice();
		
		$this->processPayment($inv);
		
		return $this->customise(array(
			'Title' => 'Payment Confirmation',
			'Invoice' => $inv
		))->renderWith(array('InvoicePage_confirmation','Page'));
		
	}
	
	private function processPayment($inv) {
		// Stripe Payment Processing Library
		chdir(dirname(__FILE__));
		require('../thirdparty/stripe/lib/Stripe.php');
		Stripe::setApiKey($this->PrivatePaymentAPIKey);
		
		Stripe_Charge::create(array(
			"amount" => $inv->GrandTotalInCents,
			"currency" => "usd",
			"card" => $inv->PaymentToken, // obtained with stripe.js
			"description" => "Payment for " . $this->InvoiceCompanyName  . " Invoice " . $inv->InvID
		));
		
	}
	
	private function getInvoice($id = null) {
		
		$param = ($id) ? $id : Director::URLParam('ID');
		
		return DataObject::get_one("Invoice", "InvID = '". Convert::raw2sql($param) ."'");
		
	}	
	
}