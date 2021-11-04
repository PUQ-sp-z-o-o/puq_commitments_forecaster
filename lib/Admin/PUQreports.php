<?php
/*
 * This file is part of the WHMCS module. "Commitments forecaster"
 * System for management and planning of outside and inside commitments.
 *
 * Author: Ruslan Poloviy ruslan.polovyi@puq.pl
 * PUQ sp. z o.o. www.puq.pl
 */

namespace WHMCS\Module\Addon\puq_commitments_forecaster\Admin;

class PUQreports
{
    function status($Commitments)
    {
        $status = array(
            'CounOfCommitments' => 0,
            'ActiveCommitments' => 0,
            'CountOfConterpartners' => 0,
            'CountOfInvoices' => 0,
            'Netto' => 0,
            'Brutto' => 0,
            'NettoAVGinvoice' => 0,
            'BruttoAVGinvoice' => 0,
            'NettoAVGmonth' => 0,
            'BruttoAVGmonth' => 0,
            'NettoEnd' => 0,
            'BruttoEnd' => 0,
        );

        $MinStartDate = '9999-12-31';
        $MaxEndDate = '0000-00-00';
        $invoices = array();
        foreach($Commitments as $commitment_key => $commitment){
            $status['CountOfCommitments']++;
            if($commitment['EndDate'] >= date("Y-m-d") or $commitment['IndefinitePeriod'] == 1){
                $status['ActiveCommitments']++;
            }

            if($commitment['IndefinitePeriod'] == 1){
                $MaxEndDate = date("Y-m-d");
            }

            if($MinStartDate > $commitment['StartDate']){
                $MinStartDate = $commitment['StartDate'];
            }

            if($MaxEndDate < $commitment['EndDate']){
                $MaxEndDate = $commitment['EndDate'];
            }

            $CountOfConterpartners[] = $commitment['counterparty_id'];
            $status['CountOfInvoices'] += $commitment['InvoicessCount'];
            $status['Netto'] += $commitment['Netto'];
            $status['Brutto'] += $commitment['Brutto'];
            $status['NettoEnd'] += $commitment['NettoEnd'];
            $status['BruttoEnd'] += $commitment['BruttoEnd'];
            $Duration += $commitment['Duration'];

            foreach($commitment['Invoices'] as $invoice_key => $invoice){
                $invoices[] = $invoice;
            }

        }

        if($MaxEndDate > date("Y-m-d") or $MaxEndDate == '0000-00-00'){
            $MaxEndDate = date("Y-m-d");
        }

        $status['CountOfConterpartners'] = count(array_unique($CountOfConterpartners));
        $status['NettoAVGinvoice'] = $status['Netto']/$status['CountOfInvoices'];
        $status['BruttoAVGinvoice'] = $status['Brutto']/$status['CountOfInvoices'];
        $duration = PUQcommitments::dt_diff($MinStartDate,$MaxEndDate);
        $status['NettoAVGmonth'] = $status['Netto']/$duration;
        $status['BruttoAVGmonth'] = $status['Brutto']/$duration;
        $status['MinStartDate'] = $MinStartDate;
        $status['MaxEndDate'] = $MaxEndDate;
        $status['MonthlysRange'] = PUQcommitments::monthlys_range($MinStartDate,$MaxEndDate);
        $status['Invoices'] = $invoices;

        return $status;
    }


}