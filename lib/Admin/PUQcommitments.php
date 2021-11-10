<?php
/*
 * This file is part of the WHMCS module. "Commitments forecaster"
 * System for management and planning of outside and inside commitments.
 *
 * Author: Ruslan Poloviy ruslan.polovyi@puq.pl
 * PUQ sp. z o.o. www.puq.pl
 * Poland
 */

namespace WHMCS\Module\Addon\puq_commitments_forecaster\Admin;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQcounterparties;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQinvoices;
use WHMCS\Database\Capsule;

class PUQcommitments
{
    function GetRange($CommitmentsIdRange){
        $agreements_obj = new PUQagreements();
        $agreements_id_range = array();

        $invoices_obj = new PUQinvoices();

        $commitments = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_commitments')

            ->leftJoin('puq_commitments_forecaster_agreements', 'puq_commitments_forecaster_commitments.AgreementId', '=', 'puq_commitments_forecaster_agreements.id')
            ->leftJoin('puq_commitments_forecaster_invoices','puq_commitments_forecaster_commitments.id', '=', 'puq_commitments_forecaster_invoices.CommitmentId')->distinct()
            ->leftjoin('puq_commitments_forecaster_counterparties', function($join)
            {
                $join->on('puq_commitments_forecaster_agreements.counterparty_id', '=', 'puq_commitments_forecaster_counterparties.id')
                    ->orOn('puq_commitments_forecaster_invoices.counterparty_id', '=', 'puq_commitments_forecaster_counterparties.id');
            })

            ->whereIn('puq_commitments_forecaster_commitments.id', $CommitmentsIdRange)
            ->select(
                'puq_commitments_forecaster_commitments.*',
                'puq_commitments_forecaster_counterparties.CompanyName',
                'puq_commitments_forecaster_counterparties.id as counterparty_id'
            )
            ->orderBy('puq_commitments_forecaster_commitments.StartDate', 'desc')
            ->get(), true));

        foreach($commitments as $key => $value)
        {
            $commitments[$key] = (array)$value;

            $agreements_id_range[] = $commitments[$key]['AgreementId'];
            $commitments[$key]['Agreement'] = array();

            $commitments[$key]['Invoices'] = array();

        }

        $agreements = $agreements_obj->GetRange($agreements_id_range);

        foreach($commitments as $commitment_key => $commitment)
        {

            foreach($agreements as $agreement_key => $agreement) {
                if($agreement['id'] == $commitment['AgreementId']) {
                    $commitments[$commitment_key]['Agreement'] = $agreement;
                }
            }

            $invoices = $invoices_obj->GetRange($invoices_obj->GetRangeIdsPerCommitment($commitment['id']));

            $commitments[$commitment_key]['Netto'] = 0;
            $commitments[$commitment_key]['Brutto'] = 0;
            $commitments[$commitment_key]['MismatchDates'] = 0;
            $commitments[$commitment_key]['InvoicessCount'] = count($invoices);

            foreach($invoices as $invoice_key => $invoice) {
                $invoices[$invoice_key]['MismatchDates'] = 0;
                if($commitment['EndDate'] < $invoice['DocumentDate'] and $commitment['IndefinitePeriod'] != '1') {
                    $commitments[$commitment_key]['MismatchDates'] = 1;
                    $invoices[$invoice_key]['MismatchDates'] = 1;
                }
                $commitments[$commitment_key]['Netto'] += $invoice['netto'];
                $commitments[$commitment_key]['Brutto'] += $invoice['brutto'];
            }
            $commitments[$commitment_key]['Invoices'] = $invoices;


            if ($commitment['IndefinitePeriod'] == '1'){

                if($commitment['EndDate'] == '0000-00-00' or $commitment['EndDate'] == null){
                    $commitment['EndDate'] = date("Y-m-d",strtotime( "-1 month", strtotime( date("Y-m-d") ) ));
                }

                if($this->dt_diff(date("Y-m-d"),$commitment['EndDate']) <= 13 ) {
                    $commitments[$commitment_key]['DurationLeft'] = '12';
                }else{
                    $commitments[$commitment_key]['DurationLeft'] = $this->dt_diff(date("Y-m-d"), $commitment['EndDate'])-2;
                }

                $commitments[$commitment_key]['Duration'] = $this->dt_diff($commitment['StartDate'], date("Y-m-d"));

            }else{
                $commitments[$commitment_key]['Duration'] = $this->dt_diff($commitment['StartDate'], $commitment['EndDate']);
                if($commitment['EndDate'] > date("Y-m-d")) {
                    $commitments[$commitment_key]['DurationLeft'] = $this->dt_diff(date("Y-m-d"), $commitment['EndDate']);
                }else{
                    $commitments[$commitment_key]['DurationLeft'] = '0';
                }
            }

            $commitments[$commitment_key]['NettoAVGinvoice'] = $commitments[$commitment_key]['Netto']/$commitments[$commitment_key]['InvoicessCount'];
            $commitments[$commitment_key]['BruttoAVGinvoice'] = $commitments[$commitment_key]['Brutto']/$commitments[$commitment_key]['InvoicessCount'];
            $commitments[$commitment_key]['NettoAVGmonth'] = $commitments[$commitment_key]['Netto']/$commitments[$commitment_key]['Duration'];
            $commitments[$commitment_key]['BruttoAVGmonth'] = $commitments[$commitment_key]['Brutto']/$commitments[$commitment_key]['Duration'];
            $commitments[$commitment_key]['NettoEnd'] = $commitments[$commitment_key]['NettoAVGmonth']*$commitments[$commitment_key]['DurationLeft'];
            $commitments[$commitment_key]['BruttoEnd'] = $commitments[$commitment_key]['BruttoAVGmonth']*$commitments[$commitment_key]['DurationLeft'];
            $commitments[$commitment_key]['MonthlysRange'] = $this->monthlys_range($commitment['StartDate'],date("Y-m-d"));



        }

        if ($commitments) {
            //print'<pre>';
            //print_r($commitments);
            //print'</pre>';
            return $commitments;
        }
        return null;
    }

    function GetRangeIdsAll(){
        $Range = array();
        $commitments = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_commitments')
            ->select(
                'puq_commitments_forecaster_commitments.id'
            )
            ->get(), true));
        foreach($commitments as $key => $commitment) {
            $commitments[$key] = (array)$commitment;
            $Range[] = $commitments[$key]['id'];
        }
        return $Range;
    }

    function GetRangeIdsPerData($data){
        $RangePerData = array();

        $year = date('Y');
        $ENDmonth = date('m');
        $STARTmonth = date('m');
        if(array_key_exists('year',$data)) {
            $year = $data['year'];
            $yearALL = $data['year'];
            $STARTmonth = "01";
            $ENDmonth = "12";

            if (array_key_exists('month', $data)) {
                $yearALL = '0';
                $ENDmonth = $data['month'];
                $STARTmonth = $data['month'];
            }
        }

        $commitments = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_commitments')
            ->select(
                'puq_commitments_forecaster_commitments.id'
            )

            ->where([
                ['puq_commitments_forecaster_commitments.StartDate', '<=', $year.'-'.$STARTmonth.'-31'],
                ['puq_commitments_forecaster_commitments.EndDate', '>=', $year.'-'.$ENDmonth.'-01']
            ])
            ->orwhere([
                ['puq_commitments_forecaster_commitments.StartDate', '<=', $year.'-'.$STARTmonth.'-01'],
                ['puq_commitments_forecaster_commitments.IndefinitePeriod', '=', '1']
            ])
            ->orwhere([
                ['puq_commitments_forecaster_commitments.StartDate', '>=', $yearALL.'-01-01'],
                ['puq_commitments_forecaster_commitments.StartDate', '<=', $yearALL.'-12-31'],
            ])
            ->orderBy('puq_commitments_forecaster_commitments.StartDate', 'desc')
            ->get(), true));


        foreach($commitments as $key => $commitment) {
            $commitments[$key] = (array)$commitment;
            $RangePerData[] = $commitments[$key]['id'];
        }
        return $RangePerData;

    }

    function GetRangeIdsPerCounterparty($CounterpartyId){
        $Range = array();
        $ids = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_commitments')
            ->leftJoin('puq_commitments_forecaster_agreements', 'puq_commitments_forecaster_commitments.AgreementId', '=', 'puq_commitments_forecaster_agreements.id')
            ->leftJoin('puq_commitments_forecaster_invoices','puq_commitments_forecaster_commitments.id', '=', 'puq_commitments_forecaster_invoices.CommitmentId')->distinct()

            ->leftjoin('puq_commitments_forecaster_counterparties', function($join)
            {
                $join->on('puq_commitments_forecaster_agreements.counterparty_id', '=', 'puq_commitments_forecaster_counterparties.id')
                    ->orOn('puq_commitments_forecaster_invoices.counterparty_id', '=', 'puq_commitments_forecaster_counterparties.id');
            })
            ->select(
                'puq_commitments_forecaster_commitments.id'
            )
            ->where('puq_commitments_forecaster_counterparties.id', '=', $CounterpartyId)
            ->orderBy('CommitmentDate', 'desc')
            ->get()), true);
        foreach($ids as $key => $id) {
            $Range[] = $id['id'];
        }
        return $Range;
    }

    function GetRangeIdsPerAgreement($AgreementId){
        $Range = array();
        $ids = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_commitments')
            ->select(
                'puq_commitments_forecaster_commitments.id'
            )
            ->where('puq_commitments_forecaster_commitments.AgreementId', '=', $AgreementId)
            ->orderBy('StartDate', 'desc')
            ->get()), true);
        foreach($ids as $key => $id) {
            $Range[] = $id['id'];
        }
        return $Range;
    }

    function Add($data)
    {
        if(!array_key_exists('StartDate',$data)){
            $data['StartDate'] = date("Y-m-d");
        }
        if(!array_key_exists('EndDate',$data)){
            $data['EndDate'] = date("Y-m-d");
        }


        if (array_key_exists('Name', $data)) {

            $IndefinitePeriod = 0;
            if (array_key_exists('IndefinitePeriod', $data)) {
                $IndefinitePeriod = 1;
            }
            foreach (Capsule::table('puq_commitments_forecaster_commitments')->where('Name', $data['Name'])->get('id') as $value) {
                $_SESSION['PUQmessage'] = ['error', 'An cost group with this name is already in the system.'];
                return null;
            }

            try {
                Capsule::table('puq_commitments_forecaster_commitments')->insert(
                    [
                        'Name' => $data['Name'],
                        'Description' => $data['Description'],
                        'StartDate' => $data['StartDate'],
                        'EndDate' => $data['EndDate'],
                        'IndefinitePeriod' => $IndefinitePeriod,
                    ]
                );
                $_SESSION['PUQmessage'] = ['success', 'Added.'];
            } catch (\Exception $e) {
                $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
                return null;
            }
            foreach (Capsule::table('puq_commitments_forecaster_commitments')
                         ->where('Name', $data['Name'])
                         ->where('Description', $data['Description'])
                         ->where('StartDate', $data['StartDate'])
                         ->where('EndDate', $data['EndDate'])
                         ->where('IndefinitePeriod', $IndefinitePeriod)
                         ->get('id') as $value) {
                $expense = json_decode(json_encode($value), true);
                header('Location: addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id='.$expense['id']);
                die();
            }
        }
    }

    function InvoiceCreateNewCommitment($data){


        $invoice = PUQinvoices::Get($data['id']);
        $counterparty = PUQcounterparties::Get($invoice['counterparty_id']);

        $name = $counterparty['CompanyName'] . ' (' . date("Y-m-d H:i:s") . ')';
        $description = $name;

        foreach (Capsule::table('puq_commitments_forecaster_commitments')->where('Name', $data['Name'])->get('id') as $value) {
            $_SESSION['PUQmessage'] = ['error', 'An cost group with this name is already in the system.'];
            return null;
        }

        try {
            Capsule::table('puq_commitments_forecaster_commitments')->insert(
                [
                    'Name' => $name,
                    'Description' => $description,
                    'StartDate' => date("Y-m-d"),
                    'EndDate' => date("Y-m-d"),
                    'IndefinitePeriod' => '0',
                ]
            );
            $_SESSION['PUQmessage'] = ['success', 'Added.'];
        } catch (\Exception $e) {
            $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            return null;
        }
        foreach (Capsule::table('puq_commitments_forecaster_commitments')
                     ->where('Name', $name)
                     ->where('Description', $description)
                     ->where('IndefinitePeriod', '0')
                     ->get('id') as $value) {
            $expense = json_decode(json_encode($value), true);

            Capsule::table('puq_commitments_forecaster_invoices')
                ->where('id', $data['id'])
                ->update(
                    [
                        'CommitmentId' => $expense['id']
                    ]
                );

            header('Location: addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id='.$expense['id']);
            die();
        }

    }

    function Update($data){
        $IndefinitePeriod = 0;
        if (array_key_exists('IndefinitePeriod',$data)){
            $IndefinitePeriod = 1;
        }
        try {
            Capsule::table('puq_commitments_forecaster_commitments')
                ->where('id', $data['id'])
                ->update(
                    [
                        'Name' => $data['Name'],
                        'Description' => $data['Description'],
                        'StartDate' => $data['StartDate'],
                        'EndDate' => $data['EndDate'],
                        'IndefinitePeriod' => $IndefinitePeriod,
                        'AgreementId' => $data['AgreementId']
                    ]
                );
            $_SESSION['PUQmessage'] = ['success', 'The commitment has been updated.'];
        } catch (\Exception $e) {
            $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
        }

        $this->UpdateDates($data['id']);
    }

    function UpdateDates($CommitmentId){
        $StartDate = '0000-00-00';
        $EndDate = '0000-00-00';
        $IndefinitePeriod = "0";

        $commitment = $this->GetRange(array($CommitmentId))[0];
        foreach (Capsule::table('puq_commitments_forecaster_agreements')->where('id', $commitment['AgreementId'])->get() as $value) {
            $agreement = json_decode(json_encode($value), true);
        }


        if($agreement) {
            $StartDate = $agreement['CommitmentDate'];
            $EndDate = $agreement['ExpiryDate'];
            $IndefinitePeriod = $agreement['IndefinitePeriod'];
        }else{
            foreach ( Capsule::table('puq_commitments_forecaster_invoices')
                          ->select(
                              Capsule::raw("MAX(DocumentDate) as maxDocumentDate"),
                              Capsule::raw("MIN(DocumentDate) as minDocumentDate"))->where('CommitmentId', '=', $commitment['id'] )
                          ->get() as $value) {
                $invoices = json_decode(json_encode($value), true);
                $StartDate = $invoices['minDocumentDate'];
                $EndDate = $invoices['maxDocumentDate'];
                $IndefinitePeriod = $commitment['IndefinitePeriod'];
            }
        }


        if($StartDate == $commitment['StartDate'] and $EndDate == $commitment['EndDate'] and $IndefinitePeriod == $commitment['IndefinitePeriod']){
            return array(
                'CommitmentId' => $CommitmentId,
                'StartDate' => '0',
                'EndDate' => '0',
                'IndefinitePeriod' => '0'
            );
        }


        try {
            Capsule::table('puq_commitments_forecaster_commitments')
                ->where('id', $CommitmentId)
                ->update(
                    [
                        'StartDate' => $StartDate,
                        'EndDate' => $EndDate,
                        'IndefinitePeriod' => $IndefinitePeriod,
                    ]
                );
        } catch (\Exception $e) {
            $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
        }

        return array(
            'CommitmentId' => $CommitmentId,
            'StartDate' => $StartDate,
            'EndDate' => $EndDate,
            'IndefinitePeriod' => $IndefinitePeriod
        );
    }

    function ListOfDates(){
        $dates = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_commitments')
            ->where('StartDate','<>','0000-00-00')
            ->select(
                Capsule::raw("DATE_FORMAT(MIN(StartDate), '%Y') AS 'StartDateY'"),
                Capsule::raw("DATE_FORMAT(MIN(StartDate), '%m') AS 'StartDateM'"),

                Capsule::raw("DATE_FORMAT(MAX(EndDate), '%Y') AS 'EndDateY'"),
                Capsule::raw("DATE_FORMAT(MAX(EndDate), '%m') AS 'EndDateM'")
            )->distinct()
            ->get(), true));

        $good = array();
        foreach($dates as $key => $value)
        {
            $value = (array) $value;
            for ($year = $value['StartDateY']; $year <= $value['EndDateY']+1; $year++) {
                $good[$year] = array(1,2,3,4,5,6,7,8,9,10,11,12);

                if($year == $value['StartDateY']){
                    foreach($good[$year] as $key2 => $item) {
                        if ($item < $value['StartDateM']) {
                            unset($good[$year][$key2]);
                        }
                    }
                }

                if($year == $value['EndDateY']+1){
                    foreach($good[$year] as $key2 => $item) {
                        if ($item > $value['EndDateM']) {
                            unset($good[$year][$key2]);
                        }
                    }
                }

            }
        }
        ksort($good);
        return $good;

    }

    function Delete($CommitmentId){
        $commitment = $this->GetRange(array($CommitmentId));
        print"<pre>";
        print_r($commitment);
        print"</pre>";

        if($commitment['counterparty_id'] != ''){
            $_SESSION['PUQmessage'] = ['error', "No success. The document has dependencies."];
            header('Location: addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=edit&id='.$CommitmentId);
            return null;
        }

        Capsule::table('puq_commitments_forecaster_commitments')->where('puq_commitments_forecaster_commitments.id', '=', $CommitmentId)->delete();
        $_SESSION['PUQmessage'] = ['success', "Success."];
        header('Location: addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=list');
    }


    function dt_diff($START,$STOP){
        if($START>$STOP){return '0';}
        $d1 = date_create($START);
        $d2 = date_create($STOP);

        $yr= date_format($d2,'Y') - date_format($d1,'Y');
        $mr = date_format($d2,'m') - date_format($d1,'m');
        if($yr > 0){
             return $yr*12 +$mr +1;
        }else{
            return $mr+1;
        }

    }

    function monthlys_range($START,$STOP){
        $START = date_create($START);
        $STOP = date_create($STOP);
        if($START>$STOP){return array();}

        $monthlys_range = array();
        $START = date_format($START,'Y-m-01');
        $STOP = date_format($STOP,'Y-m-t');
        //$STOP = date('Y-m-d',strtotime('+1 MONTH', strtotime($STOP)));

        while ($START < $STOP) {
            $monthlys_range[] = $START;
            $START = date('Y-m-d',strtotime('+1 MONTH', strtotime($START)));
        }
        return $monthlys_range;
    }
}