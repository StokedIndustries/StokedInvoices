<?php

class InvoiceLineItem extends DataObject {

	static $db = array(
		'Label' => 'Varchar(255)',
		'Qty' => 'Decimal',
		'Rate' => 'Decimal',
		'Description' => 'HTMLText'
	);
	
	static $has_one = array(
		'Invoice' => 'Invoice'
	);
	
	static $has_many = array(
	);
	
	static $searchable_fields = array(
		'Label'
	);
	
	static $summary_fields = array(
		'Label',
		'Qty',
		'Rate'
	);
	
	static $defaults = array(
		'Label' => 'New line item',
		'Qty' => '0',
		'Rate' => '55.00'
	);
	
	public function getCMSFields_forPopup() {
		return new FieldSet(
			new TextField('Label'),
			new TextField('Qty'),
			new TextField('Rate'),
			new SimpleHTMLEditorField('Description')
		);
	}
	
	public function getSubtotal() {
		return number_format(($this->Qty * $this->Rate), 2);
	}
	
	public function getRateFormatted() {
		return number_format($this->Rate, 2);
	}

}