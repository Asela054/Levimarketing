<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID = $_SESSION['userid'];
$locationID=$_SESSION['location_id'];
$updatedatetime = date('Y-m-d h:i:s');
$logtype = $_SESSION['privatetype'];

$saletype = $_POST['saletype'];
$fulltotal = $_POST['fulltotal'];
$distotal = $_POST['distotal'];
$nettotal = $_POST['nettotal'];
$tableData = json_decode($_POST['tableData'], true);  // Assuming tableData is JSON encoded in the AJAX request
$lorryID = $_POST['Recordlorry'];
$recorddate = $_POST['recordDate'];
$refID = $_POST['RecordDriver'];
$vehicleload = $_POST['vehicleload'];

$allSuccess = true; // Flag to check if all operations are successful

// Work out the starting manual invoice number (same rule as the counter-sale billing screen).
// Only users on the manual/private log type (privatetype == 1) get a running number; others get NULL.
if($logtype==1){
    $sqlcheckmenuelinv = "SELECT `manuelinvno` FROM `tbl_invoice` WHERE `status`=1 AND `manuelinvno`!='' ORDER BY `idtbl_invoice` DESC LIMIT 1";
    $resultcheckmenuelinv = $conn->query($sqlcheckmenuelinv);
    $rowcheckmenuelinv = $resultcheckmenuelinv->fetch_assoc();

    if($resultcheckmenuelinv->num_rows > 0){
        $menualinvoice = $rowcheckmenuelinv['manuelinvno'] + 1;
    } else {
        $menualinvoice = 1;
    }
} else {
    $menualinvoice = NULL;
}

foreach($tableData as $row) {
    $customerID = $row['customerID'];
    $productID = $row['productID'];
    $qty = $row['qty'];
    $salediscountper = $row['discountpercentage'];
    $saleprice = $row['saleprice'];
    $total = $row['total'];
    $totalWithDis = $row['netamount'];
    $distotal = $row['distotal'];
    $unitprice = $row['unitprice'];

    // $menualinvoice may be NULL here (non-manual log type) — that's fine, the column allows NULL.
    $insertbill = "INSERT INTO `tbl_invoice` (`manuelinvno`, `date`, `total`, `discounttotal`, `nettotal`, `saletype`, `paymentmethod`, `paymentcomplete`, `payment_created`,
    `chequesend`, `companydiffsend`, `ref_id`, `trackingnumber`, `deliverystatus`, `addtoaccountstatus`, `pricechangestatus`, `changeapproveuser`, `changedatetime`, `status`, `invoice_cancel_reason`, 
    `qtycancelstatus`, `qtyreason`, `qty_checked_user`, `qty_updatedatetime`, `updatedatetime`, `tbl_user_idtbl_user`, `customerid`, `tbl_location_idtbl_location`)
    VALUES ('$menualinvoice', '$recorddate', '$total', '$distotal', '$totalWithDis', '$saletype', '0', '0', '0', '0', '0', '$refID', '0', '0', '0', '0', '0', '0', '1', '-', '0', '-', '0', '$recorddate', '$updatedatetime', '$userID', '$customerID', '$locationID')";
    if ($conn->query($insertbill) === TRUE) {
        $invoiceID = $conn->insert_id;

        // Only bump the running number after a successful insert, and only for manual log type,
        // so multiple invoices created in this same request each get the next number in sequence.
        if($logtype==1){
            $menualinvoice++;
        }

        $insertinvoicedetail = "INSERT INTO `tbl_invoice_detail`(`qty`, `freeqty`, `freeproductid`, `unitprice`,`discountpresentage`,`discountamount`,`saleprice`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_invoice_idtbl_invoice`) VALUES ('$qty', '0', '$productID', '$unitprice','$salediscountper','$distotal', '$saleprice', '1', '$updatedatetime', '$userID', '$productID', '$invoiceID')";
        if (!$conn->query($insertinvoicedetail)) {
            $allSuccess = false;
            break;
        }
        
        $updatestock = "UPDATE `tbl_vehicle_load_detail` JOIN `tbl_vehicle_load` ON `tbl_vehicle_load_detail`.`tbl_vehicle_load_idtbl_vehicle_load` = `tbl_vehicle_load`.`idtbl_vehicle_load` SET `qty` = (`qty` - '$qty') WHERE `date` = '$recorddate' AND `lorryid` = '$lorryID' AND `driverid` = '$refID' AND `tbl_product_idtbl_product` = '$productID'";
        if (!$conn->query($updatestock)) {
            $allSuccess = false;
            break;
        }

        $insertloadinvoice = "INSERT INTO `tbl_vehicle_load_has_tbl_invoice`(`tbl_vehicle_load_idtbl_vehicle_load`, `tbl_invoice_idtbl_invoice`, `tbl_customer_idtbl_customer`) VALUES ('$vehicleload', '$invoiceID','$customerID')";
        if (!$conn->query($insertloadinvoice)) {
            $allSuccess = false;
            break;
        }
        
    } else {
        $allSuccess = false;
        break;
    }
}

if ($allSuccess) {
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-check-circle';
    $actionObj->title='';
    $actionObj->message='Add Successfully';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='success';

    $obj=new stdClass();
    $obj->action=json_encode($actionObj);
    $obj->actiontype='1';

    echo json_encode($obj);
} else {
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Record Error';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';

    $obj=new stdClass();
    $obj->action=json_encode($actionObj);
    $obj->actiontype='2';

    echo json_encode($obj);
}
?>