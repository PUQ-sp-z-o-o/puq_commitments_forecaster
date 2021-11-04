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

class PUQcounterparties
{

    function GetAll()
    {
        $counterparties = Capsule::table('puq_commitments_forecaster_counterparties')
            ->orderBy('id', 'desc')
            ->get();
        return json_decode(json_encode($counterparties), true);
    }

    function Add($data){
        $counterparty = Capsule::table('puq_commitments_forecaster_counterparties')
            ->where('VATNumber', $data['VATNumber'])
            ->orWhere('CompanyName', $data['CompanyName'])
            ->get();
        if (json_decode(json_encode($counterparty), true)) {
            return ['error', 'The counterparty is already in the system.'];
        }
        try {
            Capsule::table('puq_commitments_forecaster_counterparties')->insert(
                [
                    'CompanyName' => mb_strtoupper($data['CompanyName']),
                    'VATNumber' => $data['VATNumber'],
                    'FirstName' => $data['FirstName'],
                    'LastName' => $data['LastName'],
                    'Email' => $data['Email'],
                    'PhoneNumber' => $data['PhoneNumber'],
                    'Address' => $data['Address'],
                    'City' => $data['City'],
                    'Region' => $data['Region'],
                    'Postcode' => $data['Postcode'],
                    'Country' => $data['Country'],
                ]
            );
            return ['success', 'The counterparty has been added.'];
        } catch (\Exception $e) {
            return ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
        }
    }

    function Get($id){
        $counterparty = Capsule::table('puq_commitments_forecaster_counterparties')->where('id', $id)->get();
        return json_decode(json_encode($counterparty['0']), true);
    }

    function Update($id, $data)
    {
        try {
            Capsule::table('puq_commitments_forecaster_counterparties')
                ->where('id', $id)
                ->update(
                    [
                        'FirstName' => $data['FirstName'],
                        'LastName' => $data['LastName'],
                        'Email' => $data['Email'],
                        'PhoneNumber' => $data['PhoneNumber'],
                        'Address' => $data['Address'],
                        'City' => $data['City'],
                        'Region' => $data['Region'],
                        'Postcode' => $data['Postcode'],
                        'Country' => $data['Country'],
                    ]
                );
            return ['success', 'The counterparty has been updated.'];
        } catch (\Exception $e) {
            return ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
        }
    }

}
