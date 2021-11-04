<?php
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQtools;

add_hook('DailyCronJob', 1, function($vars) {
    PUQtools::ReindexCommitmentDates();
    PUQtools::ReindexAgreementsDates();
    PUQtools::SynchronizationIncomeInvoices();
    PUQtools::GetPUQstat();
});
