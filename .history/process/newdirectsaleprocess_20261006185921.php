<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->query("SET SESSION sql_mode = REPLACE(REPLACE(@@sql_mode,'NO_ZERO_DATE',''),'NO_ZERO_IN_DATE','')");

$userID=$_SESSION['userid'];
$locationID=$_SESSION['location_id'];

$netqty=0;

$tableData=$_POST['tableData'];
if(!empty($_POST['tableDataPay'])){$tableDataPay=$_POST['tableDataPay'];}
$total=$_POST['total'];
$distotal=$_POST['distotal'];
$nettotal=$_POST['nettotal'];
$paytotal=$_POST['paytotal'];
$billtype=$_POST['billtype'];
$cusname=$conn->real_escape_string($_POST['cusname']);
$cusnic=$conn->real_escape_string($_POST['cusnic']);
$cusmobile=$conn->real_escape_string($_POST['cusmobile']);
$cusID=$_POST['cusID'];
$saletype = isset($_POST['saletype']) && $_POST['saletype'] !== '' ? $_POST['saletype'] : null;
if($saletype === null){
    echo json_encode(['actiontype' => 0, 'action' => json_encode([
        'icon' => 'fas fa-exclamation-triangle',
        'title' => '',
        'message' => 'Sale type is missing. Please refresh and select Retail or Whole Sale.',
        'url' => '',
        'target' => '_blank',
        'type' => 'danger'
    ])]);
    die();
}
$priceeditstatus = (isset($_POST['priceeditstatus']) && $_POST['priceeditstatus'] !== '') ? $_POST['priceeditstatus'] : 0;
$billapproveuser=$_POST['billapproveuser'];
$logtype=$_SESSION['privatetype'];

$balance=$nettotal-$paytotal;
if($billtype==1 && $balance < 0){
    $balance = abs($balance);
} else if($balance < 0){
    $balance = 0;
}
if($balance>0 && $billtype != 1){$halfstatus=1;$fullstatus=0;$paycomplete=0;}
else if($balance>0 && $billtype==1 && $paytotal < $nettotal){$halfstatus=1;$fullstatus=0;$paycomplete=0;}
else{$halfstatus=0;$fullstatus=1;$paycomplete=1;}

$today=date('Y-m-d');
$updatedatetime=date('Y-m-d H:i:s');

$conn->begin_transaction();

