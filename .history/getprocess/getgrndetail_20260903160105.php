<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php"); exit;}
require_once('../connection/db.php');

$grnid=intval($_POST['grnid']);
$confirmstatus=intval($_POST['confirmstatus']);

$sqlheader="SELECT g.`date`, g.`invoicenum`, g.`dispatchnum`, g.`confirm_status`, g.`porder_id`,
        l.`location`, l.`code`,
        s.`suppliername`
    FROM `tbl_grn` AS g
    LEFT JOIN `tbl_porder` AS po ON po.`idtbl_porder` = g.`porder_id` AND g.`porder_id` > 0
    LEFT JOIN `tbl_location` AS l ON l.`idtbl_location` = g.`tbl_location_idtbl_location`
    LEFT JOIN `tbl_supplier` AS s ON s.`idtbl_supplier` = COALESCE(g.`tbl_supplier_idtbl_supplier`, po.`tbl_supplier_idtbl_supplier`)
    WHERE g.`idtbl_grn` = '$grnid'";
$resultheader = $conn->query($sqlheader);
$rowheader = $resultheader->fetch_assoc();

$sql="SELECT `tbl_grndetail`.`qty`, `tbl_grndetail`.`unitprice`, `tbl_grndetail`.`total`, `tbl_product`.`product_name`, `tbl_product`.`idtbl_product`
    FROM `tbl_grndetail`
    LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_grndetail`.`tbl_product_idtbl_product`
    WHERE `tbl_grndetail`.`tbl_grn_idtbl_grn`='$grnid' AND `tbl_grndetail`.`status`=1";
$result=$conn->query($sql);
?>
<div class="row mb-3">
    <div class="col-6"><strong>Date:</strong> <?php echo $rowheader['date']; ?></div>
    <div class="col-6">
        <strong><?php echo $rowheader['porder_id'] > 0 ? 'Purchase Order:' : 'Supplier:'; ?></strong>
        <?php echo $rowheader['porder_id'] > 0 ? 'PO-'.$rowheader['porder_id'] : htmlspecialchars($rowheader['suppliername']); ?>
    </div>
    <div class="col-6"><strong>Invoice No:</strong> <?php echo htmlspecialchars($rowheader['invoicenum']); ?></div>
    <div class="col-6"><strong>Delivery No:</strong> <?php echo htmlspecialchars($rowheader['dispatchnum']); ?></div>
    <div class="col-6"><strong>Location:</strong> <?php echo htmlspecialchars($rowheader['location'].'-'.$rowheader['code']); ?></div>
</div>
<table class="table table-striped table-bordered table-sm" id="grndetailsstable">
    <thead>
        <tr>
            <th>Product</th>
            <th class="d-none">Product ID</th>
            <th class="text-right">Unit price</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=$result->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $row['product_name']; ?></td>
            <td class="d-none"><?php echo $row['idtbl_product']; ?></td>
            <td class="text-right"><?php echo number_format($row['unitprice'],2); ?></td>
            <td class="text-center <?php if($confirmstatus==0){echo 'editnewqty';} ?>"><?php echo $row['qty']; ?></td>
            <td class="text-right"><?php echo number_format($row['total'], 2); ?></td>
            <td class="text-right d-none"><?php echo $row['unitprice']; ?></td>
            <td class="text-right d-none"><?php echo $row['total']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<button class="btn btn-danger btn-sm fa-pull-right" id="btnPrintGrn"><i class="fas fa-print"></i>&nbsp;Print GRN</button>

<input type="hidden" id="hiddengrnid" value="<?php echo $grnid ?>">
<script>
    $('#grndetailsstable tbody').off('click', '.editnewqty').on('click', '.editnewqty', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        $this = $(this);
        if ($this.data('editing')) return;

        var val = $this.text();

        $this.empty();
        $this.data('editing', true);

        $('<input type="Text" class="form-control form-control-sm optionnewqty">').val(val).appendTo($this);
        grndetailtextremove('.optionnewqty', $this);
    });

    function grndetailtextremove(classname, row) {
        $('#grndetailsstable tbody').off('keyup', classname).on('keyup', classname, function(e) {
            if (e.keyCode === 13) { 
                $this = $(this);
                var val = $this.val();
                var td = $this.closest('td');
                td.empty().html(val).data('editing', false);

                var tr = td.closest('tr');
                var rowID = tr[0].rowIndex;
                var unitprice = parseFloat(tr.find('td:eq(5)').text());
                var newqty = parseFloat(tr.find('td:eq(3)').text());

                var totnew = newqty*unitprice;
                var totnewComma = parseFloat(totnew).toFixed(2);

                tr.find('td:eq(4)').text(totnewComma);
                tr.find('td:eq(6)').text(totnew);
            }
        });
    }

    $('#btnPrintGrn').off('click').on('click', function(){
        var grnID = $('#hiddengrnid').val();
        $.ajax({
            type: "POST",
            data: {
                grnid: grnID
            },
            url: 'getprocess/getgrnprint.php',
            success: function(result) {
                var printWindow = window.open('', '', 'height=600,width=900');
                printWindow.document.write(result);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(function() { printWindow.print(); }, 250);
            }
        });
    });
</script>