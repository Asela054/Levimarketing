<?php
require_once('../connection/db.php');

$sql="SELECT `g`.`confirm_status`, `g`.`idtbl_grn`, `g`.`date`, `g`.`total`, `g`.`invoicenum`, `p`.`tbl_porder_idtbl_porder` FROM `tbl_grn` as `g` JOIN `tbl_porder_grn` as `p` ON (`g`.`idtbl_grn` = `p`.`tbl_grn_idtbl_grn`) WHERE `g`.`status`=1";
$result=$conn->query($sql);
?>
<table class="table table-striped table-bordered table-sm" id="grnlisttable">
    <thead>
        <tr>
            <th>Date</th>
            <th>PO</th>
            <th>GRN</th>
            <th>Invoice</th>
            <th class="text-right">Total</th>
            <th class="text-right">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=$result->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $row['date']; ?></td>
            <td>PO-<?php echo $row['tbl_porder_idtbl_porder']; ?></td>
            <td><?php echo 'GRN-'.$row['idtbl_grn']; ?></td>
            <td><?php echo $row['invoicenum']; ?></td>
            <td class="text-right"><?php echo number_format($row['total'], 2); ?></td>
            <td class="text-center">
                <button class="btn btn-outline-dark btn-sm btnviewgrn" id="<?php echo $row['idtbl_grn']; ?>"  name="<?php echo $row['confirm_status']; ?>"><i class="fas fa-eye"></i></button>
                <?php if($row['confirm_status'] == 0){?>
                    <a href="process/statusgrnconfirm.php?record=<?php echo $row['idtbl_grn'] ?>"
                                                        onclick="return confirm('Are you sure you want to confirm this GRN?');"
                                                        target="_self" class="btn btn-outline-danger btn-sm"><i
                                                            class="fas fa-window-close"></i></a>
                <?php }else{?>
                    <button class="btn btn-outline-success btn-sm " id="<?php echo $row['idtbl_grn']; ?>"><i class="fas fa-check"></i></button>
                <?php }?>

            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
