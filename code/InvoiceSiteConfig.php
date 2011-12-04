<?php

class InvoiceSiteConfig extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array(
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
				'InvoiceCurrency' => 'Varchar(1)'
			)
		);
	}
	
	public function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab('Root.Invoices', new LiteralField('companyinfo', '<h2>Company/Organization Information</h2><hr><br>'));
		$fields->addFieldToTab('Root.Invoices', new TextField('InvoiceCompanyName', 'Company/Organization Name'));
		$fields->addFieldToTab('Root.Invoices', new TextField('InvoiceContactName', 'Main Contact Name'));
		$fields->addFieldToTab('Root.Invoices', new TextField('InvoiceCompanyAddr1', 'Address 1'));
		$fields->addFieldToTab('Root.Invoices', new TextField('InvoiceCompanyAddr2', 'Address 2'));
		$fields->addFieldToTab('Root.Invoices', new TextField('InvoiceCompanyCity', 'City'));
		$fields->addFieldToTab('Root.Invoices', new TextField('InvoiceCompanyState', 'State'));
		$fields->addFieldToTab('Root.Invoices', new TextField('InvoiceCompanyPostal', 'Postal Code'));
		$fields->addFieldToTab('Root.Invoices', new CountryDropdownField('InvoiceCompanyCountry', 'Country'));
		$fields->addFieldToTab('Root.Invoices', new LiteralField('paymentinfo', '<br><h2>Payment, Tax and Currency Information</h2><hr><br>'));
		$fields->addFieldToTab('Root.Invoices', new TextareaField('InvoicePaymentTerms', 'Payment Terms'));
		$fields->addFieldToTab('Root.Invoices', new TextField('InvoiceTaxRate', 'Tax Rate (decimal number representing a percentage)'));
		$fields->addFieldToTab('Root.Invoices', new TextField('InvoiceCurrency', 'Currency Symbol'));
	}

}