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

class PUQtags
{

    function GetAllForInvoices($InvoicesId){
        global $InvoicesIdg;
        $InvoicesIdg = $InvoicesId;
        $tags = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_invoices_tags')

            ->leftjoin('puq_commitments_forecaster_tags', function($join)
            { global $InvoicesIdg;
                $join->on('puq_commitments_forecaster_tags.id', '=', 'puq_commitments_forecaster_invoices_tags.TagId')
                    ->whereIn('puq_commitments_forecaster_invoices_tags.InvoiceId', $InvoicesIdg);
            })
            ->select('puq_commitments_forecaster_tags.name','puq_commitments_forecaster_invoices_tags.InvoiceId')
            ->where('puq_commitments_forecaster_tags.name', '<>', '')
            ->get(), true));

        foreach($tags as $key => $value) {
            $tags[$key] = (array)$value;
        }
        return $tags;
    }

    function GetForInvoices($InvoiceId){
        global $InvoiceIdg;
        $InvoiceIdg = $InvoiceId;
        $tags = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_tags')

            ->leftjoin('puq_commitments_forecaster_invoices_tags', function($join)
            { global $InvoiceIdg;
                $join->on('puq_commitments_forecaster_tags.id', '=', 'puq_commitments_forecaster_invoices_tags.TagId')
                    ->where('puq_commitments_forecaster_invoices_tags.InvoiceId', '=', $InvoiceIdg);
            })
            //
            ->select('puq_commitments_forecaster_tags.name','puq_commitments_forecaster_invoices_tags.InvoiceId as in')
            ->get(), true));

        foreach($tags as $key => $value) {
            $tags[$key] = (array)$value;
        }

        return $tags;
    }

    function SetForInvoices($InvoiceId,$TagList){

        foreach ($TagList as $key => $tag) {
            if($tag != '') {
                Capsule::table('puq_commitments_forecaster_tags')->updateOrInsert(['name' => $tag]);
            }
        }
        $tags = array();
        $sql = json_decode(json_encode(Capsule::table('puq_commitments_forecaster_tags')->whereIn('name', $TagList)->get(), true));
        foreach($sql as $key => $value){
            $tag = (array)$value;
            $tags[] = $tag['id'];
        }
        Capsule::table('puq_commitments_forecaster_invoices_tags')->where('InvoiceId', '=', $InvoiceId)->delete();
        foreach ($tags as $key => $tag) {
            Capsule::table('puq_commitments_forecaster_invoices_tags')->insert(['TagId' => $tag, 'InvoiceId' => $InvoiceId]);
        }
    }

}