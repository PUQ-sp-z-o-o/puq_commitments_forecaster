<?php
/*
 * This file is part of the WHMCS module. "Commitments forecaster"
 * System for management and planning of outside and inside commitments.
 *
 * Author: Ruslan Poloviy ruslan.polovyi@puq.pl
 * PUQ sp. z o.o. www.puq.pl
 * Poland
 */

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\AdminDispatcher;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQtools;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function puq_commitments_forecaster_config()
{
    return [
        'name' => 'Commitments forecaster',
        'description' => 'System for management and planning of outside and inside commitments.',
        'author' => 'PUQ sp. z o.o.',
        'language' => 'english',
        'version' => '0.2-betta',
        'fields' => [
            'webdav URL' => [
                'FriendlyName' => 'WebDAV URL HTTPS://',
                'Type' => 'text',
                'Size' => '450',
                'Default' => '',
                'Description' => 'NextCloud only',
            ],

            'webdav user' => [
                'FriendlyName' => 'WebDAV user',
                'Type' => 'text',
                'Size' => '30',
                'Default' => '',
                'Description' => '',
            ],

            'webdav password' => [
                'FriendlyName' => 'WebDAV password',
                'Type' => 'password',
                'Size' => '30',
                'Default' => '',
                'Description' => '',
            ],

	]
    ];
}

