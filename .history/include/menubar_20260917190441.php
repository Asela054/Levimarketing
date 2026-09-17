<?php 
$getUrl=$_SERVER['SCRIPT_NAME'];
$url=explode('/', $getUrl);
$lastElement=end($url);

$type =  $_SESSION['privatetype'];

if($lastElement=='useraccount.php'){
    $addcheck=checkprivilege($menuprivilegearray, 1, 1);
    $editcheck=checkprivilege($menuprivilegearray, 1, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 1, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 1, 4);
}
else if($lastElement=='usertype.php'){
    $addcheck=checkprivilege($menuprivilegearray, 2, 1);
    $editcheck=checkprivilege($menuprivilegearray, 2, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 2, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 2, 4);
}
else if($lastElement=='userprivilege.php'){
    $addcheck=checkprivilege($menuprivilegearray, 3, 1);
    $editcheck=checkprivilege($menuprivilegearray, 3, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 3, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 3, 4);
}
else if($lastElement=='product.php'){
    $addcheck=checkprivilege($menuprivilegearray, 4, 1);
    $editcheck=checkprivilege($menuprivilegearray, 4, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 4, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 4, 4);
}
else if($lastElement=='productcategory.php'){
    $addcheck=checkprivilege($menuprivilegearray, 5, 1);
    $editcheck=checkprivilege($menuprivilegearray, 5, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 5, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 5, 4);
}
else if($lastElement=='groupcategory.php'){
    $addcheck=checkprivilege($menuprivilegearray, 6, 1);
    $editcheck=checkprivilege($menuprivilegearray, 6, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 6, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 6, 4);
}
else if($lastElement=='subproductcategory.php'){
    $addcheck=checkprivilege($menuprivilegearray, 7, 1);
    $editcheck=checkprivilege($menuprivilegearray, 7, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 7, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 7, 4);
}
else if($lastElement=='supplier.php'){
    $addcheck=checkprivilege($menuprivilegearray, 8, 1);
    $editcheck=checkprivilege($menuprivilegearray, 8, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 8, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 8, 4);
}
else if($lastElement=='porder.php'){
    $addcheck=checkprivilege($menuprivilegearray, 9, 1);
    $editcheck=checkprivilege($menuprivilegearray, 9, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 9, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 9, 4);
}
else if($lastElement=='grn.php'){
    $addcheck=checkprivilege($menuprivilegearray, 10, 1);
    $editcheck=checkprivilege($menuprivilegearray, 10, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 10, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 10, 4);
}
else if($lastElement=='directsale.php'){
    $addcheck=checkprivilege($menuprivilegearray, 11, 1);
    $editcheck=checkprivilege($menuprivilegearray, 11, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 11, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 11, 4);
}
else if($lastElement=='invoiceview.php'){
    $addcheck=checkprivilege($menuprivilegearray, 12, 1);
    $editcheck=checkprivilege($menuprivilegearray, 12, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 12, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 12, 4);
}
else if($lastElement=='invoicepayment.php'){
    $addcheck=checkprivilege($menuprivilegearray, 13, 1);
    $editcheck=checkprivilege($menuprivilegearray, 13, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 13, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 13, 4);
}
else if($lastElement=='paymentreceipt.php'){
    $addcheck=checkprivilege($menuprivilegearray, 14, 1);
    $editcheck=checkprivilege($menuprivilegearray, 14, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 14, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 14, 4);
}
else if($lastElement=='customer.php'){
    $addcheck=checkprivilege($menuprivilegearray, 15, 1);
    $editcheck=checkprivilege($menuprivilegearray, 15, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 15, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 15, 4);
}
else if($lastElement=='rptgrn.php'){
    $addcheck=checkprivilege($menuprivilegearray, 16, 1);
    $editcheck=checkprivilege($menuprivilegearray, 16, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 16, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 16, 4);
}
else if($lastElement=='rptinvoiceview.php'){
    $addcheck=checkprivilege($menuprivilegearray, 17, 1);
    $editcheck=checkprivilege($menuprivilegearray, 17, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 17, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 17, 4);
}
else if($lastElement=='rptinvoicepayment.php'){
    $addcheck=checkprivilege($menuprivilegearray, 18, 1);
    $editcheck=checkprivilege($menuprivilegearray, 18, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 18, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 18, 4);
}
else if($lastElement=='rptpaymentreceipt.php'){
    $addcheck=checkprivilege($menuprivilegearray, 19, 1);
    $editcheck=checkprivilege($menuprivilegearray, 19, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 19, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 19, 4);
}
else if($lastElement=='rptstock.php'){
    $addcheck=checkprivilege($menuprivilegearray, 20, 1);
    $editcheck=checkprivilege($menuprivilegearray, 20, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 20, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 20, 4);
}
else if($lastElement=='employeedetails.php'){
    $addcheck=checkprivilege($menuprivilegearray, 21, 1);
    $editcheck=checkprivilege($menuprivilegearray, 21, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 21, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 21, 4);
}
else if($lastElement=='area.php'){
    $addcheck=checkprivilege($menuprivilegearray, 22, 1);
    $editcheck=checkprivilege($menuprivilegearray, 22, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 22, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 22, 4);
}
else if($lastElement=='vehical.php'){
    $addcheck=checkprivilege($menuprivilegearray, 23, 1);
    $editcheck=checkprivilege($menuprivilegearray, 23, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 23, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 23, 4);
}
else if($lastElement=='vehicalload.php'){
    $addcheck=checkprivilege($menuprivilegearray, 24, 1);
    $editcheck=checkprivilege($menuprivilegearray, 24, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 24, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 24, 4);
}
else if($lastElement=='billingnew.php'){
    $addcheck=checkprivilege($menuprivilegearray, 25, 1);
    $editcheck=checkprivilege($menuprivilegearray, 25, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 25, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 25, 4);
}
else if($lastElement=='billingpayment.php'){
    $addcheck=checkprivilege($menuprivilegearray, 26, 1);
    $editcheck=checkprivilege($menuprivilegearray, 26, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 26, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 26, 4);
}
else if($lastElement=='dailycashcollection.php'){
    $addcheck=checkprivilege($menuprivilegearray, 28, 1);
    $editcheck=checkprivilege($menuprivilegearray, 28, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 28, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 28, 4);
}
else if($lastElement=='dailycashretransfer.php'){
    $addcheck=checkprivilege($menuprivilegearray, 29, 1);
    $editcheck=checkprivilege($menuprivilegearray, 29, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 29, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 29, 4);
}
else if($lastElement=='vehicletransfer.php'){
    $addcheck=checkprivilege($menuprivilegearray, 30, 1);
    $editcheck=checkprivilege($menuprivilegearray, 30, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 30, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 30, 4);
}
else if($lastElement=='location.php'){
    $addcheck=checkprivilege($menuprivilegearray, 31, 1);
    $editcheck=checkprivilege($menuprivilegearray, 31, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 31, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 31, 4);
}
else if($lastElement=='rptcredit.php'){
    $addcheck=checkprivilege($menuprivilegearray, 32, 1);
    $editcheck=checkprivilege($menuprivilegearray, 32, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 32, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 32, 4);
}
else if($lastElement=='rptcustomercreditanalysis.php'){
    $addcheck=checkprivilege($menuprivilegearray, 41, 1);
    $editcheck=checkprivilege($menuprivilegearray, 41, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 41, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 41, 4);
}
else if($lastElement=='transferstock.php'){
    $addcheck=checkprivilege($menuprivilegearray, 33, 1);
    $editcheck=checkprivilege($menuprivilegearray, 33, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 33, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 33, 4);
}
else if($lastElement=='managestock.php'){
    $addcheck=checkprivilege($menuprivilegearray, 34, 1);
    $editcheck=checkprivilege($menuprivilegearray, 34, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 34, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 34, 4);
}
else if($lastElement=='vatinfo.php'){
    $addcheck=checkprivilege($menuprivilegearray, 35, 1);
    $editcheck=checkprivilege($menuprivilegearray, 35, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 35, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 35, 4);
}
else if($lastElement=='vatinvoice.php'){
    $addcheck=checkprivilege($menuprivilegearray, 36, 1);
    $editcheck=checkprivilege($menuprivilegearray, 36, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 36, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 36, 4);
}
else if($lastElement=='invoicereturnadd.php'){
    $addcheck=checkprivilege($menuprivilegearray, 37, 1);
    $editcheck=checkprivilege($menuprivilegearray, 37, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 37, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 37, 4);
}
else if($lastElement=='invoicereturn.php'){
    $addcheck=checkprivilege($menuprivilegearray, 38, 1);
    $editcheck=checkprivilege($menuprivilegearray, 38, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 38, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 38, 4);
}
else if($lastElement=='rptstockactivitylog.php'){
    $addcheck=checkprivilege($menuprivilegearray, 39, 1);
    $editcheck=checkprivilege($menuprivilegearray, 39, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 39, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 39, 4);
}
else if($lastElement=='quotation.php'){
    $addcheck=checkprivilege($menuprivilegearray, 40, 1);
    $editcheck=checkprivilege($menuprivilegearray, 40, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 40, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 40, 4);
}
else if($lastElement=='rptcustomercreditanalysis.php'){
    $addcheck=checkprivilege($menuprivilegearray, 41, 1);
    $editcheck=checkprivilege($menuprivilegearray, 41, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 41, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 41, 4);
}
else if($lastElement=='rptchequecollection.php'){
    $addcheck=checkprivilege($menuprivilegearray, 42, 1);
    $editcheck=checkprivilege($menuprivilegearray, 42, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 42, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 42, 4);
}
else if($lastElement=='expencestype.php'){
    $addcheck=checkprivilege($menuprivilegearray, 43, 1);
    $editcheck=checkprivilege($menuprivilegearray, 43, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 43, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 43, 4);
}
else if($lastElement=='expensepayment.php'){
    $addcheck=checkprivilege($menuprivilegearray, 44, 1);
    $editcheck=checkprivilege($menuprivilegearray, 44, 2);
    $statuscheck=checkprivilege($menuprivilegearray, 44, 3);
    $deletecheck=checkprivilege($menuprivilegearray, 44, 4);
}


