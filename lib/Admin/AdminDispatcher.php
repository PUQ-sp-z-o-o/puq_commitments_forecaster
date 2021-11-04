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

use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQcounterparties;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQinvoices;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQfiles;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQagreements;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQcost_groups;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQcommitments;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQtools;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQreports;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQincomes;


class AdminDispatcher
{
    public $error404 = 1;
    private $message = ['0', '', ''];
    private $data = array();
    private $version;
    private $commitments;

    function __construct($var)
    {
        global $vars;
        $vars = $var;
        $this->version = $var['version'];
        $this->commitments = new PUQcommitments();
    }

// START counterparties ================================================================================================

    function counterparties_list()
    {
        $this->data['counterparties'] = PUQcounterparties::GetAll();
        return $this->buildData();
    }

    function counterparties_add()
    {
        $counterparties = new PUQcounterparties();
        if (array_key_exists('VATNumber', $_POST)) {
            $this->message($counterparties->Add($_POST));
        }
        return $this->buildData();
    }

    function counterparties_edit()
    {
        $counterparties = new PUQcounterparties();
        if ($_POST) {
            $this->message($counterparties->Update($_GET['id'],$_POST));
        }

        $this->data['counterparty'] = $counterparties->Get($_GET['id']);
        $this->data['invoices'] = PUQinvoices::GetRange(PUQinvoices::GetRangeIdsPerCounterparty($_GET['id']));
        $this->data['agreements'] = PUQagreements::GetForCounterparties($_GET['id']);
        $this->data['commitments'] = $this->commitments->GetRange($this->commitments->GetRangeIdsPerCounterparty($_GET['id']));

        $this->data['status'] = PUQreports::status($this->data['commitments']);


        return $this->buildData();

    }

// END counterparties ==================================================================================================

// START documents =====================================================================================================

    function documents_add()
    {
        if(array_key_exists('id',$_POST) AND array_key_exists('file',$_FILES)) {
            $documents = new PUQdocuments();
            $documents->Add($_POST['id'],$_FILES['file']['tmp_name'],$_POST['DocumentType']);
        }

        $counterparties = new PUQcounterparties();
        $this->data['counterparty'] = $counterparties->Get($_GET['c_id']);
        return $this->buildData();
    }
    //==================================================================================================================

    function documents_invoices_list(){
        //$this->data['invoices'] = PUQinvoices::GetAll($_GET);
        $this->data['invoices'] =  PUQinvoices::GetRange(PUQinvoices::GetRangeIdsPerDate($_GET));

        $this->data['ListOfDates'] = PUQinvoices::ListOfDates();
        return $this->buildData();
    }

    function documents_invoice_edit()
    {
        $invoices = new PUQinvoices();
        if(array_key_exists('id',$_POST)){
            $invoices->Update($_POST);
        }

        if(array_key_exists('unmapping',$_GET)){
            $invoices->Unmapping($_GET['unmapping']);
        }

        $invoice = $invoices->Get($_GET['id']);
        $this->data['commitments'] = $this->commitments->GetRange($this->commitments->GetRangeIdsAll());

        if($invoice){
            $this->data['invoice'] = $invoice;
            $this->data['counterparty'] = PUQcounterparties::Get($invoice['counterparty_id']);
            $this->data['tags'] = PUQtags::GetForInvoices($_GET['id']);
            return $this->buildData();
        }
    }

    //==================================================================================================================

    function documents_agreements_list(){
        $this->data['agreements'] = PUQagreements::GetAll($_GET);
        $this->data['commitments'] = $this->commitments->GetRange($this->commitments->GetRangeIdsAll());
        $this->data['ListOfDates'] = PUQagreements::ListOfDates();

        return $this->buildData();
    }

    function documents_agreement_edit()
    {   $annexes = new PUQannexes();
        $agreements = new PUQagreements();
        if(array_key_exists('id',$_POST))
        {
            $agreements->Update($_POST);
        }
        if(array_key_exists('unmapping',$_GET)){
            $agreements->Unmapping($_GET['unmapping']);
        }

        $agreement = $agreements->Get($_GET['id']);
        if($agreement){
            $this->data['agreement'] = $agreement;
            $this->data['counterparty'] = PUQcounterparties::Get($agreement['counterparty_id']);
            $this->data['commitments'] = $this->commitments->GetRange($this->commitments->GetRangeIdsPerAgreement($_GET['id']));
            $this->data['annexes'] = $annexes->GetRange($annexes->GetRangeIdsPerAgreement($_GET['id']));
            return $this->buildData();
        }
    }

