<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");exit;}
require_once('connection/db.php');

$recordID=intval($_GET['record']);
$locationID=intval($_SESSION['location_id']);

$sql="SELECT `r`.*, `g`.`invoicenum`, `g`.`date` AS `grndate`, 
             `s`.`suppliername`, `s`.`address` AS `supaddress`, `s`.`contactone`, `s`.`email` AS `supemail`, 
             `l`.`location`, `l`.`companyname`, `l`.`address` AS `locaddress`, `l`.`contact1`, `l`.`contact2`, `l`.`email` AS `locemail` 
      FROM `tbl_grn_return` AS `r` 
      LEFT JOIN `tbl_grn` AS `g` ON (`g`.`idtbl_grn` = `r`.`tbl_grn_idtbl_grn`) 
      LEFT JOIN `tbl_supplier` AS `s` ON (`s`.`idtbl_supplier` = `r`.`tbl_supplier_idtbl_supplier`) 
      LEFT JOIN `tbl_location` AS `l` ON (`l`.`idtbl_location` = `r`.`tbl_location_idtbl_location`) 
      WHERE `r`.`idtbl_grn_return`='$recordID' AND `r`.`status`=1 AND `r`.`tbl_location_idtbl_location`='$locationID'";
$result=$conn->query($sql);

if($result->num_rows==0){ echo 'Return note not found'; exit; }
$r=$result->fetch_assoc();

$sqllines="SELECT `d`.`qty`, `d`.`unitprice`, `d`.`total`, `p`.`product_name` 
           FROM `tbl_grn_return_detail` AS `d` 
           LEFT JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `d`.`tbl_product_idtbl_product`) 
           WHERE `d`.`tbl_grn_return_idtbl_grn_return`='$recordID' AND `d`.`status`=1 
           ORDER BY `d`.`idtbl_grn_return_detail` ASC";
$resultlines=$conn->query($sqllines);

function h($v){ return htmlspecialchars((string)$v); }
function rs($v){ return number_format((float)$v,2); }

$statustext = $r['confirm_status']==1 ? 'APPROVED' : 'PENDING APPROVAL';
$tel = trim($r['contact1'].' '.$r['contact2']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>GRN Return Note <?php echo h($r['returnnum']) ?></title>
<style>
    @page { size: A4 portrait; margin: 12mm; }
    body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #222; margin: 0; }
    .page { max-width: 780px; margin: 0 auto; padding: 10px; }
    table { border-collapse: collapse; width: 100%; }
    .head { border-bottom: 3px solid #333; padding-bottom: 8px; margin-bottom: 10px; }
    .company { font-size: 20px; font-weight: bold; }
    .title { font-size: 18px; font-weight: bold; text-align: right; letter-spacing: 1px; }
    .small { font-size: 11px; color: #555; }
    .box { border: 1px solid #bbb; padding: 8px 10px; line-height: 1.7; }
    .box-title { font-weight: bold; border-bottom: 1px solid #ddd; margin-bottom: 4px; }
    .lbl { color: #666; }
    .items th { background: #333; color: #fff; padding: 6px; border: 1px solid #333; font-size: 11px; }
    .items td { padding: 6px; border: 1px solid #bbb; }
    .r { text-align: right; }
    .c { text-align: center; }
    .totals td { padding: 4px 8px; }
    .grand td { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
    .sign { border-top: 1px solid #555; padding-top: 4px; text-align: center; font-size: 11px; }
    .noprint { text-align: right; margin-bottom: 8px; }
    .noprint button { padding: 6px 18px; font-size: 13px; cursor: pointer; }
    @media print { .noprint { display: none; } }
</style>
</head>
<body>
<div class="page">
    <div class="noprint"><button onclick="window.print()">Print</button></div>

    <table class="head">
        <tr>
            <td style="width:60%;">
                <div class="company"><?php echo h($r['companyname']) ?></div>
                <div class="small">
                    <?php echo h($r['location']) ?><br>
                    <?php echo h($r['locaddress']) ?><br>
                    <?php if($tel!=''){ echo 'Tel: '.h($tel); } ?>
                    <?php if(!empty($r['locemail'])){ echo ' | '.h($r['locemail']); } ?>
                </div>
            </td>
            <td style="width:40%;">
                <div class="title">GRN RETURN NOTE</div>
                <div class="small r" style="margin-top:4px;"><?php echo $statustext ?></div>
            </td>
        </tr>
    </table>

    <table style="margin-bottom:10px;">
        <tr>
            <td style="width:50%; padding-right:6px; vertical-align:top;">
                <div class="box">
                    <div class="box-title">SUPPLIER</div>
                    <b><?php echo h($r['suppliername']) ?></b><br>
                    <?php echo h($r['supaddress']) ?><br>
                    <span class="lbl">Tel:</span> <?php echo h($r['contactone']) ?>
                    <?php if(!empty($r['supemail'])){ ?><br><span class="lbl">Email:</span> <?php echo h($r['supemail']) ?><?php } ?>
                </div>
            </td>
            <td style="width:50%; padding-left:6px; vertical-align:top;">
                <div class="box">
                    <div class="box-title">RETURN DETAILS</div>
                    <span class="lbl">Return No:</span> <b><?php echo h($r['returnnum']) ?></b><br>
                    <span class="lbl">Return Date:</span> <?php echo h($r['date']) ?><br>
                    <span class="lbl">GRN No:</span> <?php echo h($r['tbl_grn_idtbl_grn']) ?> 
                    <span class="lbl">(<?php echo h($r['grndate']) ?>)</span><br>
                    <span class="lbl">Supplier Invoice No:</span> <?php echo h($r['invoicenum']) ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th>Product</th>
                <th style="width:12%;">Qty</th>
                <th style="width:16%;">Unit Price</th>
                <th style="width:17%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $n=0; $totalqty=0; while($line=$resultlines->fetch_assoc()){ $n++; $totalqty+=$line['qty']; ?>
            <tr>
                <td class="c"><?php echo $n ?></td>
                <td><?php echo h($line['product_name']) ?></td>
                <td class="r"><?php echo (float)$line['qty'] ?></td>
                <td class="r"><?php echo rs($line['unitprice']) ?></td>
                <td class="r"><?php echo rs($line['total']) ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <table style="margin-top:10px;">
        <tr>
            <td style="width:55%; vertical-align:top; padding-right:12px;">
                <?php if(trim($r['reason'])!=''){ ?>
                <div class="box">
                    <div class="box-title">REASON</div>
                    <?php echo nl2br(h($r['reason'])) ?>
                </div>
                <?php } ?>
            </td>
            <td style="width:45%; vertical-align:top;">
                <table class="totals">
                    <tr><td>Total Qty</td><td class="r"><?php echo (float)$totalqty ?></td></tr>
                    <tr><td>Subtotal</td><td class="r"><?php echo rs($r['subtotal']) ?></td></tr>
                    <tr><td>VAT</td><td class="r"><?php echo rs($r['vatamount']) ?></td></tr>
                    <tr class="grand"><td>TOTAL (Rs.)</td><td class="r"><?php echo rs($r['total']) ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="margin-top:50px;">
        <tr>
            <td style="width:30%;"><div class="sign">Prepared By</div></td>
            <td style="width:5%;"></td>
            <td style="width:30%;"><div class="sign">Approved By</div></td>
            <td style="width:5%;"></td>
            <td style="width:30%;"><div class="sign">Supplier Signature</div></td>
        </tr>
    </table>

    <div class="small c" style="margin-top:16px;">Printed on <?php echo date('d M Y, h:i A') ?></div>
</div>
</body>
</html>