function checkprivilege($arraymenu, $menuID, $type){
    foreach($arraymenu as $array){
        if($array->menuid==$menuID){
            if($type==1){
                return $array->add;
            }
            else if($type==2){
                return $array->edit;
            }
            else if($type==3){
                return $array->statuschange;
            }
            else if($type==4){
                return $array->remove;
            }
        }
    }
}

?>
<textarea class="d-none" id="actiontext"><?php echo $actionJSON; ?></textarea>
<input type="hidden" id="userType" value="<?php echo $_SESSION['type']; ?>">
<nav class="sidenav shadow-right sidenav-light" id="qpSidenav">
    <div class="sidenav-menu">
        <div class="nav accordion" id="accordionSidenav">

            <!-- Core -->
            <div class="sidenav-item mt-3">
                <a class="nav-link<?php if($lastElement=="dashboard.php"){echo ' active';} ?>" href="dashboard.php" data-title="Dashboard">
                    <div class="nav-link-icon"><i data-feather="activity"></i></div>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </div>

            <!-- Sales & Billing -->
            <div class="sidenav-menu-heading">Sales &amp; Billing</div>

            <?php if(menucheck($menuprivilegearray, 11)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="directsale.php"){echo ' active';} ?>" href="directsale.php" data-title="Direct Sale">
                    <div class="nav-link-icon"><i data-feather="monitor"></i></div>
                    <span class="nav-link-text">Direct Sale</span>
                </a>
            </div>
            <?php } ?>

            <?php if(menucheck($menuprivilegearray, 36)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="vatinvoice.php"){echo ' active';} ?>" href="vatinvoice.php" data-title="VAT Invoice">
                    <div class="nav-link-icon"><i data-feather="file"></i></div>
                    <span class="nav-link-text">VAT Invoice</span>
                </a>
            </div>
            <?php } ?>

            <?php if(menucheck($menuprivilegearray, 40)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="quotation.php"){echo ' active';} ?>" href="quotation.php" data-title="Quotation">
                    <div class="nav-link-icon"><i class="fas fa-file-alt"></i></div>
                    <span class="nav-link-text">Quotation</span>
                </a>
            </div>
            <?php } ?>

            <?php if(menucheck($menuprivilegearray, 25)==1 | menucheck($menuprivilegearray, 26)==1) { ?>
            <div class="sidenav-item">
                <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse"
                    data-target="#collapsbilling" aria-expanded="false" aria-controls="collapsbilling">
                    <div class="nav-link-icon"><i class="fas fa-file-alt"></i></div>
                    <span class="nav-link-text">Billing</span>
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php if( $lastElement=="billingpayment.php" | $lastElement=="billingnew.php" ){echo 'show';} ?>"
                    id="collapsbilling" data-parent="#accordionSidenav" data-flyout-title="Billing">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <?php if(menucheck($menuprivilegearray, 25)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="billingnew.php"){echo ' active';} ?>" href="billingnew.php"><span class="nav-link-text">Billing New</span></a>
                        <?php }if(menucheck($menuprivilegearray, 26)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="billingpayment.php"){echo ' active';} ?>" href="billingpayment.php"><span class="nav-link-text">Billing Payment</span></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <?php } ?>

            <?php if(menucheck($menuprivilegearray, 12)==1 | menucheck($menuprivilegearray, 13)==1 | menucheck($menuprivilegearray, 14)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse"
                    data-target="#collapseinvoice" aria-expanded="false" aria-controls="collapseinvoice">
                    <div class="nav-link-icon"><i data-feather="file"></i></div>
                    <span class="nav-link-text">Invoice</span>
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php if($lastElement=="invoiceview.php" | $lastElement=="invoicepayment.php" | $lastElement=="paymentreceipt.php"){echo 'show';} ?>"
                    id="collapseinvoice" data-parent="#accordionSidenav" data-flyout-title="Invoice">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <?php if(menucheck($menuprivilegearray, 12)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="invoiceview.php"){echo ' active';} ?>" href="invoiceview.php"><span class="nav-link-text">Invoice View</span></a>
                        <?php }if(menucheck($menuprivilegearray, 13)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="invoicepayment.php"){echo ' active';} ?>" href="invoicepayment.php"><span class="nav-link-text">Invoice Payment</span></a>
                        <?php }if(menucheck($menuprivilegearray, 14)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="paymentreceipt.php"){echo ' active';} ?>" href="paymentreceipt.php"><span class="nav-link-text">Payment Receipt</span></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <?php } ?>

            <?php if(menucheck($menuprivilegearray, 37)==1 | menucheck($menuprivilegearray, 38)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse"
                    data-target="#collapseinvoicereturn" aria-expanded="false" aria-controls="collapseinvoicereturn">
                    <div class="nav-link-icon"><i data-feather="corner-up-left"></i></div>
                    <span class="nav-link-text">Invoice Return</span>
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php if($lastElement=="invoicereturnadd.php" | $lastElement=="invoicereturn.php"){echo 'show';} ?>"
                    id="collapseinvoicereturn" data-parent="#accordionSidenav" data-flyout-title="Invoice Return">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <?php if(menucheck($menuprivilegearray, 37)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="invoicereturnadd.php"){echo ' active';} ?>" href="invoicereturnadd.php"><span class="nav-link-text">New Return</span></a>
                        <?php }if(menucheck($menuprivilegearray, 38)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="invoicereturn.php"){echo ' active';} ?>" href="invoicereturn.php"><span class="nav-link-text">Return List</span></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <?php } ?>

            <?php if(menucheck($menuprivilegearray, 28)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="dailycashcollection.php"){echo ' active';} ?>" href="dailycashcollection.php" data-title="Daily Cash">
                    <div class="nav-link-icon"><i class="fas fa-money-bill"></i></div>
                    <span class="nav-link-text">Daily Cash</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 29)==1){
                if($type==2){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="dailycashretransfer.php"){echo ' active';} ?>" href="dailycashretransfer.php" data-title="Cash Retransfer">
                    <div class="nav-link-icon"><i class="fas fa-money-bill"></i></div>
                    <span class="nav-link-text">Cash Retransfer</span>
                </a>
            </div>
            <?php } } ?>

            <!-- Expenses -->
            <?php if(menucheck($menuprivilegearray, 43)==1 | menucheck($menuprivilegearray, 44)==1){ ?>
            <div class="sidenav-menu-heading">Expenses</div>
            <div class="sidenav-item">
                <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse"
                    data-target="#collapsexpenses" aria-expanded="false" aria-controls="collapsexpenses">
                    <div class="nav-link-icon"><i class="fas fa-money-bill"></i></div>
                    <span class="nav-link-text">Expenses</span>
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php if($lastElement=="expencestype.php" | $lastElement=="expensepayment.php"){echo 'show';} ?>"
                    id="collapsexpenses" data-parent="#accordionSidenav" data-flyout-title="Expenses">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <?php if(menucheck($menuprivilegearray, 43)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="expencestype.php"){echo ' active';} ?>" href="expencestype.php"><span class="nav-link-text">Expenses Type</span></a>
                        <?php } if(menucheck($menuprivilegearray, 44)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="expensepayment.php"){echo ' active';} ?>" href="expensepayment.php"><span class="nav-link-text">Expenses Payment</span></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <?php } ?>

            <!-- Procurement & Stock -->
            <?php if(menucheck($menuprivilegearray, 9)==1 | menucheck($menuprivilegearray, 10)==1 | menucheck($menuprivilegearray, 33)==1 | menucheck($menuprivilegearray, 34)==1 | menucheck($menuprivilegearray, 23)==1 | menucheck($menuprivilegearray, 24)==1 | menucheck($menuprivilegearray, 30)==1){ ?>
            <div class="sidenav-menu-heading">Procurement &amp; Stock</div>

            <?php if(menucheck($menuprivilegearray, 9)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="porder.php"){echo ' active';} ?>" href="porder.php" data-title="Purchase Order">
                    <div class="nav-link-icon"><i class="fas fa-truck"></i></div>
                    <span class="nav-link-text">Purchase Order</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 10)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="grn.php"){echo ' active';} ?>" href="grn.php" data-title="GRN Info">
                    <div class="nav-link-icon"><i class="fas fa-warehouse"></i></div>
                    <span class="nav-link-text">GRN Info</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 33)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="stocktransfer.php"){echo ' active';} ?>" href="stocktransfer.php" data-title="Stock Transfer">
                    <div class="nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                    <span class="nav-link-text">Stock Transfer</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 34)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="managestock.php"){echo ' active';} ?>" href="managestock.php" data-title="Manage Stock">
                    <div class="nav-link-icon"><i class="fas fa-file-alt"></i></div>
                    <span class="nav-link-text">Manage Stock</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 39)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="rptstockactivitylog.php"){echo ' active';} ?>" href="rptstockactivitylog.php" data-title="Stock Activity Log">
                    <div class="nav-link-icon"><i class="fas fa-history"></i></div>
                    <span class="nav-link-text">Stock Activity Log</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 23)==1 | menucheck($menuprivilegearray, 24)==1 | menucheck($menuprivilegearray, 30)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse"
                    data-target="#collapsvehical" aria-expanded="false" aria-controls="collapsvehical">
                    <div class="nav-link-icon"><i class="fas fa-truck"></i></div>
                    <span class="nav-link-text">Vehicle</span>
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php if($lastElement=="vehical.php" | $lastElement=="vehicalload.php" | $lastElement=="vehicletransfer.php"){echo 'show';} ?>"
                    id="collapsvehical" data-parent="#accordionSidenav" data-flyout-title="Vehicle">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <?php if(menucheck($menuprivilegearray, 23)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="vehical.php"){echo ' active';} ?>" href="vehical.php"><span class="nav-link-text">Vehicle View</span></a>
                        <?php }if(menucheck($menuprivilegearray, 24)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="vehicalload.php"){echo ' active';} ?>" href="vehicalload.php"><span class="nav-link-text">Vehicle Load</span></a>
                        <?php }if(menucheck($menuprivilegearray, 30)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="vehicletransfer.php"){echo ' active';} ?>" href="vehicletransfer.php"><span class="nav-link-text">Vehicle Transfer</span></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <?php } } ?>

            <!-- Catalog -->
            <?php if(menucheck($menuprivilegearray, 4)==1 | menucheck($menuprivilegearray, 5)==1 | menucheck($menuprivilegearray, 6)==1 | menucheck($menuprivilegearray, 7)==1){ ?>
            <div class="sidenav-menu-heading">Catalog</div>
            <div class="sidenav-item">
                <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse"
                    data-target="#collapseproduct" aria-expanded="false" aria-controls="collapseproduct">
                    <div class="nav-link-icon"><i data-feather="shopping-cart"></i></div>
                    <span class="nav-link-text">Product</span>
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php if($lastElement=="product.php" | $lastElement=="productcategory.php" | $lastElement=="groupcategory.php" | $lastElement=="subproductcategory.php"){echo 'show';} ?>"
                    id="collapseproduct" data-parent="#accordionSidenav" data-flyout-title="Product">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <?php if(menucheck($menuprivilegearray, 4)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="product.php"){echo ' active';} ?>" href="product.php"><span class="nav-link-text">Product</span></a>
                        <?php }if(menucheck($menuprivilegearray, 5)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="productcategory.php"){echo ' active';} ?>" href="productcategory.php"><span class="nav-link-text">Product Category</span></a>
                        <?php }if(menucheck($menuprivilegearray, 6)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="groupcategory.php"){echo ' active';} ?>" href="groupcategory.php"><span class="nav-link-text">Product Group Category</span></a>
                        <?php }if(menucheck($menuprivilegearray, 7)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="subproductcategory.php"){echo ' active';} ?>" href="subproductcategory.php"><span class="nav-link-text">Product Sub Category</span></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <?php } ?>

            <!-- Business Partners -->
            <?php if(menucheck($menuprivilegearray, 15)==1 | menucheck($menuprivilegearray, 8)==1){ ?>
            <div class="sidenav-menu-heading">Business Partners</div>
            <?php if(menucheck($menuprivilegearray, 15)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="customer.php"){echo ' active';} ?>" href="customer.php" data-title="Customers">
                    <div class="nav-link-icon"><i data-feather="users"></i></div>
                    <span class="nav-link-text">Customers</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 8)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="supplier.php"){echo ' active';} ?>" href="supplier.php" data-title="Supplier">
                    <div class="nav-link-icon"><i data-feather="users"></i></div>
                    <span class="nav-link-text">Supplier</span>
                </a>
            </div>
            <?php } } ?>

            <!-- Reports -->
            <?php if(menucheck($menuprivilegearray, 16)==1 | menucheck($menuprivilegearray, 17)==1 | menucheck($menuprivilegearray, 18)==1 | menucheck($menuprivilegearray, 19)==1 | menucheck($menuprivilegearray, 20)==1 | menucheck($menuprivilegearray, 32)==1 | menucheck($menuprivilegearray, 41)==1 | menucheck($menuprivilegearray, 42)==1){ ?>
            <div class="sidenav-menu-heading">Reports</div>
            <div class="sidenav-item">
                <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse"
                    data-target="#collapsreports" aria-expanded="false" aria-controls="collapsreports">
                    <div class="nav-link-icon"><i data-feather="file"></i></div>
                    <span class="nav-link-text">Reports</span>
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php if($lastElement=="rptgrn.php" | $lastElement=="rptinvoiceview.php" | $lastElement=="rptinvoicepayment.php" | $lastElement=="rptpaymentreceipt.php" | $lastElement=="rptstock.php" | $lastElement=="rptcredit.php" | $lastElement=="rptcustomercreditanalysis.php"){echo 'show';} ?>"
                    id="collapsreports" data-parent="#accordionSidenav" data-flyout-title="Reports">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <?php if(menucheck($menuprivilegearray, 16)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="rptgrn.php"){echo ' active';} ?>" href="rptgrn.php"><span class="nav-link-text">GRN Report</span></a>
                        <?php }if(menucheck($menuprivilegearray, 17)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="rptinvoiceview.php"){echo ' active';} ?>" href="rptinvoiceview.php"><span class="nav-link-text">Invoice Report</span></a>
                        <?php }if(menucheck($menuprivilegearray, 18)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="rptinvoicepayment.php"){echo ' active';} ?>" href="rptinvoicepayment.php"><span class="nav-link-text">Invoice Payment Report</span></a>
                        <?php }if(menucheck($menuprivilegearray, 19)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="rptpaymentreceipt.php"){echo ' active';} ?>" href="rptpaymentreceipt.php"><span class="nav-link-text">Payment Receipt Report</span></a>
                        <?php }if(menucheck($menuprivilegearray, 20)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="rptstock.php"){echo ' active';} ?>" href="rptstock.php"><span class="nav-link-text">Stock Report</span></a>
                        <?php }if(menucheck($menuprivilegearray, 32)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="rptcredit.php"){echo ' active';} ?>" href="rptcredit.php"><span class="nav-link-text">Credit Report</span></a>
                        <?php }if(menucheck($menuprivilegearray, 41)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="rptcustomercreditanalysis.php"){echo ' active';} ?>" href="rptcustomercreditanalysis.php"><span class="nav-link-text">Customer Credit Analysis</span></a>
                        <?php }if(menucheck($menuprivilegearray, 42)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="rptchequecollection.php"){echo ' active';} ?>" href="rptchequecollection.php"><span class="nav-link-text">Cheque Collection Report</span></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <?php } ?>

            <!-- Administration -->
            <?php if(menucheck($menuprivilegearray, 21)==1 | menucheck($menuprivilegearray, 22)==1 | menucheck($menuprivilegearray, 31)==1 | menucheck($menuprivilegearray, 35)==1 | menucheck($menuprivilegearray, 1)==1 | menucheck($menuprivilegearray, 2)==1 | menucheck($menuprivilegearray, 3)==1){ ?>
            <div class="sidenav-menu-heading">Administration</div>

            <?php if(menucheck($menuprivilegearray, 21)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="employeedetails.php"){echo ' active';} ?>" href="employeedetails.php" data-title="Employee">
                    <div class="nav-link-icon"><i data-feather="users"></i></div>
                    <span class="nav-link-text">Employee</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 22)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="area.php"){echo ' active';} ?>" href="area.php" data-title="Area">
                    <div class="nav-link-icon"><i class="far fa-map"></i></div>
                    <span class="nav-link-text">Area</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 31)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="location.php"){echo ' active';} ?>" href="location.php" data-title="Location">
                    <div class="nav-link-icon"><i class="fa fa-map-marker"></i></div>
                    <span class="nav-link-text">Location</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 35)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link<?php if($lastElement=="vatinfo.php"){echo ' active';} ?>" href="vatinfo.php" data-title="VAT Information">
                    <div class="nav-link-icon"><i class="fas fa-comments-dollar"></i></div>
                    <span class="nav-link-text">VAT Information</span>
                </a>
            </div>
            <?php } if(menucheck($menuprivilegearray, 1)==1 | menucheck($menuprivilegearray, 2)==1 | menucheck($menuprivilegearray, 3)==1){ ?>
            <div class="sidenav-item">
                <a class="nav-link collapsed" href="javascript:void(0);" data-toggle="collapse"
                    data-target="#collapseUser" aria-expanded="false" aria-controls="collapseUser">
                    <div class="nav-link-icon"><i data-feather="user"></i></div>
                    <span class="nav-link-text">User Account</span>
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php if($lastElement=="useraccount.php" | $lastElement=="usertype.php" | $lastElement=="userprivilege.php"){echo 'show';} ?>"
                    id="collapseUser" data-parent="#accordionSidenav" data-flyout-title="User Account">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <?php if(menucheck($menuprivilegearray, 1)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="useraccount.php"){echo ' active';} ?>" href="useraccount.php"><span class="nav-link-text">User Account</span></a>
                        <?php }if(menucheck($menuprivilegearray, 2)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="usertype.php"){echo ' active';} ?>" href="usertype.php"><span class="nav-link-text">Type</span></a>
                        <?php }if(menucheck($menuprivilegearray, 3)==1){ ?>
                        <a class="nav-link<?php if($lastElement=="userprivilege.php"){echo ' active';} ?>" href="userprivilege.php"><span class="nav-link-text">Privilege</span></a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <?php }} ?>

        </div>
    </div>
    <div class="sidenav-footer bg-laugfs d-flex align-items-center">
        <div class="sidenav-footer-content">
            <div class="sidenav-footer-subtitle">Logged in as:</div>
            <div class="sidenav-footer-title" data-title="<?php echo ucfirst($_SESSION['name']); ?>"><?php echo ucfirst($_SESSION['name']); ?></div>
        </div>
    </div>
</nav>