    //==================================================================================================================

    function documents_annex_edit()
    {
        $annexes = new PUQannexes();
        if(array_key_exists('id',$_POST))
        {
            $annexes->Update($_POST);
        }
        if(array_key_exists('unmapping',$_GET)){
            $annexes->Unmapping($_GET['unmapping']);
        }

        $annex = $annexes->GetRange(array($_GET['id']));
        if($annex){
            $this->data['annex'] = $annex[0];
            $this->data['counterparty'] = PUQcounterparties::Get($annex[0]['counterparty_id']);
            $this->data['agreements'] = PUQagreements::GetForCounterparties($annex[0]['counterparty_id']);
            return $this->buildData();
        }
    }

    function documents_annexes_list(){
        $this->data['annexes'] = PUQannexes::GetRange(PUQannexes::GetRangeIdsAll());
        return $this->buildData();
    }

    //==================================================================================================================

    function documents_unclassified_list(){
        $documents = new PUQdocuments();

        if($_FILES) {
            $this->data['uploads'] = $documents->MultiUpload($_FILES);
        }

        if(array_key_exists('delete',$_GET)){
            $documents->Delete($_GET['delete']);
        }

        $this->data['documents'] = $documents->GetNotClassified();

        return $this->buildData();

    }

    function documents_unclassified_edit(){
        $documents = new PUQdocuments();

        if(array_key_exists('delete',$_GET)){
            $documents->Delete($_GET['delete']);
        }

        if(array_key_exists('CounterpartyId',$_POST) AND array_key_exists('DocumentType',$_POST)) {
            $documents->Mapping($_POST['CounterpartyId'],$_GET['id'],$_POST['DocumentType']);
        }

        $document = $documents->Get($_GET['id']);
        if($document) {
            $this->data['documents'] = $documents->GetNotClassified();
            $this->data['document'] = $document;
            $this->data['counterparties'] = PUQcounterparties::GetAll();

            return $this->buildData();
        }

    }

// END documents =======================================================================================================

// START cost_groups ===================================================================================================

    function cost_groups_list()
    {
        $this->data['cost_groups'] = PUQcost_groups::GetAll();
        return $this->buildData();
    }

    function cost_groups_edit()
    {
        if(array_key_exists('id', $_POST)){
            $cost_groups = new PUQcost_groups();
            $cost_groups->Update($_POST);
        }

        if (array_key_exists('id',$_GET)) {
            $cost_groups = new PUQcost_groups();
            $cost_group = $cost_groups->Get($_GET['id']);
            if ($cost_group){
                $this->data['cost_groups'] = $cost_group;
                return $this->buildData();
            }
        }
    }

    function cost_groups_add(){
        if(array_key_exists('Name', $_POST)) {
            $CostGroups = new PUQcost_groups();
            $CostGroups->Add($_POST);
        }
        return $this->buildData();
    }
//END cost_groups ======================================================================================================

// START commitments ===================================================================================================

    function commitments_list(){
        $this->data['commitments'] = $this->commitments->GetRange($this->commitments->GetRangeIdsPerData($_GET));

        //$this->data['commitments'] = $this->commitments->GetWithCounterparty($_GET);
        $this->data['ListOfDates'] = $this->commitments->ListOfDates();

        return $this->buildData();
    }

    function commitments_edit()
    {

        if(array_key_exists('id', $_POST)){
            $this->commitments->Update($_POST);
        }

        if(array_key_exists('delete',$_GET)){
            $this->commitments->Delete($_GET['delete']);
        }

        if (array_key_exists('id',$_GET)) {
            $commitment = $this->commitments->GetRange(array($_GET['id']));

            if ($commitment){
                $this->data['commitments'] = $commitment[0];
                $this->data['counterparty'] = PUQcounterparties::Get($commitment[0]['counterparty_id']);
                $this->data['agreements'] = PUQagreements::GetForCommitment($commitment[0]['counterparty_id']);
                $this->data['invoices'] = $commitment[0]['Invoices'];
                $this->data['status'] = PUQreports::status($commitment);
                return $this->buildData();
            }
        }

    }

