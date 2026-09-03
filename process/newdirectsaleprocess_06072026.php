<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');//die('bc');
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
$cusname=$_POST['cusname'];
$cusnic=$_POST['cusnic'];
$cusmobile=$_POST['cusmobile'];
$cusID=$_POST['cusID'];
$saletype=$_POST['saletype'];
$priceeditstatus = (isset($_POST['priceeditstatus']) && $_POST['priceeditstatus'] !== '') ? $_POST['priceeditstatus'] : 0;
$billapproveuser=$_POST['billapproveuser'];
$logtype=$_SESSION['privatetype'];

$balance=$nettotal-$paytotal;
if($billtype==1 && $balance < 0){
    $balance = abs($balance); // change due when customer overpays cash
} else if($balance < 0){
    $balance = 0;
}
if($balance>0 && $billtype != 1){$halfstatus=1;$fullstatus=0;$paycomplete=0;}
else if($balance>0 && $billtype==1 && $paytotal < $nettotal){$halfstatus=1;$fullstatus=0;$paycomplete=0;}
else{$halfstatus=0;$fullstatus=1;$paycomplete=1;}

$today=date('Y-m-d');
$updatedatetime=date('Y-m-d h:i:s');

//Change on 2024-08-05 Now cash customer select by database
// if($cusID==1 && $billtype>1){ 
if($cusID==0){
    $insertcustomer="INSERT INTO `tbl_customer`(`type`, `name`, `nic`, `phone`, `email`, `address`, `vat_num`, `s_vat`, `creditlimit`, `credittype`, `creditperiod`, `emergencydate`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_area_idtbl_area`) VALUES ('0','$cusname','$cusnic','$cusmobile','','','','','','','','','1','$updatedatetime','$userID','1')";
    $conn->query($insertcustomer);
    $cusID=$conn->insert_id;
}

if($logtype==1){
    $sqlcheckmenuelinv="SELECT `manuelinvno` FROM `tbl_invoice` WHERE `status`=1 AND `manuelinvno`!='' ORDER BY `idtbl_invoice` DESC LIMIT 1";
    $resultcheckmenuelinv=$conn->query($sqlcheckmenuelinv);
    $rowcheckmenuelinv=$resultcheckmenuelinv->fetch_assoc();

    if($resultcheckmenuelinv-> num_rows > 0){
        $menualinvoice=$rowcheckmenuelinv['manuelinvno']+1;
    }
    else{
        $menualinvoice=1;
    }
}
else{
    $menualinvoice=NULL;
}



