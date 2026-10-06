<?php
/* ===== REPLACE everything from `$locationId = ...` down to the "KPI values" block ===== */

date_default_timezone_set('Asia/Colombo'); // PHP "today" must match the business day

$type       = isset($_SESSION['privatetype']) ? intval($_SESSION['privatetype']) : 0;
$locationId = isset($_SESSION['location_id']) ? intval($_SESSION['location_id']) : 0;

// Same rules as the invoice report + full detail sale report:
//  - location filter
//  - privatetype 1 users only see invoices that have a manual invoice number
$locInv   = $locationId ? " AND `u`.`tbl_location_idtbl_location` = $locationId" : "";
if ($type == 1) { $locInv .= " AND `u`.`manuelinvno` IS NOT NULL"; }
$locStock = $locationId ? " AND `s`.`tbl_location_idtbl_location` = $locationId" : "";

// ONE place that decides what "sales" means. Reports show `total`, so this ties out to them.
// For after-discount sales use `u`.`nettotal`, or `u`.`nettotal_with_vat` for incl. VAT.
$amt = "`u`.`total`";

/* ---- KPI values (use $amt instead of `u`.`total`) ---- */
$salesToday = dash_val($conn, "SELECT COALESCE(SUM($amt),0) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`date` = '$today' $locInv");
$salesMonth = dash_val($conn, "SELECT COALESCE(SUM($amt),0) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`date` >= '$monthStart' AND `u`.`date` < '$nextMonthStart' $locInv");
$salesLast  = dash_val($conn, "SELECT COALESCE(SUM($amt),0) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`date` >= '$lastMonthStart' AND `u`.`date` <= '$lastMonthEnd' $locInv");
// $invCount, $stockValue: unchanged (they already use $locInv / $locStock)
$unpaid     = dash_rows($conn, "SELECT COUNT(*) AS c, COALESCE(SUM($amt),0) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`paymentcomplete` = 0 $locInv");

/* ---- Daily sales query ---- */
// change SUM(`u`.`total`) AS v  ->  SUM($amt) AS v

/* ---- Monthly sales query ---- */
// change SUM(`u`.`total`) AS v  ->  SUM($amt) AS v

/* ---- Recent invoices query ---- */
// change `u`.`total` AS total  ->  $amt AS total

/* NOTE: $today, $monthStart etc. must be computed AFTER date_default_timezone_set(),
   so keep that block below the lines above. Everything else is unchanged. */