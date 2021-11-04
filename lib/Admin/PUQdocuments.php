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
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQinvoices;


class PUQdocuments
{

    function MultiUpload($data){
        $files = new PUQfiles();
        $good = array();
        foreach ($data['file']['tmp_name'] as $key => $value){
            $id = $files->Add('raw/upload', $value);
            if($id) {
                $good[$data['file']['name'][$key]] = $id;
            }else{
                $good[$data['file']['name'][$key]] = '0';
            }
        }
        return $good;
    }

    function GetNotClassified(){
        $files = new PUQfiles();

        $documents = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_files')
            ->leftJoin('puq_commitments_forecaster_invoices', 'puq_commitments_forecaster_invoices.file_id', '=', 'puq_commitments_forecaster_files.id')
            ->leftJoin('puq_commitments_forecaster_agreements', 'puq_commitments_forecaster_agreements.file_id', '=', 'puq_commitments_forecaster_files.id')
            ->leftJoin('puq_commitments_forecaster_annexes', 'puq_commitments_forecaster_annexes.file_id', '=', 'puq_commitments_forecaster_files.id')
            ->select(
                'puq_commitments_forecaster_files.*',
                'puq_commitments_forecaster_invoices.id as InvoiceId',
                'puq_commitments_forecaster_agreements.id as AgreementId',
                'puq_commitments_forecaster_annexes.id as AnnexId'
            )
            ->whereNull('puq_commitments_forecaster_invoices.id')
            ->whereNull('puq_commitments_forecaster_agreements.id')
            ->whereNull('puq_commitments_forecaster_annexes.id')
            ->orderBy('id', 'desc')
            ->get()
            , true));

        foreach($documents as $key => $value)
        {
            $documents[$key] = (array)$value;
            if (stristr($documents[$key]['file'], 'raw/upload') === FALSE) {
                $files->Move($documents[$key]['id'],'raw/upload');
            }
        }
        if ($documents) {
            return $documents;
        }
        return null;

    }

    function Get($DocumentId){
        $documents = $this->GetNotClassified();
        foreach($documents as $key => $document){
            if ($DocumentId == $document['id']){
                return $document;
            }
        }
        //$_SESSION['PUQmessage'] = ['error', "The file is already classified or does not exist."];
        //header('Location: addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_list');
        //$_SESSION['PUQmessage'] = ['error', "The file is already classified or does not exist."];
        return null;
    }

    function Add($CounterpartyId,$FileTmpName,$DocumentType)
    {
        $files = new PUQfiles();
        $FileId = $files->Add($DocumentType . 's/' . date("Y/m"), $FileTmpName);

        if($FileId) {
            $this->Mapping($CounterpartyId, $FileId, $DocumentType);
        }
    }

    function Delete($id){
        $files = new PUQfiles();

        $documents = $this->GetNotClassified();
        foreach ($documents as $key => $value) {
            if($value['id'] == $id){
                $files->Move($id,'raw/delete');
                Capsule::table('puq_commitments_forecaster_files')->where('puq_commitments_forecaster_files.id', '=', $id)->delete();
                $_SESSION['PUQmessage'] = ['success', 'The document has been deleted!'];
                return null;
            }
        }
        $_SESSION['PUQmessage'] = ['error', 'Document not deleted!'];
        return null;
    }

    function Mapping($CounterpartyId,$FileId,$DocumentType){

        if ($FileId) {
            $id = '';
            // invoice
            if ($DocumentType == 'invoice') {
                $invoices = new PUQinvoices();
                $id = $invoices->Add($CounterpartyId, $FileId);
            }
            // agreement
            if ($DocumentType == 'agreement') {
                $agreements = new PUQagreements();
                $id = $agreements->Add($CounterpartyId, $FileId);
            }
            // annex
            if ($DocumentType == 'annex') {
                $annexes = new PUQannexes();
                $id = $annexes->Add($CounterpartyId, $FileId);
            }
            ////////////////////////////////////////////////////
            if ($id != '') {
                $_SESSION['PUQmessage'] = ['success', 'Document added.'];
                header('Location: addonmodules.php?module=puq_commitments_forecaster&m=documents&action=' . $DocumentType . '_edit&id=' . $id);
                die();
            }
            $_SESSION['PUQmessage'] = ['error', 'Document not added!'];
        }

    }

}