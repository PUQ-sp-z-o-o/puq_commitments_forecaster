<?php
/*
 * This file is part of the WHMCS module. "Commitments forecaster"
 * System for management and planning of outside and inside commitments.
 *
 * Author: Ruslan Poloviy ruslan.polovyi@puq.pl
 * PUQ sp. z o.o. www.puq.pl
 * Poland
 */
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQtools;

add_hook('DailyCronJob', 1, function($vars) {
    PUQtools::ReindexCommitmentDates();
    PUQtools::ReindexAgreementsDates();
    PUQtools::SynchronizationIncomeInvoices();
    PUQtools::GetPUQstat();
});