    function commitments_add(){
        if(array_key_exists('Name', $_POST)) {
            $commitments = new PUQcommitments();
            $commitments->Add($_POST);
        }
        return $this->buildData();
    }

// END commitments =====================================================================================================

// START tools =========================================================================================================

    function tools_reindex_commitment_dates(){
        $this->data['commitments'] = PUQtools::ReindexCommitmentDates();
        return $this->buildData();
    }

    function tools_reindex_agreements_dates(){
        $this->data['annexes'] = PUQtools::ReindexAgreementsDates();
        return $this->buildData();
    }

    function tools_synchronization_income_invoices(){
        $this->data['systems'] = PUQtools::SynchronizationIncomeInvoices();
        return $this->buildData();
    }

// END tools ===========================================================================================================
// START reports =======================================================================================================

    function reports_dashboard(){
        PUQtools::GetPUQstat();
        $this->data['ListOfDates'] = PUQinvoices::ListOfDates();
        $this->data['invoices'] =  PUQinvoices::GetRange(PUQinvoices::GetRangeIdsPerDate(array('year' =>date('Y'))));
        $this->data['IncomesInvoices'] = PUQincomes::GetInvoicesRange(PUQincomes::GetInvoicesRangeIdsPerDate(array('year' =>date('Y'))));
        $this->data['conf'] =PUQtools::GetConf();
        $this->data['version'] = $this->version;
        $this->data['status'] = PUQreports::status($this->commitments->GetRange($this->commitments->GetRangeIdsAll()));
        return $this->buildData();
    }

    function reports_about_us(){
        return $this->buildData();
    }

    function reports_invoices(){

        if(!array_key_exists('year',$_GET)) {
            header('Location: addonmodules.php?module=puq_commitments_forecaster&m=reports&action=invoices&year=all');
        }
        if(array_key_exists('year',$_GET)){
            $this->data['ListOfDates'] = PUQinvoices::ListOfDates();
            $this->data['invoices'] =  PUQinvoices::GetRange(PUQinvoices::GetRangeIdsPerDate($_GET));
            $this->data['IncomesInvoices'] = PUQincomes::GetInvoicesRange(PUQincomes::GetInvoicesRangeIdsPerDate($_GET));
            return $this->buildData();
        }
    }

// END reports =========================================================================================================

// START incomes =======================================================================================================

    function incomes_systems_list(){
        $incomes = new PUQincomes();

        if(array_key_exists('create',$_GET)){
            $incomes->Create($_GET['create']);
        }

        if(array_key_exists('delete',$_GET)){
            $incomes->Delete($_GET['delete']);
        }
        $this->data['systems'] = $incomes->GetSystemsRange($incomes->GetSystemsRangeIdsAll());
        return $this->buildData();
    }

    function incomes_apiWHMCS_edit(){
        $incomes = new PUQincomes();

        if(array_key_exists('sync',$_GET)) {
            $this->data['step'] = $incomes->apiWHMCS_Sync($_GET['id']);
        }


        if(array_key_exists('id',$_POST)){
            $incomes->Update($_POST);
        }

        if(array_key_exists('step',$_GET)) {
            if($_GET['step'] == 1){
                $incomes->DeleteInvoices($_GET['id']);
                unset($_SESSION['FullSyncWHMCS']);
            }
            $this->data['step'] = $incomes->FullSyncWHMCS($_GET['id'],$_GET['step']);
        }

        if(array_key_exists('id',$_GET)) {

            $system = $incomes->GetSystemsRange(array($_GET['id']))[0];
            if ($system) {
                if($system['Type'] == 'apiWHMCS') {
                    $this->data['system'] = $system;
                    $this->data['test'] = $incomes->apiWHMCS($system['URL'],$system['Username'],$system['Password'],'1','0');
                    return $this->buildData();
                }
            }
        }
    }

// END incomes =======================================================================================================

    function get_file($id){
        $files = new PUQfiles();
        $files->Get($id);
    }

    function message($msg)
    {

        $types = [
            'error' => '&#10006;',
            'info' => '&#8520;',
            'success' => '&#10003;'
        ];

        $this->message = [$msg[0], $types[$msg[0]], $msg[1]];
    }

    function buildData()
    {
        if(array_key_exists('PUQmessage', $_SESSION)){
            $this->message($_SESSION['PUQmessage']);
            unset($_SESSION['PUQmessage']);
        }

        $data = [
            'message' => $this->message,
            'data' => $this->data,
        ];
        $this->error404 = 0;
        return $data;
    }
}