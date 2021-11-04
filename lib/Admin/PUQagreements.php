<?php

namespace WHMCS\Module\Addon\puq_commitments_forecaster\Admin;

use WHMCS\Database\Capsule;

class PUQagreements
{
    function GetRange($AgreementsIdRange){
        $agreements = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_agreements')
            ->join('puq_commitments_forecaster_counterparties', 'puq_commitments_forecaster_agreements.counterparty_id', '=', 'puq_commitments_forecaster_counterparties.id')

            ->whereIn('puq_commitments_forecaster_agreements.id', $AgreementsIdRange)
            ->select(
                'puq_commitments_forecaster_agreements.*',
                'puq_commitments_forecaster_counterparties.CompanyName'
            )
            ->orderBy('puq_commitments_forecaster_agreements.CommitmentDate', 'desc')
            ->get(), true));

        foreach($agreements as $key => $value)
        {
            $good[] = (array) $value;
        }
        return $good;
    }

    function GetAll($data)
    {
        $year = date('Y');
        $month = [date('m'),date('m')];
        if(array_key_exists('year',$data)) {
            $year = $data['year'];
            $month = [1,12];
            if (array_key_exists('month', $data)) {
                $month = [$data['month'],$data['month']];

            }
        }

        $agreements = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_agreements')
            ->join('puq_commitments_forecaster_counterparties', 'puq_commitments_forecaster_agreements.counterparty_id', '=', 'puq_commitments_forecaster_counterparties.id')
            ->select(
                'puq_commitments_forecaster_agreements.*',
                'puq_commitments_forecaster_counterparties.CompanyName',
                Capsule::raw("YEAR(ConclusionDate) as year"),
                Capsule::raw("MONTH(ConclusionDate) as month")
            )
            ->orderBy('puq_commitments_forecaster_agreements.CommitmentDate', 'desc')
            ->get()
            ->where('year', $year)
            ->whereBetween('month', $month), true));

        foreach($agreements as $key => $value)
        {
            $good[] = (array) $value;
        }
        return $good;
    }

    function Add($CounterpartyId,$FileID){

        try {
            Capsule::table('puq_commitments_forecaster_agreements')->insert(
                [
                    'file_id' => $FileID,
                    'counterparty_id' => $CounterpartyId,
                    'ConclusionDate' => date("Y-m-d"),
                    'CommitmentDate' => date("Y-m-d")
                ]
            );
        } catch (\Exception $e){
            $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            return null;
        }

        foreach (Capsule::table('puq_commitments_forecaster_agreements')
                     ->where('file_id', $FileID)
                     ->get('id') as $value) {
            $id = json_decode(json_encode($value), true);
            return $id['id'];
        }
    }

    function Get($id)
    {
        $agreement = Capsule::table('puq_commitments_forecaster_agreements')->where('id', $id)->get();
        return json_decode(json_encode($agreement['0']), true);
    }

    function Update($data)
    {
        if(array_key_exists('id',$data)){

            try {
                foreach (Capsule::table('puq_commitments_forecaster_agreements')->where('id', $data['id'])->get() as $value) {
                    $file = json_decode(json_encode($value), true);
                    //if($file['ConclusionDate'] != $data['ConclusionDate']) {
                    $Y = explode("-", $data['ConclusionDate']);
                    $new_dir = 'agreements/' . $Y['0'] . '/' . $Y['1'];
                    $files = new PUQfiles();
                    $files->Move($file['file_id'],$new_dir);
                    //}
                }

                $IndefinitePeriod = 0;
                if (array_key_exists('IndefinitePeriod',$data)){
                    $IndefinitePeriod = 1;
                }

                Capsule::table('puq_commitments_forecaster_agreements')
                    ->where('id', $data['id'])
                    ->update(
                        [
                            'Number' => $data['Number'],
                            'Description' => $data['Description'],
                            'ConclusionDate' => $data['ConclusionDate'],
                            'CommitmentDate' => $data['CommitmentDate'],
                            'ExpiryDate' => $data['ExpiryDate'],
                            'NoticePeriod' =>$data['NoticePeriod'],
                            'IndefinitePeriod' => $IndefinitePeriod,
                        ]
                    );

                $_SESSION['PUQmessage'] = ['success', 'The agreement has been updated.'];
            } catch (\Exception $e) {
                $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            }
        }
    }

    function GetForCommitment($CounterpartyId)
    {
        if($CounterpartyId == '' or $CounterpartyId == '0'){
            $where = '>';
            $CounterpartyId = 0;
        }else{
            $where = '=';
        }
        $agreements = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_agreements')
            ->join('puq_commitments_forecaster_counterparties', 'puq_commitments_forecaster_agreements.counterparty_id', '=', 'puq_commitments_forecaster_counterparties.id')
            ->select('puq_commitments_forecaster_agreements.*', 'puq_commitments_forecaster_counterparties.CompanyName')
            ->where('counterparty_id', $where, $CounterpartyId)
            ->orderBy('CommitmentDate', 'desc')

            ->get(), true));

        foreach($agreements as $key => $value)
        {
            $agreements[$key] = (array)$value;
        }
        if ($agreements) {
            return $agreements;
        }
        return null;
    }

    function GetForCounterparties($counterparty_id)
    {
        if($counterparty_id == '' or $counterparty_id == '0'){
            $where = '>';
            $counterparty_id = 0;
        }else{
            $where = '=';
        }
        $agreements = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_agreements')
            //->leftjoin('puq_commitments_forecaster_commitments', 'puq_commitments_forecaster_agreements.id', '=', 'puq_commitments_forecaster_commitments.AgreementId')
            ->select(
                'puq_commitments_forecaster_agreements.*'
                //'puq_commitments_forecaster_commitments.id as CommitmentId',
                //'puq_commitments_forecaster_commitments.Name as CommitmentName',
                //'puq_commitments_forecaster_commitments.Description as CommitmentDescription'
            )
            ->where('counterparty_id', $where, $counterparty_id)
            ->orderBy('CommitmentDate', 'desc')

            ->get(), true));

        foreach($agreements as $key => $value)
        {
            $agreements[$key] = (array)$value;
        }
        if ($agreements) {
            return $agreements;
        }
        return null;
    }

    function ListOfDates(){
        $dates = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_agreements')
            ->select(Capsule::raw("DATE_FORMAT(ConclusionDate, '%Y') AS 'Y',DATE_FORMAT(ConclusionDate, '%m') AS 'M'"))->distinct()
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

    function Unmapping($AgreementId){
        $commitments = json_decode(json_encode( Capsule::table('puq_commitments_forecaster_commitments')
            ->select(
                'puq_commitments_forecaster_commitments.*'
            )
            ->where('puq_commitments_forecaster_commitments.AgreementId', '=', $AgreementId)
            ->get(), true));
        foreach($commitments as $key => $value)
        {
            $commitments[$key] = (array)$value;
        }
        if($commitments){
            $_SESSION['PUQmessage'] = ['error', "No success. The document has dependencies."];
            header('Location: addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreement_edit&id=' .$AgreementId );
            return null;
        }


        Capsule::table('puq_commitments_forecaster_agreements')->where('puq_commitments_forecaster_agreements.id', '=', $AgreementId)->delete();
        $_SESSION['PUQmessage'] = ['success', "Success. The document is unmapped."];
        header('Location: addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_list');
        return null;
    }

    function UpdateDates($data)
    {
        if(array_key_exists('id',$data)){

            try {
                Capsule::table('puq_commitments_forecaster_agreements')
                    ->where('id', $data['id'])
                    ->update(
                        [
                            'ExpiryDate' => $data['ExpiryDate'],
                            'NoticePeriod' => $data['NoticePeriod'],
                            'IndefinitePeriod' => $data['IndefinitePeriod'],
                        ]
                    );
            } catch (\Exception $e) {
                $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
            }
        }
    }

}