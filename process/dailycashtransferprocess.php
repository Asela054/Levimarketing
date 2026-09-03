
<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');


$tableData =$_POST['tableData'];


$obj=new stdClass();

        foreach($tableData as $rowtabledata){
            $rowselect=$rowtabledata['col_1'];
            $invoiceid=$rowtabledata['col_2'];
           

            if($rowselect==2){
                
                $insertinvoicehidden= "SELECT `manuelinvno` FROM `tbl_invoice` WHERE `manuelinvno` IS NOT NULL  ORDER BY `manuelinvno` DESC";
                $result= mysqli_query($conn,$insertinvoicehidden);
                $row=$result->fetch_assoc();
                $manuelid = $row['manuelinvno'];

                $newmanuelid = $manuelid + 1;

            $updatemanuelid="UPDATE `tbl_invoice` SET `manuelinvno`= '$newmanuelid'  WHERE  `idtbl_invoice` = '$invoiceid'";
            $conn->query($updatemanuelid);

            
        }
    }
        $obj->actiontype = 1;
 
  echo json_encode($obj); 

?>
