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


class PUQannexes
{
    function GetRange($AnnexesIdRange){
        $annexes = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_annexes')
            ->join('puq_commitments_forecaster_counterparties', 'puq_commitments_forecaster_annexes.counterparty_id', '=', 'puq_commitments_forecaster_counterparties.id')
            ->leftjoin('puq_commitments_forecaster_agreements', 'puq_commitments_forecaster_annexes.AgreementId', '=', 'puq_commitments_forecaster_agreements.id')

            ->whereIn('puq_commitments_forecaster_annexes.id', $AnnexesIdRange)
            ->select(
                'puq_commitments_forecaster_annexes.*',
                'puq_commitments_forecaster_counterparties.CompanyName',
                'puq_commitments_forecaster_agreements.Number as AgreementNumber',
                'puq_commitments_forecaster_agreements.Description as AgreementDescription'
            )
            ->orderBy('puq_commitments_forecaster_annexes.CommitmentDate', 'desc')
            ->get(), true));

        foreach($annexes as $key => $annex)
        {
            $good[] = (array) $annex;
        }
        return $good;
    }

    function GetRangeIdsAll(){
        $Range = array();
        $annexes = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_annexes')
            ->select(
                'puq_commitments_forecaster_annexes.id'
            )
            ->get(), true));
        foreach($annexes as $key => $annex) {
            $annexes[$key] = (array)$annex;
            $Range[] = $annexes[$key]['id'];
        }
        return $Range;
    }

    function GetRangeIdsPerAgreement($AgreementId){
        $Range = array();
        $annexes = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_annexes')
            ->where('puq_commitments_forecaster_annexes.AgreementId',$AgreementId)
            ->select(
                'puq_commitments_forecaster_annexes.id'
            )
            ->get(), true));
        foreach($annexes as $key => $annex) {
            $annexes[$key] = (array)$annex;
            $Range[] = $annexes[$key]['id'];
        }
        return $Range;
    }

    function Add($CounterpartyId,$FileID){

        try {
            Capsule::table('puq_commitments_forecaster_annexes')->insert(
                [
                    'file_id' => $FileID,
                    'AgreementId' => '0',
                    'counterparty_id' => $CounterpartyId,
                    'ConclusionDate' => date("Y-m-d"),
                    'CommitmentDate' => date("Y-m-d")
                ]
            );
        } catch (\Exception $e){
            $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            return null;
        }

        foreach (Capsule::table('puq_commitments_forecaster_annexes')
                     ->where('file_id', $FileID)
                     ->get('id') as $value) {
            $id = json_decode(json_encode($value), true);
            return $id['id'];
        }
    }

    function Update($data)
    {
        if(array_key_exists('id',$data)){

            try {
                foreach (Capsule::table('puq_commitments_forecaster_annexes')->where('id', $data['id'])->get() as $value) {
                    $file = json_decode(json_encode($value), true);
                    $Y = explode("-", $data['ConclusionDate']);
                    $new_dir = 'annexs/' . $Y['0'] . '/' . $Y['1'];
                    $files = new PUQfiles();
                    $files->Move($file['file_id'],$new_dir);
                }

                $IndefinitePeriod = 0;
                if (array_key_exists('AgreementIndefinitePeriod',$data)){
                    $IndefinitePeriod = 1;
                }

                $ChangeAgreement = 0;
                if (array_key_exists('ChangeAgreement',$data)){
                    $ChangeAgreement = 1;
                }

                Capsule::table('puq_commitments_forecaster_annexes')
                    ->where('id', $data['id'])
                    ->update(
                        [
                            'AgreementId' => $data['AgreementId'],
                            'Number' => $data['Number'],
                            'Description' => $data['Description'],
                            'ConclusionDate' => $data['ConclusionDate'],
                            'CommitmentDate' => $data['CommitmentDate'],
                            'AgreementExpiryDate' => $data['AgreementExpiryDate'],
                            'AgreementNoticePeriod' =>$data['AgreementNoticePeriod'],
                            'AgreementIndefinitePeriod' => $IndefinitePeriod,
                            'ChangeAgreement' => $ChangeAgreement
                        ]
                    );

                $_SESSION['PUQmessage'] = ['success', 'The annex has been updated.'];
            } catch (\Exception $e) {
                $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            }
        }
    }

    function Unmapping($AnnexId){
        $annex = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_annexes')
            ->select(
                'puq_commitments_forecaster_annexes.id'
            )
            ->where('puq_commitments_forecaster_annexes.AgreementId', '>', '0')
            ->where('puq_commitments_forecaster_annexes.id', '=', $AnnexId)
            ->get(), true));
        foreach($Annex as $key => $value)
        {
            $annex[$key] = (array)$value;
        }
        if($annex){
            $_SESSION['PUQmessage'] = ['error', "No success. The document has dependencies."];
            header('Location: addonmodules.php?module=puq_commitments_forecaster&m=documents&action=annex_edit&id=' .$AnnexId );
            return null;
        }


        Capsule::table('puq_commitments_forecaster_annexes')->where('puq_commitments_forecaster_annexes.id', '=', $AnnexId)->delete();
        $_SESSION['PUQmessage'] = ['success', "Success. The document is unmapped."];
        header('Location: addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_list');
        return null;
    }

}