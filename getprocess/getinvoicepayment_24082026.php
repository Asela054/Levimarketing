<?php 
session_start();
require_once('../connection/db.php');

$locationID = $_SESSION['location_id'];
$result = null;
$invoiceRow = null;

if(!empty($_POST['invoiceno'])){
    $invoiceno = trim($_POST['invoiceno']);

    $checkSql = "SELECT `idtbl_invoice`, `paymentcomplete`, `status`, `tbl_location_idtbl_location` 
                 FROM `tbl_invoice` 
                 WHERE `manuelinvno`='".$conn->real_escape_string($invoiceno)."' 
                 LIMIT 1";
    $checkResult = $conn->query($checkSql);

    if($checkResult === false){
        echo '<div class="alert alert-danger">Something went wrong while looking up this invoice. Please try again.</div>';
        return;
    }

    if($checkResult->num_rows === 0){
        echo '<div class="alert alert-warning">This invoice was not found.</div>';
        return;
    }

    $invoiceRow = $checkResult->fetch_assoc();

    if((int)$invoiceRow['status'] !== 1){
        echo '<div class="alert alert-warning">This invoice is not active.</div>';
        return;
    }

    if((int)$invoiceRow['tbl_location_idtbl_location'] !== (int)$locationID){
        echo '<div class="alert alert-warning">This invoice belongs to a different location.</div>';
        return;
    }

    if((int)$invoiceRow['paymentcomplete'] === 1){
        echo '<div class="alert alert-info">This invoice is already fully paid.</div>';
        return;
    }

    $sql="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`manuelinvno`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, SUM(`tbl_invoice_payment_has_tbl_invoice`.`payamount`) AS `payamount` FROM `tbl_invoice` LEFT JOIN `tbl_invoice_payment_has_tbl_invoice` ON `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_idtbl_invoice`=`tbl_invoice`.`idtbl_invoice` WHERE `tbl_invoice`.`manuelinvno`='".$conn->real_escape_string($invoiceno)."' AND `tbl_invoice`.`status`=1 AND `tbl_invoice`.`paymentcomplete`=0 AND `tbl_invoice`.`tbl_location_idtbl_location`='$locationID' GROUP BY `tbl_invoice`.`idtbl_invoice`";
    $result=$conn->query($sql);
}
else if(!empty($_POST['customerID'])){
    $customerID = $_POST['customerID'];

    $checkSql = "SELECT `idtbl_invoice`, `status`, `paymentcomplete`, `tbl_location_idtbl_location` 
                 FROM `tbl_invoice` 
                 WHERE `customerid`='".$conn->real_escape_string($customerID)."'";
    $checkResult = $conn->query($checkSql);

    if($checkResult === false){
        echo '<div class="alert alert-danger">Something went wrong while looking up invoices. Please try again.</div>';
        return;
    }

    if($checkResult->num_rows === 0){
        echo '<div class="alert alert-warning">No invoices found for this customer.</div>';
        return;
    }

    $hasActive = false;
    $hasActiveInLocation = false;
    $hasUnpaidActiveInLocation = false;

    while($chkRow = $checkResult->fetch_assoc()){
        if((int)$chkRow['status'] !== 1){
            continue;
        }
        $hasActive = true;

        if((int)$chkRow['tbl_location_idtbl_location'] === (int)$locationID){
            $hasActiveInLocation = true;
            if((int)$chkRow['paymentcomplete'] !== 1){
                $hasUnpaidActiveInLocation = true;
            }
        }
    }

    if(!$hasActive){
        echo '<div class="alert alert-warning">This customer has invoices, but none are active.</div>';
        return;
    }

    if(!$hasActiveInLocation){
        echo '<div class="alert alert-warning">This customer has invoices, but not in this location.</div>';
        return;
    }

    if(!$hasUnpaidActiveInLocation){
        echo '<div class="alert alert-info">All invoices for this customer in this location are already fully paid.</div>';
        return;
    }

    $sql="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`manuelinvno`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, SUM(`tbl_invoice_payment_has_tbl_invoice`.`payamount`) AS `payamount` FROM `tbl_invoice` LEFT JOIN `tbl_invoice_payment_has_tbl_invoice` ON `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_idtbl_invoice`=`tbl_invoice`.`idtbl_invoice` WHERE `tbl_invoice`.`customerid`='".$conn->real_escape_string($customerID)."' AND `tbl_invoice`.`status`=1 AND `tbl_invoice`.`paymentcomplete`=0 AND `tbl_invoice`.`tbl_location_idtbl_location`='$locationID' GROUP BY `tbl_invoice`.`idtbl_invoice`";
    $result=$conn->query($sql);
}

if($result === false){
    echo '<div class="alert alert-danger">Something went wrong while looking up this invoice. Please try again.</div>';
    return;
}

if($result === null || $result->num_rows === 0){
    echo '<div class="alert alert-warning">This invoice was not found in this location.</div>';
    return;
}
?>
<table class="table table-striped table-bordered table-sm" id="paymentDetailTable">
    <thead>
        <tr>
            <th class="d-none">Invoice No</th>
            <th>Invoice No</th>
            <th class="d-none">Sale Type</th>
            <th>Date</th>
            <th class="text-right">Amount</th>
            <th class="text-right">Paid Amount</th>
            <th class="text-right">Balance</th>
            <th>Full Payment</th>
            <th>Half Payment</th>
            <th class="text-right">Payment</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=$result->fetch_assoc()){ ?>
        <tr>
            <td class="d-none"><?php echo $row['idtbl_invoice']; ?></td>
            <td><?php echo 'INV-'.$row['manuelinvno']; ?></td>
            <td class="d-none">&nbsp;</td>
            <td><?php echo $row['date']; ?></td>
            <td class="text-right"><?php echo sprintf('%.2f', $row['total']); ?></td>
            <td class="text-right"><?php echo sprintf('%.2f', $row['payamount']); ?></td>
            <td class="text-right"><?php echo sprintf('%.2f', ($row['total']-$row['payamount'])); ?></td>
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input fullAmount" name="payCheck1" id="payCheck1<?php echo $row['idtbl_invoice']; ?>" value="1" <?php if($row['payamount']>0){echo 'disabled';} ?>>
                    <label class="custom-control-label small" for="payCheck1<?php echo $row['idtbl_invoice']; ?>">Full Payment</label>
                </div>
            </td>
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input halfAmount" name="payCheck2" id="payCheck2<?php echo $row['idtbl_invoice']; ?>">
                    <label class="custom-control-label small" for="payCheck2<?php echo $row['idtbl_invoice']; ?>">Half Payment</label>
                </div>
            </td>
            <td class='paidAmount text-right'>0.00</td>
        </tr>
        <?php } ?>
    </tbody>
</table>