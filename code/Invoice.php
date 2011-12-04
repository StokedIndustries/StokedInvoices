<?php

/**
 * 	Invoice DataObject
 * 
 * 	@package stokedinvoices
 */

class Invoice extends DataObject {

	static $db = array(
		'DueDate' => 'Date',
		'InvID' => 'Varchar(255)',
		'InvPaid' => 'Boolean',
		'InvPassword' => 'Varchar(255)',
		'RecipientName' => 'Varchar(255)',
		'RecipientAddr1' => 'Varchar(255)',
		'RecipientAddr2' => 'Varchar(255)',
		'RecipientCity' => 'Varchar(255)',
		'RecipientState' => 'Varchar(255)',
		'RecipientPostal' => 'Varchar(10)',
		'RecipientCountry' => 'Varchar(255)',
		'RecipientContact' => 'Varchar(255)',
		'RecipientEmail' => 'Varchar(255)'
	);
	
	static $has_one = array(
	);
	
	static $has_many = array(
		'LineItems' => 'InvoiceLineItem'
	);
	
	static $searchable_fields = array(
		'InvPaid' => array('title' => 'Paid'),
		'DueDate' => array('title' => 'Due (yyyy-mm-dd)'),
		'InvID' => array('title' => 'Invoice #'),
		'RecipientName',
		'RecipientContact'
	);
	
	static $summary_fields = array(
		'InvID' => 'Invoice #',
		'RecipientName' => 'Recipient Name',
		'getSubtotal' => 'Total',
		'DueDate' => 'Due',
		'isInvoicePaid' => 'Paid'
	);
	
	static $defaults = array(
	);
	
	static $currency = '$';//$this->InvoicePage()->InvoiceCurrency;
	
	static $tax_rate = '4.166';//$this->InvoicePage()->InvoiceTaxRate;
	
	public function getCMSFields() {
		
		$f = new FieldSet();
		
		if($this->ID) $f->push(new ReadonlyField('InvID', 'Invoice #'));
		
		$f->push(new TextField('RecipientName', 'Recipient Name'));
		$f->push(new TextField('RecipientContact', 'Main Contact'));
		$f->push(new TextField('RecipientEmail', 'Contact E-mail Address'));
		$f->push(new TextField('RecipientAddr1', 'Address 1'));
		$f->push(new TextField('RecipientAddr2', 'Address 2'));
		$f->push(new TextField('RecipientCity', 'City'));
		$f->push(new TextField('RecipientState', 'State/Province'));
		$f->push(new DropdownField('RecipientCountry', 'Country', Geoip::getCountryDropDown(), Geoip::visitor_country()));
		$f->push(new TextField('RecipientPostal', 'Zip or Postal Code'));
		
		$df = new DateField('DueDate', 'Due Date');
		$df->setConfig('showcalendar', true);
		$f->push($df);
		
		$f->push(new LiteralField('spacer', '<br><br>'));
		
		if($this->ID) {
		
			$tf = new HasManyComplexTableField(
				$this,
				'LineItems',
				'InvoiceLineItem',
				array(
					'Label' => 'Label',
					'Qty' => 'Qty',
					'Rate' => 'Rate',
					'Subtotal' => 'Subtotal'
				),
				'getCMSFields_forPopup'
			);
			$tf->setAddTitle('Line Item');
			$tf->relationAutoSetting = true;
			
		} else {
			
			$tf = new LiteralField('note', '<p>You will be able to add line items after you save the invoice.</p>');
		
		}
		$f->push($tf);
		
		if($this->ID) {
			$f->push(new LiteralField('total', '<span style="font-size:16px;"><strong style="font-size:16px;">Total:</strong> ' . $this->getSubtotal() . '</span>'));
			$f->push(new LiteralField('link', '<p><a href="' . $this->Link() . '" target="_blank">Click here to view invoice on site</a>.</p>'));
		}
		
		return $f;
	}
	
	function onBeforeWrite() {
		parent::onBeforeWrite();
		
		if(!$this->InvID) {
			$unique = substr($this->RecipientName, 0, 3);
			$unique = strtoupper($unique);
			$this->InvID = $unique . '-' . rand(1000000,9999999);
			//$this->InvID = rand(1000000,9999999);
		}
	}
	
	private function addUpLineItems() {
		$total = 0;
		foreach($this->LineItems() as $item) {
			$total += $item->Rate * $item->Qty;
		}
		return $total;
	}
	
	public function calculateTax($amt) {
		return ($amt * self::$tax_rate)/100;
	}
	
	public function getSubtotal() {
		$total = $this->addUpLineItems();
		return self::$currency . number_format($total, 2);
	}
	
	public function getTaxAmount() {
		$total = $this->addUpLineItems();
		$total = $this->calculateTax($total);
		return self::$currency . number_format($total, 2);
	}
	
	public function getGrandTotal() {
		$total = $this->addUpLineItems();
		$total += $this->calculateTax($total);
		return self::$currency . number_format($total, 2);
	}
	
	public function getGrandTotalInCents() {
		$total = $this->addUpLineItems();
		$total += $this->calculateTax($total);
		return round($total * 100);
	}
	
	public function isInvoicePaid() {
		return ($this->InvPaid) ? 'Yes' : 'No';
	}
	
	public function Link() {
		$inv_page = DataObject::get_one('InvoicePage');
		
		return Director::absoluteBaseURL() . $inv_page->URLSegment . '/view/' . $this->InvID;
	}
	
	public function PayLink() {
		$inv_page = DataObject::get_one('InvoicePage');
		
		return Director::absoluteBaseURL() . $inv_page->URLSegment . '/pay/' . $this->InvID;
	}

}

class InvoiceAdmin extends ModelAdmin {
	
	public static $managed_models = array(
		'Invoice'
	);
	
	static $url_segment = 'invoices';
	static $menu_title = 'Invoices';
	
}