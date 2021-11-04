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

use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQcommitments;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQinvoices;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQannexes;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQfiles;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQincomes;

class PUQtools
{

    function ReindexCommitmentDates(){
        $Commitments_obj = new PUQcommitments;
        $Commitments = $Commitments_obj->GetRange($Commitments_obj->GetRangeIdsAll());
        foreach($Commitments as $key => $Commitment)
        {
            $UpdateDates = $Commitments_obj->UpdateDates($Commitment['id']);
            $Commitments[$key]['NewStartDate'] = $UpdateDates['StartDate'];
            $Commitments[$key]['NewEndDate'] = $UpdateDates['EndDate'];
            $Commitments[$key]['NewIndefinitePeriod'] = $UpdateDates['IndefinitePeriod'];
        }
        return $Commitments;
    }

    function ReindexAgreementsDates(){
        $annexes_obj = new PUQannexes();
        $annexes = $annexes_obj->GetRange($annexes_obj->GetRangeIdsAll());
        foreach($annexes as $key => $annex)
        {
            $annexes[$key]['NewStartDate'] = '0';
            $annexes[$key]['NewEndDate'] = '0';
            $annexes[$key]['NewIndefinitePeriod'] = '0';


            if($annex['ChangeAgreement'] == '1' and
                $annex['CommitmentDate'] < date("Y-m-d") and
                $annex['AgreementId'] > 0
            ) {

                $annexes[$key]['NewExpiryDate'] = $annex['AgreementExpiryDate'];
                $annexes[$key]['NewNoticePeriod'] =  $annex['AgreementNoticePeriod'];
                $annexes[$key]['NewIndefinitePeriod'] =  $annex['AgreementIndefinitePeriod'];


                PUQagreements::UpdateDates(
                    array(
                        'id' => $annex['AgreementId'],
                        'ExpiryDate' => $annex['AgreementExpiryDate'],
                        'NoticePeriod' => $annex['AgreementNoticePeriod'],
                        'IndefinitePeriod' => $annex['AgreementIndefinitePeriod']
                    )
                );
            }
        }
        //print_r($annexes);
        return $annexes;
    }

    function SynchronizationIncomeInvoices(){
        $good = array();
        $incomes = new PUQincomes();
        $systems = $incomes->GetSystemsRange($incomes->GetSystemsRangeIdsAll());
        foreach($systems as $key => $system){
            $Sync = $system['Type'] . '_Sync';
            if($system['AutomaticSync'] == 1 and is_callable(array($incomes,  $Sync)) ) {

                $SynInvoices = $incomes->$Sync($system['id']);
                if($SynInvoices == 0){
                    $system['SynInvoices'] = 0;
                }else{
                    $system['SynInvoices'] =  $SynInvoices - $system['status']['count'];
                }
                $good[] = $system;
            }
        }
        return $good;
    }

    function Install(){
        global $CONFIG;
        $InstallId = $CONFIG['DefaultCountry']. '-' .date('Y-m-d') . '-' . rand(1000, 9999);

        Capsule::table('puq_commitments_forecaster_conf')->insert(
            [
                'name' => 'InstallId',
                'value' => $InstallId
            ]
        );

        Capsule::table('puq_commitments_forecaster_conf')->insert(
            [
                'name' => 'info',
                'value' => ''
            ]
        );
        Capsule::table('puq_commitments_forecaster_conf')->insert(
            [
                'name' => 'available_version',
                'value' => ''
            ]
        );


    }

    function GetPUQstat(){

        $sql = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_conf')->where('name','InstallId')->get(), true))[0];
        $InstallId = (array)$sql;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://cf.puq.info/whmcs-stat.php?installid=' . $InstallId['value']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $answer = curl_exec($curl);
        curl_close($curl);

        $info = json_decode($answer, true);
        if(json_last_error() < 0 or !$info){
            return null;
        }

        Capsule::table('puq_commitments_forecaster_conf')->where('name', 'info')->update(
            [
                'value' => $info['info']
            ]
        );
        Capsule::table('puq_commitments_forecaster_conf')->where('name', 'available_version')->update(
            [
                'value' => $info['available_version']
            ]
        );
    }

    function GetConf(){
        $conf = array();
        $sql = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_conf')->get(), true));
        foreach($sql as $key => $value) {
            $con = (array)$value;
            $conf[$con['name']] = $con['value'];
        }
        return $conf;
    }
}