$insertinvoice="INSERT INTO `tbl_invoice`(`manuelinvno`, `date`, `total`, `discounttotal`, `nettotal`, `saletype`, `paymentmethod`, `paymentcomplete`, `payment_created`, `chequesend`, `companydiffsend`, `ref_id`, `trackingnumber`, `deliverystatus`, `addtoaccountstatus`, `pricechangestatus`, `changeapproveuser`, `changedatetime`, `status`, `invoice_cancel_reason`, `qtycancelstatus`, `qtyreason`, `qty_checked_user`, `qty_updatedatetime`, `updatedatetime`, `tbl_user_idtbl_user`, `customerid`, `tbl_location_idtbl_location`) VALUES ('$menualinvoice','$today','$total','$distotal','$nettotal','$saletype','$billtype','$paycomplete','0','0','0','0','0','0','0','$priceeditstatus','$billapproveuser','$updatedatetime','1','-','0','-','0','$updatedatetime','$updatedatetime','$userID','$cusID','$locationID')";
if($conn->query($insertinvoice)==true){
    $invoiceID=$conn->insert_id;

    foreach($tableData as $rowtabledata){
        $productID=$rowtabledata['col_6'];
        $qty=$rowtabledata['col_2'];
        // col_8 = "sale" hidden column = actual price LINE AMOUNT (unit price x qty), as calculated on screen
        $actuallineamount=$rowtabledata['col_8'];
        $total=$rowtabledata['col_10'];
        $deiscountpresntage=$rowtabledata['col_11'];
        $discountamount=$rowtabledata['col_12'];
        $totalwithdiscount=$rowtabledata['col_13'];
        $editstatus=$rowtabledata['col_14'];
        // col_15 = cashier-typed override LINE AMOUNT (already includes qty), only meaningful when editstatus = 1
        $editedprice = isset($rowtabledata['col_15']) ? $rowtabledata['col_15'] : 0;

        // unitprice/saleprice store the actual-price LINE AMOUNT (qty already applied), not a per-unit price.
        // editedprice stores the cashier's override LINE AMOUNT the same way.
        // Stock is always deducted by qty regardless of which amount was used for billing.
        $insertinvoicedetail="INSERT INTO `tbl_invoice_detail`(`qty`, `freeqty`, `freeproductid`, `unitprice`, `editedprice`, `saleprice`, `discountpresentage`, `discountamount`, `editstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_invoice_idtbl_invoice`) VALUES ('$qty','0','$productID','$actuallineamount','$editedprice','$actuallineamount','$deiscountpresntage','$discountamount','$editstatus','1','$updatedatetime','$userID','$productID','$invoiceID')";
        $conn->query($insertinvoicedetail);
        
        $updatestock="UPDATE `tbl_stock` SET `qty`=(`qty`-'$qty') WHERE `tbl_product_idtbl_product`='$productID'";
        $conn->query($updatestock);
    }

    if($billtype==1){
        $insertpayment="INSERT INTO `tbl_invoice_payment`(`date`, `payment`, `balance`, `status`, `updatedatetime`, `tbl_user_idtbl_user`) VALUES ('$today','$paytotal','$balance','1','$updatedatetime','$userID')";
        if($conn->query($insertpayment)==true){
            $invoicepayID=$conn->insert_id;

            if(!empty($tableDataPay)){
                foreach($tableDataPay as $rowtableDataPay){
                    $paymethod=$rowtableDataPay['col_1'];
                    $bank=$rowtableDataPay['col_3'];
                    $chequeno=$rowtableDataPay['col_4'];
                    $chequedate=$rowtableDataPay['col_5'];
                    $totalamount=$rowtableDataPay['col_6'];

                    $insertpaymentdetail="INSERT INTO `tbl_invoice_payment_detail`(`method`, `amount`, `bank`, `receiptno`, `chequeno`, `chequedate`, `addaccountstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_invoice_payment_idtbl_invoice_payment`) VALUES ('$paymethod','$totalamount','$bank','','$chequeno','$chequedate','1','1','$updatedatetime','$userID','$invoicepayID')";
                    $conn->query($insertpaymentdetail);
                }
            }

            $inserthastable="INSERT INTO `tbl_invoice_payment_has_tbl_invoice`(`tbl_invoice_payment_idtbl_invoice_payment`, `tbl_invoice_idtbl_invoice`, `total`, `discount`, `payamount`, `fullstatus`, `halfstatus`) VALUES ('$invoicepayID','$invoiceID','$nettotal','0','$paytotal','$fullstatus','$halfstatus')";
            $conn->query($inserthastable);
        }
    }
    else{
        // Credit / Quotation: still record whatever payment was added (could be partial or none),
        // so balance/outstanding tracking stays consistent across all bill types.
        $insertpayment="INSERT INTO `tbl_invoice_payment`(`date`, `payment`, `balance`, `status`, `updatedatetime`, `tbl_user_idtbl_user`) VALUES ('$today','$paytotal','$balance','1','$updatedatetime','$userID')";
        if($conn->query($insertpayment)==true){
            $invoicepayID=$conn->insert_id;

            if(!empty($tableDataPay)){
                foreach($tableDataPay as $rowtableDataPay){
                    $paymethod=$rowtableDataPay['col_1'];
                    $bank=$rowtableDataPay['col_3'];
                    $chequeno=$rowtableDataPay['col_4'];
                    $chequedate=$rowtableDataPay['col_5'];
                    $totalamount=$rowtableDataPay['col_6'];

                    $insertpaymentdetail="INSERT INTO `tbl_invoice_payment_detail`(`method`, `amount`, `bank`, `receiptno`, `chequeno`, `chequedate`, `addaccountstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_invoice_payment_idtbl_invoice_payment`) VALUES ('$paymethod','$totalamount','$bank','','$chequeno','$chequedate','1','1','$updatedatetime','$userID','$invoicepayID')";
                    $conn->query($insertpaymentdetail);
                }
            }

            $inserthastable="INSERT INTO `tbl_invoice_payment_has_tbl_invoice`(`tbl_invoice_payment_idtbl_invoice_payment`, `tbl_invoice_idtbl_invoice`, `total`, `discount`, `payamount`, `fullstatus`, `halfstatus`) VALUES ('$invoicepayID','$invoiceID','$nettotal','0','$paytotal','$fullstatus','$halfstatus')";
            $conn->query($inserthastable);
        }
    }

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

}else{
    echo "Database Error: " . $conn->error;
    die();
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Record Error';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';

    $obj=new stdClass();
    $obj->action=json_encode($actionObj);
    $obj->actiontype='0';
    $obj->invoiceid='0';
    $obj->billtype=$billtype;
    $obj->saletype=$saletype;

    echo json_encode($obj);
}


?>