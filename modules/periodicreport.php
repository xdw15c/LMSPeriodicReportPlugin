<?php

/*
 * LMS version 1.11-git
 *
 *  (C) Copyright 2001-2017 LMS Developers
 *
 *  Please, see the doc/AUTHORS for more information about authors!
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 *  $Id$
 */

// function defined in LMS class for 1.11.23 and newer
$type = isset($_GET['type']) ? $_GET['type'] : '';

switch ($type) {

case 'periodicreport':

		if (!ConfigHelper::checkConfig('privileges.superuser') && !ConfigHelper::checkConfig('privileges.finances_management'))
			access_denied();

		$from = $_POST['from'];
		$to = $_POST['to'];

		// date format 'yyyy/mm/dd'
		list($year, $month, $day) = explode('/',$from);
		$date['from'] = mktime(0,0,0, (int)$month, (int)$day, (int)$year);

		if($to) {
			list($year, $month, $day) = explode('/',$to);
			$date['to'] = mktime(23,59,59,$month,$day,$year);
		} else {
			$to = date("Y/m/d",time());
			$date['to'] = mktime(23,59,59); // end of today
		}

		$layout['pagetitle'] = trans('Periodic report ($a to $b)',($from ? $from : ''), $to);

		$balancereport = $DB->GetAll('SELECT
				cash.customerid,
				cash.time,
				cash.type AS transactiontype,
				cash.value,
				cash.comment,
				c.name,
				c.lastname,
				c.city,
				c.address,
				d.number,
				d.fullnumber AS invoicenumber,
				d.cdate,
				n.template AS numbertemplate
			FROM cash
			JOIN customerview c ON cash.customerid = c.id
			LEFT JOIN documents d on cash.docid = d.id
			LEFT JOIN numberplans n ON n.id = d.numberplanid
			WHERE
				cash.time >= ? AND
				cash.time <= ?
			ORDER BY
				cash.customerid, d.id;',
			array($date['from'], $date['to']));

		$monthBeginSum = $DB->GetAllByKey('SELECT
			customerid,
			SUM(value) AS value
			FROM
				cash
			WHERE
				time <= ?
			GROUP BY
				customerid;',
				"customerid", array($date['from']) );

		$customer_collection = new LMSCustomerCollection();

		foreach ($balancereport as $row) {
			$ID = $row['customerid'];

			if (!$customer_collection->customerExists($ID)) {
				$customer = $customer_collection->addCustomer($ID);
				$customer->setName($row['name']);
				$customer->setLastname($row['lastname']);
				$customer->setCity($row['city']);
				$customer->setAddress($row['address']);
			}

			switch ( $row['transactiontype'] ) {
				case 0:
					$value = $row['value'];
					$title = $row['comment'];
					$number = $row['invoicenumber'];

					if( $row['invoicenumber'] != NULL )
						$number = $row['invoicenumber'];
					else if( $row['number'] != NULL )
						$number = docnumber($row['number'], $row['numbertemplate'], $row['cdate']);
					else
						$number = $title;

					$customer->addInvoice($number)->addPosition($value, $title);
				break;

				case 1:
					$value = $row['value'];
					$title = $row['comment'];
					$time = $row['time'];
					$customer->addPayment($value, $time, $title);
				break;
			}
		}

		$customerList = $customer_collection->getCustomerList();
		$totalInvoiceSummary = $totalPaymentSummary = 0;

		foreach($customerList as $singleCustomer) {

			//CREATE LIST OF INVOICE NUMBER'S
			$tmp_invoicelist = $singleCustomer->getInvoiceList();
			$customerInvoiceSummary = 0;
			$invoiceNameList = $invoicePriceList = array();
			if (empty($tmp_invoicelist))
				$tmp_invoicelist = array();
			else
				foreach($tmp_invoicelist as $singleInvoice) {
					$invoiceNameList[] = $singleInvoice->getNumber();
					$tmp = ($singleInvoice->getSummary() * (-1));
					$invoicePriceList[] = $tmp;
					$customerInvoiceSummary += $tmp;
				}
			//----

			//CREATE LIST OF PAYMENTS
			$tmp_paymentlist = $singleCustomer->getPaymentList();
			$paymentDateList = $paymentValueList = array();
			if (empty($tmp_paymentlist))
				$tmp_paymentlist = array();
			else
				foreach($tmp_paymentlist as $singlePayment) {
					$paymentDateList[] = $singlePayment->getDate();
					$tmp = $singlePayment->getValue();
					$paymentValueList[] = $tmp;
				}
			//----

			$customerPaymentSummary = round( $singleCustomer->getPaymentSummary(), 2);
			$customerInvoiceSummary = round( $customerInvoiceSummary, 2);
			$balance = $customerPaymentSummary - $customerInvoiceSummary;

			if( isset($_POST['allowZeroClient']) || (!isset($_POST['allowZeroClient']) && $balance!=0)) {
				$totalInvoiceSummary += $customerInvoiceSummary;
				$totalPaymentSummary += $customerPaymentSummary;

				if ($balance < 0) {
					$owing = abs($balance);
					$has = NULL;
				} elseif ($balance > 0) {
					$has = $balance;
					$owing = NULL;
				} else {
					$has = $owing = NULL;
				}

				$customer_array[] = array("id"					=> $singleCustomer->getID(),
										"name"					=> $singleCustomer->getName(),
										"lastname"				=> $singleCustomer->getLastname(),
										"city"					=> $singleCustomer->getCity(),
										"address"				=> $singleCustomer->getAddress(),
										"invoicelist"			=> $invoiceNameList,
										"invoicepricelist"		=> $invoicePriceList,
										"invoicesum"			=> $customerInvoiceSummary,
										"paymentlist"			=> $paymentValueList,
										"paymentDatelist"		=> $paymentDateList,
										"paymentsum"			=> $customerPaymentSummary,
										"owing"					=> $owing,
										"has"					=> $has,
										"dayBeforeBalance"		=> $monthBeginSum[$singleCustomer->getID()]['value']);
			}
		}
		if( $from != '' && isset($_POST['monthBeginSum']) )
			$SMARTY->assign('dayBeforeBalance', $date['from']-1);

		$SMARTY->assign('customer_list', $customer_array);
		$SMARTY->assign('summaryInvoice', $totalInvoiceSummary);
		$SMARTY->assign('summaryPayment', $totalPaymentSummary);

		if (strtolower(ConfigHelper::getConfig('phpui.report_type')) == 'pdf') {
			$output = $SMARTY->fetch('printperiodicreport.html');
			html2pdf($output, trans('Reports'), $layout['pagetitle']);
		} else {
			$SMARTY->display('printperiodicreport.html');
		}
break;

default:
$layout['pagetitle'] = trans('Periodic report');

$SMARTY->assign('error', $error);
$SMARTY->display('periodicreport.html');
break;

}


