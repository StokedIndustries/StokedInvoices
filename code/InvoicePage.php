<?php
/**
 * 	Invoice Page
 * 
 * 	@package stokedinvoices
 */
 
 
// Stripe Payment Processing Library
chdir(dirname(__FILE__));
require_once('../thirdparty/stripe/lib/Stripe.php');
Stripe::setApiKey('YOUR_PRIVATE_API_KEY_HERE');

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
		"InvoiceCurrency" => "Enum('USD,EUR,JPY,GBP,CHF,AUS,CAD,SEK,HKD,NOK,NZD,MXN,SGD,BRL,CNY,CZK,DKK,HUF,ILS,INR,MYR,PHP,PLN,THB,TWD')"
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
		
		$f->addFieldToTab('Root.Content.PaymentOptions', new TextareaField('InvoicePaymentTerms', 'Payment Terms'));
		$f->addFieldToTab('Root.Content.PaymentOptions', new TextField('InvoiceTaxRate', 'Tax Rate (decimal number representing a percentage)'));
		$f->addFieldToTab('Root.Content.PaymentOptions', new TextField('InvoiceTaxLabel', 'Tax Label'));
		$f->addFieldToTab('Root.Content.PaymentOptions', new DropdownField('InvoiceCurrency', 'Currency', $this->dbObject('InvoiceCurrency')->enumValues()));

		return $f;
		
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
	
		if(!Member::currentUserID()) Security::permissionFailure($this,"You must be logged in to view the invoice.");
		
		$inv = $this->getInvoice();
		
		Requirements::css('stokedinvoices/css/print-invoice.css','print');
		
		return $this->customise(array(
			'Invoice' => $inv,
			'Title' => 'Viewing Invoice'
		))->renderWith(array('InvoicePage_view','Page'));
		
	}
	
	public function pay() {
	
		$inv = $this->getInvoice();
		
		return $this->customise(array(
			'Title' => 'Make a Payment',
			'Invoice' => $inv,
			'Form' => $this->CreditCardForm($inv->InvID)
		))->renderWith(array('InvoicePage_pay','Page'));
		
	}
	
	public function CreditCardForm($inv_id) {
	
		$cvc = new TextField('CVC');
		//$cvc->addExtraClass('CardCVC');
		
		$cem = new TextField('CardExpiryMonth', 'Expiry Date');
		//$cem->addExtraClass('CardExpiryMonth');
		
		$cey = new TextField('CardExpiryYear', '(mm/yyyy format)');
		//$cey->addExtraClass('CardExpiryYear');
		
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
		
		//$inv = DataObject::get_one("Invoice", "InvID = '" . $data['InvID'] . "'");
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
		
		Stripe_Charge::create(array(
			"amount" => $inv->GrandTotalInCents,
			"currency" => "usd",
			"card" => $inv->PaymentToken, // obtained with stripe.js
			"description" => "Payment for Stoked Industries Invoice " . $inv->InvID
		));
		
	}
	
	private function getInvoice($id = null) {
		
		$param = ($id) ? $id : Director::URLParam('ID');
		
		return DataObject::get_one("Invoice", "InvID = '". Convert::raw2sql($param) ."'");
		
	}	
	
}