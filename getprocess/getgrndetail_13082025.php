<?php
require_once('../connection/db.php');

$grnid=$_POST['grnid'];
$confirmstatus=$_POST['confirmstatus'];

$sql="SELECT `tbl_grndetail`.`qty`, `tbl_grndetail`.`unitprice`, `tbl_grndetail`.`total`, `tbl_product`.`product_name`, `tbl_product`.`idtbl_product` FROM `tbl_grndetail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_grndetail`.`tbl_product_idtbl_product` WHERE `tbl_grndetail`.`tbl_grn_idtbl_grn`='$grnid' AND `tbl_grndetail`.`status`=1";
$result=$conn->query($sql);
?>
<table class="table table-striped table-bordered table-sm" id="grndetailsstable">
    <thead>
        <tr>
            <th>Product</th>
            <th class = "d-none">Product ID</th>
            <th class="text-right">Unit price</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=$result->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $row['product_name']; ?></td>
            <td class = "d-none"><?php echo $row['idtbl_product']; ?></td>
            <td class="text-right"><?php echo number_format($row['unitprice'],2); ?></td>
            <td class="text-center editnewqty"><?php echo $row['qty']; ?></td>
            <td class="text-right"><?php echo number_format($row['total'], 2); ?></td>
            <td class="text-right d-none"><?php echo $row['unitprice']; ?></td>
            <td class="text-right d-none"><?php echo $row['total']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php if($confirmstatus == 0){?>
<button class="btn btn-primary btn-sm fa-pull-right" id="btnUpdate"><i class="fa fa-save"></i>&nbsp;Update</button>
<?php }else{?>
<button class="btn btn-primary btn-sm fa-pull-right" id="btnUpdate" disabled><i class="fa fa-save"></i>&nbsp;GRN Already confirmed</button>
<?php }?>


<input type="hidden" id = "hiddengrnid" value = "<?php echo $grnid ?>">
<script>
    
    $('#grndetailsstable tbody').on('click', '.editnewqty', function(e) {
            var row = $(this);
            // var rowid = row.closest("tr").find('td:eq(0)').text();
            // var selectvalueone = $('.optionpiorityone' + rowid).val();
            // row.closest("tr").find('td:eq(7)').text(selectvalueone);

            e.preventDefault();
            e.stopImmediatePropagation();

            $this = $(this);
            if ($this.data('editing')) return;

            var val = $this.text();

            $this.empty();
            $this.data('editing', true);

            $('<input type="Text" class="form-control form-control-sm optionnewqty">').val(val).appendTo($this);
            textremove('.optionnewqty', row);
        });

        function textremove(classname, row) {
            $('#grndetailsstable tbody').on('keyup', classname, function(e) {
                if (e.keyCode === 13) { 
                    $this = $(this);
                    var val = $this.val();
                    var td = $this.closest('td');
                    td.empty().html(val).data('editing', false);
                    
                    var rowID = row.closest("td").parent()[0].rowIndex;
                    var unitprice = parseFloat(row.closest("tr").find('td:eq(5)').text());
                    var newqty = parseFloat(row.closest("tr").find('td:eq(3)').text());

                    var totnew = newqty*unitprice;
                   
                    var totnewComma = parseFloat(totnew).toFixed(2);
                    $('#grndetailsstable').find('tr').eq(rowID).find('td:eq(4)').text(totnewComma);
                    $('#grndetailsstable').find('tr').eq(rowID).find('td:eq(6)').text(totnew);
                }
            });
        }

        $('#btnUpdate').click(function(){
                jsonObj = [];
                $("#grndetailsstable tbody tr").each(function() {
                    item = {}
                    $(this).find('td').each(function(col_idx) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    });
                    jsonObj.push(item);
                });
            
                // console.log(jsonObj);

                var grnID = $('#hiddengrnid').val();
          

                $.ajax({
                    type: "POST",
                    data: {
                        grnID: grnID,
                        tableData: jsonObj,      
                    },
                    url: 'process/updategrndetailsprocess.php',
                    success: function(result) { //alert(result);
                        action(result);
                        location.reload();
                    }
                });
            
        });
</script>