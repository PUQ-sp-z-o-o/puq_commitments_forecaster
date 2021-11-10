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
use WHMCS\Database\Capsule;

class PUQincomes
{

    function GetSystemsRange($SystemsIdRange){

        $systems = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_income_systems')
            ->whereIn('puq_commitments_forecaster_income_systems.id', $SystemsIdRange)
            ->select(
                'puq_commitments_forecaster_income_systems.*'
            )
            ->orderBy('puq_commitments_forecaster_income_systems.id', 'desc')
            ->get(), true));

        foreach($systems as $key => $system)
        {
            $systems[$key] = (array) $system;
            $systems[$key]['status'] = $this->InvoicesStatus($systems[$key]['id']);
        }

        return $systems;
    }

    function GetSystemsRangeIdsAll(){
        $Range = array();
        $systems = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_income_systems')
            ->select(
                'puq_commitments_forecaster_income_systems.id'
            )
            ->get(), true));
        foreach($systems as $key => $system) {
            $systems[$key] = (array)$system;
            $Range[] = $systems[$key]['id'];
        }
        return $Range;
    }

    function InvoicesStatus($SystemId){
        $invoices = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_income_invoices')
            ->leftJoin('puq_commitments_forecaster_income_systems', 'puq_commitments_forecaster_income_systems.id', '=', 'puq_commitments_forecaster_income_invoices.SystemId')

            ->where('puq_commitments_forecaster_income_invoices.SystemId', $SystemId)
            ->select(
                Capsule::raw('COUNT(puq_commitments_forecaster_income_invoices.id) as count'),
                'puq_commitments_forecaster_income_systems.ExchangeRate as ExchangeRate',
                Capsule::raw('SUM(Netto)*ExchangeRate as Netto'),
                Capsule::raw('SUM(Brutto)*ExchangeRate as Brutto')
            )
            ->get()
            , true));

        foreach($invoices as $key => $invoice)
        {
            $status = (array) $invoice;
        }
        return $status;
    }

    function FullSyncWHMCS($SystemId,$step)
    {
        $system = $this->GetSystemsRange(array($SystemId))[0];
        $inv = $this->apiWHMCS($system['URL'], $system['Username'], $system['Password'], $system['Step'], $system['Step'] * ($step - 1));

        if ($inv['result'] != 'success') {
            $_SESSION['FullSyncWHMCS'][$step] = array(
                'status' => "NOT OK"
            );
            return null;
        }

        $invoices = $inv['invoices']['invoice'];
        $_SESSION['FullSyncWHMCS'][$step]['sync'] = 0;
        foreach ($invoices as $key => $invoice){
            Capsule::table('puq_commitments_forecaster_income_invoices')->insert(
                [
                    'SystemId' => $SystemId,
                    'InSystemID' => $invoice['id'],
                    'InvoiceNum' => $invoice['invoicenum'],
                    'InvoiceDate' => $invoice['date'],
                    'Netto' => $invoice['subtotal'],
                    'Brutto' => $invoice['subtotal']+$invoice['tax'],
                    'VAT' => $invoice['tax'],
                    'taxrate' => $invoice['taxrate'],
                    'currencycode' => $invoice['currencycode'],
                    'currencyprefix' => $invoice['currencyprefix'],
                    'currencysuffix' => $invoice['currencysuffix']
                ]
            );
            $_SESSION['FullSyncWHMCS'][$step]['sync']++;
        }
        $_SESSION['FullSyncWHMCS'][$step]['status'] = 'OK';

        $this->UpdateLastSync($SystemId);
        return 'OK';
    }

    function apiWHMCS_Sync($SystemId)
    {
        $system = $this->GetSystemsRange(array($SystemId))[0];
        $inv = $this->apiWHMCS($system['URL'], $system['Username'], $system['Password'], 1, 0);

        if ($inv['result'] != 'success') {
            $_SESSION['PUQmessage'] = ['error', "API connection problem"];
            return 0;
        }
        if($system['status']['count'] >= $inv['totalresults']){
            $_SESSION['PUQmessage'] = ['error', "Nothing to sync"];
            return 0;
        }
        $totalresults = $inv['totalresults'];
        $i = 0;
        while ($this->GetSystemsRange(array($SystemId))[0]['status']['count'] < $totalresults) {
            $inv = $this->apiWHMCS($system['URL'], $system['Username'], $system['Password'], $system['Step'], $system['Step']*$i);
            $invoices = $inv['invoices']['invoice'];
            foreach ($invoices as $key => $invoice){
                Capsule::table('puq_commitments_forecaster_income_invoices')->updateOrInsert(
                    [
                        'SystemId' => $SystemId,
                        'InSystemID' => $invoice['id'],
                    ],
                    [
                        'InvoiceNum' => $invoice['invoicenum'],
                        'InvoiceDate' => $invoice['date'],
                        'Netto' => $invoice['subtotal'],
                        'Brutto' => $invoice['subtotal']+$invoice['tax'],
                        'VAT' => $invoice['tax'],
                        'taxrate' => $invoice['taxrate'],
                        'currencycode' => $invoice['currencycode'],
                        'currencyprefix' => $invoice['currencyprefix'],
                        'currencysuffix' => $invoice['currencysuffix']
                    ]
                );
            }
            $i++;
        }
        $_SESSION['PUQmessage'] = ['success', 'Synchronization was successful'];
        $this->UpdateLastSync($SystemId);
        return $this->GetSystemsRange(array($SystemId))[0]['status']['count'];
    }

    function apiWHMCS($url,$login,$password,$limitnum,$limitstart){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(
                array(
                    'action' => 'GetInvoices',
                    'username' => $login,
                    'password' => $password,
                    'orderby' => 'id',
                    'responsetype' => 'json',
                    'status' => 'Paid',
                    'limitnum' => $limitnum,
                    'limitstart' => $limitstart,
                    'order' => 'desc'
                )
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $good = curl_exec($ch);
        curl_close($ch);
        return json_decode($good,true);
    }

    function DeleteInvoices($SystemId){
        Capsule::table('puq_commitments_forecaster_income_invoices')->where('puq_commitments_forecaster_income_invoices.SystemId', '=', $SystemId)->delete();
    }

    function UpdateLastSync($SystemId){
        Capsule::table('puq_commitments_forecaster_income_systems')
            ->where('puq_commitments_forecaster_income_systems.id', '=', $SystemId)
            ->update([
                'LastSyncDate' => date('Y-m-d H:i:s')
                ]);
    }

    function Update($data)
    {
        if(array_key_exists('id',$data)){

            if ($data['ExchangeRate'] < 0){$data['ExchangeRate'] = 0;}
            if (array_key_exists('AutomaticSync', $data)) {$data['AutomaticSync'] = 1;}


            try {
                Capsule::table('puq_commitments_forecaster_income_systems')
                    ->where('id', $data['id'])
                    ->update(
                        [
                            'Name' => $data['Name'],
                            'URL' => $data['URL'],
                            'Username' => $data['Username'],
                            'Password' => $data['Password'],
                            'APIkey1' => $data['APIkey1'],
                            'APIkey2' => $data['APIkey2'],
                            'APIkey3' => $data['APIkey3'],
                            'AutomaticSync' => $data['AutomaticSync'],
                            'ExchangeRate' => $data['ExchangeRate'],
                            'Description' => $data['Description'],
                            'Step' => $data['Step'],
                        ]
                    );

                $_SESSION['PUQmessage'] = ['success', 'The system has been updated.'];
            } catch (\Exception $e) {
                $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            }
        }
    }

    function Delete($SystemId){
        $this->DeleteInvoices($SystemId);
        Capsule::table('puq_commitments_forecaster_income_systems')->where('puq_commitments_forecaster_income_systems.id', '=', $SystemId)->delete();
        $_SESSION['PUQmessage'] = ['success', 'Deleted.'];
    }

    function GetInvoicesRange($InvoicesIdRange){
        $good = array();

        $invoices = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_income_invoices')
            ->Join('puq_commitments_forecaster_income_systems', 'puq_commitments_forecaster_income_systems.id', '=', 'puq_commitments_forecaster_income_invoices.SystemId')

            ->whereIn('puq_commitments_forecaster_income_invoices.id', $InvoicesIdRange)
            ->select(
                'puq_commitments_forecaster_income_systems.ExchangeRate as ExchangeRate',
                'puq_commitments_forecaster_income_invoices.*',
                Capsule::raw('puq_commitments_forecaster_income_invoices.Netto*ExchangeRate as Netto'),
                Capsule::raw('puq_commitments_forecaster_income_invoices.Brutto*ExchangeRate as Brutto'),
                Capsule::raw('puq_commitments_forecaster_income_invoices.VAT*ExchangeRate as VAT')
            )
            ->orderBy('puq_commitments_forecaster_income_invoices.InvoiceDate', 'desc')
            ->get(), true));

        foreach($invoices as $key => $value)
        {
            $invoice =(array) $value;
            $good[$key] = $invoice;
        }
        return $good;
    }

    function GetInvoicesRangeIdsPerDate($data){
        /*
        if(!array_key_exists('month',$data)){
            $StartDate = $data['year'] . '-01-' . '01';
            $StartDateTMP = date_create($StartDate);
            $StopDate =  date_format($StartDateTMP,'Y-12-31');
        }
*/
        if(array_key_exists('month',$data) and array_key_exists('year',$data)){
            $StartDate = $data['year'] . '-' . $data['month'] . '-' . '01';
            $StartDateTMP = date_create($StartDate);
            $StopDate = date_format($StartDateTMP, 'Y-m-t');
        }

        if(!array_key_exists('month',$data)){
            $StartDate = $data['year'] . '-01-' . '01';
            $StartDateTMP = date_create($StartDate);
            $StopDate =  date_format($StartDateTMP,'Y-12-31');
        }

        if($data['year'] == 'all'){
            $StartDate = date_create('0001-01-01');
            $StopDate =  date_create('9999-12-31');
        }


        $Range = array();
        $invoices = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_income_invoices')
            ->where('InvoiceDate', '>=', $StartDate)
            ->where('InvoiceDate', '<=', $StopDate)
            ->select('puq_commitments_forecaster_income_invoices.id')
            ->get()), true);
        foreach($invoices as $key => $invoice) {
            $Range[] = $invoice['id'];
        }
        return $Range;
    }

    function Create($type){
        $datetime = date("Y-m-d H:i:s");
        try {
            Capsule::table('puq_commitments_forecaster_income_systems')->insert(
                [
                    'Name' => 'NEW '.$datetime,
                    'Description' => 'NEW '.$datetime,
                    'Type' => $type
                ]
            );
            $_SESSION['PUQmessage'] = ['success', 'Added.'];
        } catch (\Exception $e) {
            $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            return null;
        }
        foreach (Capsule::table('puq_commitments_forecaster_income_systems')
                     ->where('Name', 'NEW '.$datetime)
                     ->where('Description', 'NEW '.$datetime)
                     ->where('Type', $type)
                     ->get('id') as $value) {
            $expense = json_decode(json_encode($value), true);
            header('Location: addonmodules.php?module=puq_commitments_forecaster&m=incomes&action='.$type.'_edit&id='.$expense['id']);
            die();
        }
    }


}