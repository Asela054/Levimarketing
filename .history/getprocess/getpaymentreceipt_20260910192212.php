<?php 
session_start();
require_once('../connection/db.php');

$paymentinoiceID=$_POST['paymentinoiceID'];
$locationID=$_SESSION['location_id'];

$query = mysqli_query($conn, "
    SELECT location, code, companyname, address, contact1, contact2, contact3, email
    FROM tbl_location
    WHERE idtbl_location = $locationID
");

$company = mysqli_fetch_assoc($query);

$companyName    = $company['companyname'] ?? '';
$companyAddress = $company['address'] ?? '';

$companyTel = implode(' / ', array_filter([
    $company['contact1'] ?? '',
    $company['contact2'] ?? '',
    $company['contact3'] ?? ''
]));

$companyEmail = $company['email'] ?? '';
$companyTin   = '103464978'; // Supplier's TIN

$sqlpaymentdetail="SELECT `tbl_invoice_payment_has_tbl_invoice`.*, `tbl_invoice`.`manuelinvno`, `tbl_invoice`.`total` AS `invoicetotal` FROM `tbl_invoice_payment_has_tbl_invoice` LEFT JOIN `tbl_invoice` ON `tbl_invoice`.`idtbl_invoice`=`tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_idtbl_invoice` WHERE `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_payment_idtbl_invoice_payment`='$paymentinoiceID'";
$resultpaymentdetail=$conn->query($sqlpaymentdetail);

$sqlpayment="SELECT * FROM `tbl_invoice_payment` WHERE `idtbl_invoice_payment`='$paymentinoiceID' AND `status`=1";
$resultpayment=$conn->query($sqlpayment);
$rowpayment=$resultpayment->fetch_assoc();

$sqlpaymentbank="SELECT * FROM `tbl_invoice_payment_detail` WHERE `status`=1 AND `method`=2 AND `tbl_invoice_payment_idtbl_invoice_payment`='$paymentinoiceID'";
$resultpaymentbank=$conn->query($sqlpaymentbank);
?>
<div class="row">
    <div class="col-12">
        <table class="w-100 tableprint">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="5" class="text-center small align-middle">
                        <h2 class="font-weight-light m-0"><?php echo $companyName; ?></h2>
                        <?php echo $companyAddress; ?><br>
                        <?php echo $companyTel; ?> | <?php echo $companyEmail; ?><br>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>            
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12 text-right">Receipt No: PR-<?php echo $paymentinoiceID; ?></div>
</div>
<div class="row">
    <div class="col-12">
        <h5 class="mt-3">Payment Receipt</h5>
        <hr class="border-dark">
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-striped table-bordered table-black table-sm small bg-transparent tableprint">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice No</th>
                    <th class="text-right">Invoice Amount</th>
                    <th class="text-right">Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1;while($rowpaymentdetail=$resultpaymentdetail->fetch_assoc()){ 
                    $displayinvno = 'INV-'.(!empty($rowpaymentdetail['manuelinvno']) ? $rowpaymentdetail['manuelinvno'] : $rowpaymentdetail['tbl_invoice_idtbl_invoice']);
                ?>
                <tr>
                    <td><?php echo $i ?></td>
                    <td><?php echo htmlspecialchars($displayinvno); ?></td>
                    <td class="text-right"><?php echo number_format($rowpaymentdetail['invoicetotal'], 2); ?></td>
                    <td class="text-right"><?php echo number_format($rowpaymentdetail['payamount'],2); ?></td>
                </tr>
                <?php $i++;} ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-9 text-right"><h2 class="font-weight-bold">Net Total</h2></div>
    <div class="col-3 text-right"><h2 class="font-weight-bold"><?php echo 'Rs.'.number_format($rowpayment['payment'], 2); ?></h2></div>
</div>
<div class="row">
    <div class="col-9 text-right"><h5 class="font-weight-light">Payment</h5></div>
    <div class="col-3 text-right"><h5 class="font-weight-light"><?php echo 'Rs.'.number_format($rowpayment['payment'], 2); ?></h5></div>
</div>
<div class="row">
    <div class="col-9 text-right"><h6 class="font-weight-light">balance</h6></div>
    <div class="col-3 text-right"><h6 class="font-weight-light"><?php echo 'Rs.'.number_format($rowpayment['balance'], 2); ?></h6></div>
</div>
<div class="row">
    <div class="col">
        <?php 
        while($rowpaymentbank=$resultpaymentbank->fetch_assoc()){
            if($rowpaymentbank['chequeno']!=''){echo $rowpaymentbank['chequeno'].' - '.$rowpaymentbank['amount'].'<br>';}
            else if($rowpaymentbank['receiptno']!=''){echo $rowpaymentbank['receiptno'].' - '.$rowpaymentbank['amount'].'<br>';}
        } 
        ?>
    </div>
</div>