function puq_commitments_forecaster_activate()
{
    try {
        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_agreements',
                function ($table) {
                    $table->increments('id');
                    $table->string('Status', 100)->nullable();
                    $table->string('Number', 100)->nullable();
                    $table->string('Description', 2000)->nullable();
                    $table->date('ConclusionDate')->default('0000-00-00')->nullable();
                    $table->date('CommitmentDate')->default('0000-00-00')->nullable();
                    $table->date('ExpiryDate')->nullable();
                    $table->integer('IndefinitePeriod')->nullable();
                    $table->integer('NoticePeriod')->nullable();
                    $table->integer('file_id')->nullable();
                    $table->integer('counterparty_id')->nullable();
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_annexes',
                function ($table) {
                    $table->increments('id');
                    $table->string('Status', 100)->nullable();
                    $table->string('Number', 100)->nullable();
                    $table->string('Description', 2000)->nullable();
                    $table->date('ConclusionDate')->default('0000-00-00')->nullable();
                    $table->date('CommitmentDate')->default('0000-00-00')->nullable();
                    $table->date('AgreementExpiryDate')->nullable();
                    $table->integer('AgreementIndefinitePeriod')->nullable();
                    $table->integer('AgreementNoticePeriod')->nullable();
                    $table->integer('file_id')->nullable();
                    $table->integer('counterparty_id')->nullable();
                    $table->integer('AgreementId')->nullable();
                    $table->integer('ChangeAgreement')->nullable();
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_commitments',
                function ($table) {
                    $table->increments('id');
                    $table->string('Name', 100)->nullable();
                    $table->date('StartDate')->default('0000-00-00');
                    $table->date('EndDate')->default('0000-00-00');
                    $table->integer('IndefinitePeriod')->nullable();
                    $table->string('Description', 2000)->nullable();
                    $table->integer('AgreementId')->nullable();
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_cost_groups',
                function ($table) {
                    $table->increments('id');
                    $table->string('Name', 100)->nullable();
                    $table->date('StartDate')->default('0000-00-00');
                    $table->date('EndDate')->default('0000-00-00');
                    $table->integer('IndefinitePeriod')->nullable();
                    $table->string('Description', 2000)->nullable();
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_counterparties',
                function ($table) {
                    $table->increments('id');
                    $table->string('FirstName', 100)->nullable();
                    $table->string('LastName', 100)->nullable();
                    $table->string('CompanyName', 100)->nullable();
                    $table->string('VATNumber', 100)->nullable();
                    $table->string('Email', 100)->nullable();
                    $table->string('Address', 100)->nullable();
                    $table->string('City', 100)->nullable();
                    $table->string('Region', 100)->nullable();
                    $table->string('Postcode', 100)->nullable();
                    $table->string('Country', 100)->nullable();
                    $table->string('PhoneNumber', 100)->nullable();
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_files',
                function ($table) {
                    $table->increments('id');
                    $table->string('file', 200)->nullable();
                    $table->string('md5', 200)->nullable();
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_income_invoices',
                function ($table) {
                    $table->increments('id');
                    $table->integer('SystemId')->nullable();
                    $table->integer('InSystemID')->nullable();
                    $table->string('InvoiceNum', 100)->nullable();
                    $table->date('InvoiceDate')->nullable();
                    $table->decimal('Netto', $precision = 16, $scale = 2)->nullable();
                    $table->decimal('Brutto', $precision = 16, $scale = 2)->nullable();
                    $table->decimal('VAT', $precision = 16, $scale = 2)->nullable();
                    $table->decimal('taxrate', $precision = 16, $scale = 2)->nullable();
                    $table->string('currencycode', 200)->nullable();
                    $table->string('currencyprefix', 200)->nullable();
                    $table->string('currencysuffix', 200)->nullable();
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_income_systems',
                function ($table) {
                    $table->increments('id');
                    $table->string('Name', 100)->nullable();
                    $table->string('Type', 100)->nullable();
                    $table->string('URL', 200)->nullable();
                    $table->string('Username', 200)->nullable();
                    $table->string('Password', 200)->nullable();
                    $table->string('APIkey1', 200)->nullable();
                    $table->string('APIkey2', 200)->nullable();
                    $table->string('APIkey3', 200)->nullable();
                    $table->string('AutomaticSync', 200)->nullable();
                    $table->decimal('ExchangeRate', $precision = 16, $scale = 4)->nullable()->default('0');
                    $table->string('Description', 2000)->nullable();
                    $table->integer('Step')->nullable()->default('0');
                    $table->datetime('LastSyncDate')->default('0000-00-00 00:00:00');
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_invoices',
                function ($table) {
                    $table->increments('id');
                    $table->string('status', 100)->nullable();
                    $table->string('number', 100)->nullable();
                    $table->decimal('netto', $precision = 16, $scale = 2)->nullable();
                    $table->decimal('brutto', $precision = 16, $scale = 2)->nullable();
                    $table->decimal('vat', $precision = 16, $scale = 2)->nullable();
                    $table->date('DocumentDate')->default('0000-00-00')->nullable();
                    $table->date('ReceiptDate')->nullable();
                    $table->integer('file_id')->nullable();
                    $table->integer('counterparty_id')->nullable();
                    $table->integer('CommitmentId')->default('0')->nullable();
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_invoices_tags',
                function ($table) {
                    $table->increments('id');
                    $table->integer('InvoiceId')->nullable();
                    $table->integer('TagId')->nullable();
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_tags',
                function ($table) {
                    $table->increments('id');
                    $table->string('name', 100);
                }
            );

        Capsule::schema()
            ->create(
                'puq_commitments_forecaster_conf',
                function ($table) {
                    $table->increments('id');
                    $table->string('name', 100);
                    $table->text('value');
                }
            );

        PUQtools::Install();
        PUQtools::GetPUQstat();

        return [
            'status' => 'success',
            'description' => 'Module activated',
        ];
    } catch (\Exception $e) {
        return [
            'status' => "error",
            'description' => 'Unable to created DB: ' . $e->getMessage(),
        ];
    }
}

function puq_commitments_forecaster_deactivate(){

    try {
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_agreements');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_annexes');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_commitments');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_cost_groups');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_counterparties');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_files');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_income_invoices');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_income_systems');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_invoices');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_invoices_tags');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_tags');
        Capsule::schema()
            ->dropIfExists('puq_commitments_forecaster_conf');

        return [
            // Supported values here include: success, error or info
            'status' => 'success',
            'description' => 'Module deactivated.',
        ];
    } catch (\Exception $e) {
        return [
            // Supported values here include: success, error or info
            "status" => "error",
            "description" => "Unable to drop DB: {$e->getMessage()}",
        ];
    }
}

function puq_commitments_forecaster_output($vars)
{
    //PUQtools::GetPUQstat();
    $m = isset($_REQUEST['m']) ? $_REQUEST['m'] : '';
    $action = $_GET['action'];

    $dispatcher = new AdminDispatcher($vars);


    // GET FILE
    if ($_GET['m'] == 'get_file') {
        $dispatcher->get_file($_GET['id']);
        exit;
    }
    //
    $smarty = new Smarty();
    $smarty->assign('dir', dirname(__FILE__));
    $smarty->caching = false;
    $smarty->display(dirname(__FILE__) . '/templates/css.tpl');
    $smarty->assign('_lang', $vars['_lang']);
    $smarty->assign('currency', getCurrency());

    if ($vars['webdav URL'] == '' or $vars['webdav user'] == '' or $vars['webdav password'] == ''){
        $smarty->display(dirname(__FILE__) . '/templates/nowebdav.tpl');
        return null;
    }

    if ($m AND $action){
        $m_action = $m.'_'.$action;
        if (is_callable(array($dispatcher, $m_action))) {
            $data =  $dispatcher->$m_action();
            if($dispatcher->error404 == 1){
                $smarty->display(dirname(__FILE__) . '/templates/404.tpl');
            }else {
                $smarty->assign('message', $data['message']);
                $smarty->assign('data', $data['data']);

                if (file_exists(dirname(__FILE__) . '/templates/' . $m . '/' . $action . '.tpl')) {
                    $smarty->display(dirname(__FILE__) . '/templates/header.tpl');
                    $smarty->display(dirname(__FILE__) . '/templates/' . $m . '/' . $action . '.tpl');
                } else {
                    $smarty->display(dirname(__FILE__) . '/templates/404.tpl');
                }
            }
        }else{
            $smarty->display(dirname(__FILE__) . '/templates/404.tpl');
        }
    }else {
        if($m==''){
            header('Location: addonmodules.php?module=puq_commitments_forecaster&m=reports&action=dashboard');
        }else {
            $smarty->display(dirname(__FILE__) . '/templates/404.tpl');
        }
    }

}

function puq_commitments_forecaster_sidebar($vars)
{
    $_lang = $vars['_lang'];
    $sidebar = '
<style type = "text/css">


.sidebar-header { display: none;}
.content-padded { display: none;}
.menu { display: none;}
.contentarea H1 { display: none;}


.accordion {
    border-left: #000000;
    border-right:#000000;
    border-top:solid #1a4d80 2px;
    border-bottom:#000000;
    width: 100%;
    background-color: #666666;
    color: #ffffff;
    cursor: pointer;
    padding: 5px;
    text-align: left;
    outline: none;
    font-size: 14px;
    transition: 0.5s;
}
.active, .accordion:hover {
    background-color: #666666;
}
.panel {
    margin-bottom: 0px;
    border-radius: 0px;
    font-size: 13px;
    #padding: 0 10px;
    display: none;
    background-color: #ccc;
    overflow: hidden;
}

.panel a{
    background-color: #eee;
    color: black;
    display: block;
    padding: 7px;
    text-decoration: none;
}
.panel a:hover {
    background-color: #ccc;
}

</style>

<div id="navigation">

            <button class="accordion" onclick="window.location.href=\'addonmodules.php?module=puq_commitments_forecaster&m=reports&action=dashboard\'">'.$_lang['Dashboard'].'</button>
            
            <button class="accordion">'.$_lang['Reports'].'</button>
            <div class="panel">
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=reports&action=invoices">'.$_lang['Invoices'].'</a>
            </div>
            
            <button class="accordion">'.$_lang['Documents'].'</button>
            <div class="panel">
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=list">'.$_lang['Add document'].'</a>
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=invoices_list">'.$_lang['List invoices'].'</a>
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=agreements_list">'.$_lang['List agreements'].'</a>
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=annexes_list">'.$_lang['List annexes'].'</a>
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=documents&action=unclassified_list">'.$_lang['List of unclassified'].'</a>
            </div>
            
            <button class="accordion">'.$_lang['Commitments'].'</button>
            <div class="panel">
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=list">'.$_lang['List of commitments'].'</a>
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=commitments&action=add">'.$_lang['Add commitment'].'</a>
            </div>

            <button class="accordion">'.$_lang['Counterparties'].'</button>
            <div class="panel">
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=list">'.$_lang['List counterparties'].'</a>
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=counterparties&action=add">'.$_lang['Add counterparty'].'</a>
            </div>

            <button class="accordion">'.$_lang['Accounting systems'].'</button>
            <div class="panel">
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=incomes&action=systems_list">'.$_lang['List of systems'].'</a>
            </div>

            <button class="accordion">'.$_lang['Tools'].'</button>
            <div class="panel">
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=tools&action=reindex_commitment_dates">'.$_lang['Reindex commitment dates'].'</a>
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=tools&action=reindex_agreements_dates">'.$_lang['Reindex agreements dates'].'</a>
                    <a href="addonmodules.php?module=puq_commitments_forecaster&m=tools&action=synchronization_income_invoices">'.$_lang['Synchronization income invoices'].'</a>
            </div>
          
            <button class="accordion" onclick="window.location.href=\'addonmodules.php?module=puq_commitments_forecaster&m=reports&action=about_us\'">'.$_lang['About us'].'</button>

</div>

<script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
</script>
    ';

    return $sidebar;
}
