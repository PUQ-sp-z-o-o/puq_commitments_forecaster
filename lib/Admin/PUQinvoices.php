<?php
/*
 * This file is part of the WHMCS module. "Commitments forecaster"
 * System for management and planning of outside and inside commitments.
 *
 * Author: Ruslan Poloviy ruslan.polovyi@puq.pl
 * PUQ sp. z o.o. www.puq.pl
 */

namespace WHMCS\Module\Addon\puq_commitments_forecaster\Admin;
use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQfiles;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQtags;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQcommitments;




class PUQinvoices
{

    function GetRange($InvoicesIdRange){
        $good = array();

        $invoices = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_invoices')
            ->leftjoin('puq_commitments_forecaster_counterparties', 'puq_commitments_forecaster_invoices.counterparty_id', '=', 'puq_commitments_forecaster_counterparties.id')
            ->leftjoin('puq_commitments_forecaster_commitments', 'puq_commitments_forecaster_invoices.CommitmentId', '=', 'puq_commitments_forecaster_commitments.id')

            ->whereIn('puq_commitments_forecaster_invoices.id', $InvoicesIdRange)

            ->select(
                'puq_commitments_forecaster_invoices.*',
                'puq_commitments_forecaster_counterparties.CompanyName',
                'puq_commitments_forecaster_commitments.Name as CommitmentName',
                'puq_commitments_forecaster_commitments.id as CommitmentID',
                'puq_commitments_forecaster_commitments.Description as CommitmentDescription')
            ->orderBy('puq_commitments_forecaster_invoices.DocumentDate', 'desc')
            ->get(), true));

        foreach($invoices as $key => $value)
        {
            $invoice =(array) $value;
            $good[] = $invoice;
        }

        $tags = PUQtags::GetAllForInvoices($InvoicesIdRange);
        foreach($good as $key => $value){
            foreach($tags as $key2 => $tag){
                if($tag['InvoiceId'] == $good[$key]['id']){
                    $good[$key]['tags'][] = $tag['name'];
                }
            }
        }

        return $good;
    }

    function GetRangeIdsPerDate($data){
        if(!array_key_exists('month',$data)){
            $StartDate = $data['year'] . '-01-' . '01';
            $StartDateTMP = date_create($StartDate);
            $StopDate =  date_format($StartDateTMP,'Y-12-31');
        }
        if(array_key_exists('month',$data) and array_key_exists('year',$data)) {
            $StartDate = $data['year'] . '-' . $data['month'] . '-' . '01';
            $StartDateTMP = date_create($StartDate);
            $StopDate = date_format($StartDateTMP, 'Y-m-t');
        }

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
        $RangePerCommitment = array();
        $invoices = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_invoices')
            ->where('DocumentDate', '>=', $StartDate)
            ->where('DocumentDate', '<=', $StopDate)
            ->select('puq_commitments_forecaster_invoices.id')
            ->get()), true);
        foreach($invoices as $key => $invoice) {
            $RangePerCommitment[] = $invoice['id'];
        }
        return $RangePerCommitment;
    }

    function GetRangeIdsPerCommitment($CommitmentId){
        $RangePerCommitment = array();
        $invoices = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_invoices')
            ->where('CommitmentId', $CommitmentId)
            ->select('puq_commitments_forecaster_invoices.id')
            ->get()), true);
        foreach($invoices as $key => $invoice) {
            $RangePerCommitment[] = $invoice['id'];
        }
        return $RangePerCommitment;
    }

    function GetRangeIdsPerCounterparty($CounterpartyId){
        $Range = array();
        $invoices = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_invoices')
            ->where('counterparty_id', $CounterpartyId)
            ->select('puq_commitments_forecaster_invoices.id')
            ->get()), true);
        foreach($invoices as $key => $invoice) {
            $Range[] = $invoice['id'];
        }
        return $Range;
    }

    function Add($CounterpartyId,$FileID){

        try {
            Capsule::table('puq_commitments_forecaster_invoices')->insert(
                        [
                            'file_id' => $FileID,
                            'counterparty_id' => $CounterpartyId,
                            'DocumentDate' => date("Y-m-d"),
                            'ReceiptDate' => date("Y-m-d")
                        ]
                    );
        } catch (\Exception $e){
            $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            return null;
        }

        foreach (Capsule::table('puq_commitments_forecaster_invoices')
                     ->where('file_id', $FileID)
                     ->get('id') as $value) {
            $id = json_decode(json_encode($value), true);
            return $id['id'];
        }
    }

    function Get($id)
    {
        $invoice = Capsule::table('puq_commitments_forecaster_invoices')->where('id', $id)->get();
        return json_decode(json_encode($invoice['0']), true);
    }

    function Update($data)
    {
        if(array_key_exists('id',$data)){

            PUQtags::SetForInvoices($data['id'],explode(",", $data['Tags']));

            try {
                foreach (Capsule::table('puq_commitments_forecaster_invoices')->where('id', $data['id'])->get() as $value) {
                    $file = json_decode(json_encode($value), true);
                     //if($file['DocumentDate'] != $data['DocumentDate']) {
                    $Y = explode("-", $data['DocumentDate']);
                    $new_dir = 'invoices/' . $Y['0'] . '/' . $Y['1'];
                    $files = new PUQfiles();
                    $files->Move($file['file_id'],$new_dir);
                    //}
                }

                Capsule::table('puq_commitments_forecaster_invoices')
                    ->where('id', $data['id'])
                    ->update(
                        [
                            'number' => $data['number'],
                            'netto' => $data['netto'],
                            'brutto' => $data['brutto'],
                            'vat' => $data['vat'],
                            'DocumentDate' => $data['DocumentDate'],
                            'ReceiptDate' => $data['ReceiptDate'],
                            'CommitmentId' => $data['CommitmentId']
                        ]
                    );

                $_SESSION['PUQmessage'] = ['success', 'The invoice has been updated.'];
            } catch (\Exception $e) {
                $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            }
        }

        if(array_key_exists('CreateNewCommitment',$data)) {
            PUQcommitments::InvoiceCreateNewCommitment($data);

        }

    }

    function ListOfDates(){
        $dates = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_invoices')
            ->select(Capsule::raw("DATE_FORMAT(DocumentDate, '%Y') AS 'Y',DATE_FORMAT(DocumentDate, '%m') AS 'M'"))->distinct()
            ->get(), true));


        $good = array();
        foreach($dates as $key => $value)
        {
            $value = (array) $value;
            $good[$value['Y']][] = $value['M'];
            sort($good[$value['Y']]);
        }
        ksort($good);
        return $good;
    }

    function Unmapping($InvoiceId){
        $invoice = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_invoices')
            ->select(
                'puq_commitments_forecaster_invoices.*'
            )
            ->where('puq_commitments_forecaster_invoices.id', '=', $InvoiceId)
            ->where('puq_commitments_forecaster_invoices.CommitmentId', '>', 0)
            ->get(), true));
        foreach($invoice as $key => $value)
        {
            $invoice[$key] = (array)$value;
        }
        if($invoice){
            $_SESSION['PUQmessage'] = ['error', "No success. The document has dependencies."];
            header('Location: addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoice_edit&id=' .$InvoiceId );
            return null;
        }


        Capsule::table('puq_commitments_forecaster_invoices')->where('puq_commitments_forecaster_invoices.id', '=', $InvoiceId)->delete();
        $_SESSION['PUQmessage'] = ['success', "Success. The document is unmapped."];
        header('Location: addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_list');
        return null;
    }






}