try {

    if($cusID==0){
        $insertcustomer="INSERT INTO `tbl_customer`(`type`, `name`, `nic`, `phone`, `email`, `address`, `vat_num`, `s_vat`, `creditlimit`, `credittype`, `creditperiod`, `emergencydate`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_area_idtbl_area`) VALUES ('0','$cusname','$cusnic','$cusmobile','','','','','','','','','1','$updatedatetime','$userID','1')";
        $conn->query($insertcustomer);
        $cusID=$conn->insert_id;
    }

    if($logtype==1){
        $sqlcheckmenuelinv="SELECT `manuelinvno` FROM `tbl_invoice` WHERE `status`=1 AND `manuelinvno`!='' ORDER BY `idtbl_invoice` DESC LIMIT 1";
        $resultcheckmenuelinv=$conn->query($sqlcheckmenuelinv);
        $rowcheckmenuelinv=$resultcheckmenuelinv->fetch_assoc();

        if($resultcheckmenuelinv->num_rows > 0){
            $menualinvoice=$rowcheckmenuelinv['manuelinvno']+1;
        }
        else{
            $menualinvoice=1;
        }
    }
    else{
        $menualinvoice=NULL;
    }

    $insertinvoice="INSERT INTO `tbl_invoice`(`invtype`, `manuelinvno`, `taxinvoice_no`, `date`, `total`, `discounttotal`, `nettotal`, `vattype`, `vatpercent`, `vatamount`, `nettotal_with_vat`, `saletype`, `paymentmethod`, `paymentcomplete`, `payment_created`, `chequesend`, `companydiffsend`, `ref_id`, `trackingnumber`, `deliverystatus`, `addtoaccountstatus`, `pricechangestatus`, `changeapproveuser`, `changedatetime`, `status`, `invoice_cancel_reason`, `qtycancelstatus`, `qtyreason`, `qty_checked_user`, `qty_updatedatetime`, `updatedatetime`, `tbl_user_idtbl_user`, `customerid`, `tbl_location_idtbl_location`) VALUES ('0','$menualinvoice','','$today','$total','$distotal','$nettotal','0','0','0','0','$saletype','$billtype','$paycomplete','0','0','0','0','0','0','0','$priceeditstatus','$billapproveuser','$updatedatetime','1','-','0','-','0','$updatedatetime','$updatedatetime','$userID','$cusID','$locationID')";
    $conn->query($insertinvoice);
    $invoiceID=$conn->insert_id;

    foreach($tableData as $rowtabledata){
        $productID=intval($rowtabledata['col_6']);
        $qty=floatval($rowtabledata['col_2']);
        $actuallineamount=floatval(str_replace(',', '', $rowtabledata['col_8']));
        $deiscountpresntage=floatval($rowtabledata['col_11']);
        $discountamount=floatval(str_replace(',', '', $rowtabledata['col_12']));
        $editstatus=intval($rowtabledata['col_14']);
        $editedprice = isset($rowtabledata['col_15']) ? floatval(str_replace(',', '', $rowtabledata['col_15'])) : 0;

        $insertinvoicedetail="INSERT INTO `tbl_invoice_detail`(`qty`, `freeqty`, `freeproductid`, `unitprice`, `editedprice`, `saleprice`, `discountpresentage`, `discountamount`, `editstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_invoice_idtbl_invoice`) VALUES ('$qty','0','$productID','$actuallineamount','$editedprice','$actuallineamount','$deiscountpresntage','$discountamount','$editstatus','1','$updatedatetime','$userID','$productID','$invoiceID')";
        $conn->query($insertinvoicedetail);

        $updatestock="UPDATE `tbl_stock` SET `qty`=(`qty`-'$qty') WHERE `tbl_product_idtbl_product`='$productID' AND `tbl_location_idtbl_location`='$locationID'";
        $conn->query($updatestock);
    }

    if($billtype==1){
        $insertpayment="INSERT INTO `tbl_invoice_payment`(`date`, `payment`, `balance`, `status`, `updatedatetime`, `tbl_user_idtbl_user`) VALUES ('$today','$paytotal','$balance','1','$updatedatetime','$userID')";
        $conn->query($insertpayment);
        $invoicepayID=$conn->insert_id;

        if(empty($tableDataPay)){
            throw new Exception('No payment rows were received.');
        }

        foreach($tableDataPay as $rowtableDataPay){
            $paymethod   = intval($rowtableDataPay['col_1']);
            $bank        = $conn->real_escape_string($rowtableDataPay['col_3'] ?? '');
            $chequeno    = $conn->real_escape_string($rowtableDataPay['col_4'] ?? '');
            $chequedate  = !empty(trim($rowtableDataPay['col_5'] ?? '')) ? $conn->real_escape_string($rowtableDataPay['col_5']) : '0000-00-00';
            $cardlast4   = $conn->real_escape_string($rowtableDataPay['col_6'] ?? '');
            $totalamount = floatval(str_replace(',', '', $rowtableDataPay['col_7']));

            $insertpaymentdetail="INSERT INTO `tbl_invoice_payment_detail`(`method`, `amount`, `bank`, `receiptno`, `chequeno`, `chequedate`, `cardlast4`, `addaccountstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_invoice_payment_idtbl_invoice_payment`) VALUES ('$paymethod','$totalamount','$bank','','$chequeno','$chequedate','$cardlast4','1','1','$updatedatetime','$userID','$invoicepayID')";
            $conn->query($insertpaymentdetail);
        }

        $inserthastable="INSERT INTO `tbl_invoice_payment_has_tbl_invoice`(`tbl_invoice_payment_idtbl_invoice_payment`, `tbl_invoice_idtbl_invoice`, `total`, `discount`, `payamount`, `fullstatus`, `halfstatus`) VALUES ('$invoicepayID','$invoiceID','$nettotal','0','$paytotal','$fullstatus','$halfstatus')";
        $conn->query($inserthastable);
    }

    $conn->commit();

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
    $obj->invoiceid=$invoiceID;
    $obj->billtype=$billtype;
    $obj->saletype=$saletype;

    echo json_encode($obj);

} catch (Exception $e) {
    $conn->rollback();
    echo "Database Error: " . $e->getMessage();
    die();
}
?>