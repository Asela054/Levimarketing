<?php 
include "include/header.php"; 

include "include/topnavbar.php"; 

$sqlmaincat = "SELECT `idtbl_product_category`, `category` FROM `tbl_product_category` WHERE `status`= '1'";
$resultmaincat = $conn->query($sqlmaincat);
?>
<style>
    .pointer {cursor: pointer;}
    #tableproductpricelist tr:focus {
        outline: 2px solid #3a86ff; /* Blue outline */
        box-shadow: inset 0 0 3px rgba(0,0,0,0.2);
    }
</style>
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <?php include "include/menubar.php"; ?>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid p-0 p-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div id="maindiv" class="col-sm-12 col-md-12 col-lg-7 col-xl-7" style="border-right: 1px dotted #000;">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control form-control" id="barcode" name="barcode">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fas fa-barcode"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion" id="accordionExample">
                                    <div class="card shadow-none border-0">
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body border rounded" style="height: 460px; overflow-y:auto;" id="style-2">
                                                <div class="row row-cols-1 row-cols-md-4">
                                                    <?php if($resultmaincat->num_rows > 0) {while ($rowmaincat = $resultmaincat-> fetch_assoc()) { ?>
                                                    <div class="col mb-4 categorydiv" id="<?php echo $rowmaincat['idtbl_product_category'] ?>">
                                                        <div class="card h-100 shadow-none bg-primary border-primary">
                                                            <div class="card-body p-2 text-center pointer">
                                                                <h4 class="text-light font-weight-light">
                                                                    <?php echo $rowmaincat['category'] ?></h4>
                                                                <hr class="border-light my-1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php }} ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-none border-0">
                                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                            data-parent="#accordionExample">
                                            <div class="card-body border rounded">
                                                <div class="row">
                                                    <div class="col-12 text-right pb-3">
                                                        <button class="btn btn-danger px-4" id="btnbackone"><i class="fa fa-arrow-left mr-1"></i>Back</button>
                                                    </div>
                                                    <div class="col-12" id="divsubcategory">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <img src="images/spinner.gif" class="img-fluid">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-none border-0">
                                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordionExample">
                                            <div class="card-body border rounded">
                                                <div  class="row">
                                                    <div class="col-12 text-right pb-3">
                                                        <button class="btn btn-danger px-4" id="btnbacktwo"><i class="fa fa-arrow-left mr-1"></i>Back</button>
                                                    </div>
                                                    <div class="col-12" id="divgroupcategory">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <img src="images/spinner.gif" class="img-fluid">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-none border-0">
                                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                            data-parent="#accordionExample">
                                            <div class="card-body border rounded">
                                                <div  class="row">
                                                    <div class="col-12 text-right pb-3">
                                                        <button class="btn btn-danger px-4" id="btnbackthree"><i class="fa fa-arrow-left mr-1"></i>Back</button>
                                                    </div>
                                                    <div class="col-12" id="divproductlist">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <img src="images/spinner.gif" class="img-fluid">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                <h6 class="title-style mb-3"><span>Cart Information</span></h6>
                                <div id="style-17" style="height: 295px; overflow-y:auto;">
                                    <table class="table table-striped table-sm" id = "carttable">
                                        <thead>
                                            <tr>
                                                <th>PRODUCT</th>
                                                <th class="text-center">QTY</th>
                                                <th class="text-right">SALE</th>
                                                <th class="text-right">DISCOUNT</th>
                                                <th class="text-right">TOTAL</th>
                                                <th class="d-none">productid</th>
                                                <th class="d-none">productcode</th>
                                                <th class="d-none">sale</th>
                                                <th class="d-none">unit</th>
                                                <th class="d-none">total</th>
                                                <th class="d-none">dispre</th>
                                                <th class="d-none">distotal</th>
                                                <th class="d-none">nettotal</th>
                                                <th class="d-none">editstatus</th>
                                                <th class="d-none">editedprice</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <h5 id = "labeltotal">Gross Amount: 0.00</h5>
                                        <h5 id = "labeldistotal">Discount: 0.00</h5>
                                        <hr class="my-1">
                                        <div id = "labelnettotal" class="display-4">0.00</div>
                                        <input type="hidden" id = "hiddenfulltotal">
                                        <input type="hidden" id = "hiddenfulldistotal">
                                        <input type="hidden" id = "hiddenfullnettotal">
                                        <input type="hidden" id = "priceeditstatus">
                                        <input type="hidden" name="hideapproveuser" id="hideapproveuser" value="0">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="hiddencategoryID" name="hiddencategoryID">
                            <input type="hidden" id="hiddensubID" name="hiddensubID">
                            <input type="hidden" id="hiddengroupID" name="hiddengroupID">
                            <input type="hidden" id="saletype" name="saletype">
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-right">
                                <button id = "paymentbtn" class="btn btn-danger btn-sm"><i class="fas fa-cash-register fa-3x mr-2"></i>
                                    <h1 class="font-weight-normal mt-2 text-light">PAYMENT</h1>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<!-- Modal Qty -->
<div class="modal fade" id="modalqty" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">ADD TO LIST</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card shadow-none bg-primary border-primary mb-3">
                    <div class="card-body p-2 text-center pointer">
                        <h4 class="text-light font-weight-light mb-0" id="selectproduct">Test</h4>
                    </div>
                </div>
                <form id="formqtyadd">
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold text-dark mb-2">
                            Quick Add
                        </label>
                        <div class="btn-group d-flex w-100" role="group">
                            <button type="button"
                                class="btn btn-outline-danger btn-sm qtyquickbtn flex-fill"
                                data-qty="0.25">
                                250ml
                            </button>
                            <button type="button"
                                class="btn btn-outline-danger btn-sm qtyquickbtn flex-fill"
                                data-qty="0.5">
                                500ml
                            </button>
                            <button type="button"
                                class="btn btn-outline-danger btn-sm qtyquickbtn flex-fill"
                                data-qty="0.75">
                                750ml
                            </button>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold text-dark mb-1">
                            Qty
                        </label>
                        <input type="tel"
                            name="qtycount"
                            id="qtycount"
                            class="form-control"
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold text-dark mb-1">
                            Sale Price (Qty &times; Unit Price)
                        </label>
                        <input type="tel"
                            name="salepriceedit"
                            id="salepriceedit"
                            class="form-control"
                            readonly
                            tabindex="-1">
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox"
                                class="custom-control-input"
                                id="enablepriceedit">
                            <label class="custom-control-label small font-weight-bold text-dark"
                                for="enablepriceedit">
                                Edit This Price
                            </label>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold text-dark mb-1">
                            Discount*
                        </label>
                        <div class="input-group flex-nowrap">
                            <input type="number"
                                name="discountpresentage"
                                id="discountpresentage"
                                class="form-control"
                                step=".01"
                                value="0"
                                min="0"
                                required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="addon-wrapping">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0 mt-3 text-right">
                        <button type="button"
                            class="btn btn-danger"
                            id="btnaddtolist">
                            ADD TO LIST
                        </button>
                    </div>
                    <input type="submit" class="d-none" id="btnhideqtysubmit">
                    <input type="reset" class="d-none" id="btnhideqtyreset">
                    <input type="hidden" name="hideproductid" id="hideproductid">
                    <input type="hidden" name="hideproduct" id="hideproduct">
                    <input type="hidden" name="hideproductcode" id="hideproductcode">
                    <input type="hidden" name="hideproductunit" id="hideproductunit">
                    <input type="hidden" name="hideproductsale" id="hideproductsale">
                    <input type="hidden" name="hideproductstock" id="hideproductstock">
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Payment -->
<div class="modal fade" id="modalpayment" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">PAYMENT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                        <h3 class="display-4 text-right" id="htmlbillamount"></h3>
                        <hr class="m-0">
                        <form id="paymentform" autocomplete="off">
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Bill Method</label>
                                <select name="billtype" id="billtype" class="form-control">
                                    <option value="1" selected>Cash</option>
                                    <option value="2">Credit</option>
                                    <option value="3">Quotation</option>
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <div class="collapse" id="collapsecustomerinfo">
                                    <div class="card card-body shadow-none border-0 p-0">
                                        <div class="form-group mb-1">
                                            <label class="small font-weight-bold text-dark">Name</label>
                                            <input type="text" id="cusname" name="cusname" class="form-control" required>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="small font-weight-bold text-dark">NIC</label>
                                            <input type="text" id="cusnic" name="cusnic" class="form-control">
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="small font-weight-bold text-dark">Mobile No</label>
                                            <input type="tel" id="cusmobile" name="cusmobile" class="form-control" required>
                                        </div>
                                        <div class="form-group mb-1" id="divcusoutstanding" style="display:none;">
                                            <label class="small font-weight-bold text-dark">Customer Outstanding Balance (All Invoices)</label>
                                            <input type="text" id="cusoutstanding" class="form-control text-danger font-weight-bold" readonly tabindex="-1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Payment Method</label>
                                <select name="paymentmethod" id="paymentmethod" class="form-control">
                                    <option value="1" selected>Cash Payment</option>
                                    <option value="2">Card Payment</option>
                                    <option value="3">Cheque Payment</option>
                                    <option value="4">Online Transfer</option>
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Cash / Cheque</label>
                                <input type="tel" id="amount" name="amount" class="form-control" autofocus required>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Bank Name</label>
                                <input type="text" id="bank" name="bank" class="form-control" readonly>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Cheque No</label>
                                <input type="tel" id="chequeno" name="chequeno" class="form-control" readonly>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Cheque Date</label>
                                    <input type="date" id="chequedate" name="chequedate" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-row mb-1" id="divcardlast4" style="display:none;">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Card Last 4 Digits</label>
                                    <input type="tel"
                                        id="cardlast4"
                                        name="cardlast4"
                                        class="form-control"
                                        maxlength="4"
                                        inputmode="numeric"
                                        pattern="\d{4}"
                                        placeholder="e.g. 1234">
                                </div>
                            </div>
                            <div class="form-row mb-1" id="divonlineref" style="display:none;">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Reference Number</label>
                                    <input type="text"
                                        id="onlineref"
                                        name="onlineref"
                                        class="form-control"
                                        placeholder="e.g. Bank transfer ref no">
                                </div>
                            </div>
                            <div class="form-group mt-3 text-right">
                                <button type="button" class="btn btn-danger" id="btnpayaddlist">ADD PAYMENT</button>
                                <input type="submit" id="btnhidepayaddlist" class="d-none">
                                <input type="reset" id="btnhidepayresetlist" class="d-none">
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                        <table class="table table-striped table-bordered" id="tablepayment">
                            <thead>
                                <tr>
                                    <th class="d-none">paymethod</th>
                                    <th>PAY METHOD</th>
                                    <th>BANK</th>
                                    <th>CHEQUE NO</th>
                                    <th>CHEQUE DATE</th>
                                    <th>CARD / REF NO</th>
                                    <th class="d-none">total</th>
                                    <th class="text-right">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <hr>
                        <h1 class="display-4 text-right" id="paynettotal"></h1>
                        <h3 class="font-weight-normal text-right text-danger" id="paybalance"></h3>
                        <input type="hidden" name="hidepaymenttotal" id="hidepaymenttotal" value="0">
                        <input type="hidden" name="hidecustomerID" id="hidecustomerID" value="0">
                        <hr>
                        <button type="button" class="btn btn-secondary fa-pull-right" id="paymentcomplete" disabled><i class="fas fa-save mr-2"></i>DONE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Already Cutomers Modal-->
<div class="modal fade" id="alreadyCustomerModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="oLevel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header p-0 p-2">
                <h5 class="modal-title" id="oLevelTitle">ALREADY CUSTOMERS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <form id="alreadycusform">
                            <label class="small font-weight-bold text-dark">Search Customer</label>
                            <input type="text"
                                id="externalsearch"
                                name="externalsearch"
                                class="form-control"
                                placeholder="Type Name, NIC or Mobile...">
                        </form>

                        <hr>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <table id="alreadyCustomerTable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NAME</th>
                                    <th>NIC</th>
                                    <th>MOBILE</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12"><button class="btn btn-outline-danger btn-sm" id="btnAddToDB">ADD TO CUSTOMER LIST</button></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Invoice Receipt -->
<div class="modal fade" id="modalinvoicereceipt" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewreceiptprint"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm fa-pull-right" id="btnreceiptprint"><i class="fas fa-print"></i>&nbsp;Print Receipt</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Invoice Pos Receipt -->
<div class="modal fade" id="modalinvoicereceiptpos" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewreceiptprintpos"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm fa-pull-right" id="btnreceiptprintpos"><i class="fas fa-print"></i>&nbsp;Print Receipt</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Retail Whole Sale -->
<div class="modal fade" id="modalretailwholesale" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <button id="btnretailsale" type="button" class="btn btn-primary btn-sm w-100 mb-3"><i class="fas fa-cash-register fa-3x mr-2"></i>
                            <h1 class="font-weight-normal mt-2 text-light">RETAIL SALE</h1>
                        </button>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <button id="btnwholesale" type="button" class="btn btn-danger btn-sm w-100"><i class="fas fa-cash-register fa-3x mr-2"></i>
                            <h1 class="font-weight-normal mt-2 text-light">WHOLE SALE</h1>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Cash Or Credit -->
<div class="modal fade" id="modalcashcredit" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <button id="btncash" type="button" class="btn btn-primary btn-sm w-100 mb-3"><i class="fas fa-cash-register fa-3x mr-2"></i>
                            <h1 class="font-weight-normal mt-2 text-light">CASH</h1>
                        </button>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <button id="btncredit" type="button" class="btn btn-danger btn-sm w-100"><i class="fas fa-cash-register fa-3x mr-2"></i>
                            <h1 class="font-weight-normal mt-2 text-light">CREDIT</h1>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#modalretailwholesale').modal('show');
        $('#qtycount').keyboard();
        $('#salepriceedit').keyboard();
        $('#discountpresentage').keyboard();
        $('#amount').keyboard();
        $('#bank').keyboard();
        $('#chequeno').keyboard();
        $('#externalsearch').keyboard();
        $('#cusname').keyboard();
        $('#cusnic').keyboard();
        $('#cusmobile').keyboard();
        $('#barcode').keyboard();
        $('#cardlast4').keyboard(); 
        $('#onlineref').keyboard();

        $('#btnretailsale').click(function(){
            $('#saletype').val('1');
            $('#modalretailwholesale').modal('hide');
            $('#barcode').focus();
        });
        $('#btnwholesale').click(function(){
            $('#saletype').val('2');
            $('#modalretailwholesale').modal('hide');
            $('#barcode').focus();
        });

        //Barcode Process Start
        $("#barcode").keyup(function(event) {            
            if (event.keyCode === 13) {
                var barcode = $(this).val();
                var saletype = $('#saletype').val();

                if(barcode!=''){
                    $.ajax({
                        method: "POST",
                        data: {
                            barcode: barcode,
                            saletype: saletype
                        },
                        url: "getprocess/getproductlistaccobarcode.php",
                        success: function (result) { //alert(result)
                            var obj = JSON.parse(result);
                            
                            $('#divproductlist').html(obj.html);
                            productlistoption();
                            $("#collapseFour").collapse('show');
                            $('#hiddencategoryID').val(obj.categoryID);
                            getgroupcategorylist(obj.categoryID);
                            $('#hiddengroupID').val(obj.groupcategoryID);
                            $("#barcode").val('');

                            setTimeout(function() {
                                var $firstRow = $("#tableproductpricelist tbody tr.classfocus:first");
                                if ($firstRow.length) {
                                    $firstRow.attr('tabindex', '0').focus();
                                }
                            }, 100);
                        }
                    });
                }
            }
        });
        //Barcode Process End
        
        var dataTable = $('#alreadyCustomerTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            bFilter: false,
            ajax: {
                url: "scripts/alreadycustomerlist.php",
                type: "POST",
                data: function(d) {
                    d.searchbox = $('#externalsearch').val();
                }
            },
            order: [[0, "asc"]],
            columns: [
                { data: "idtbl_customer" },
                { data: "name" },
                { data: "nic" },
                { data: "phone" }
            ]
        });
        var typingTimer;

        $('#externalsearch').on('keyup', function () {
            clearTimeout(typingTimer);

            typingTimer = setTimeout(function () {
                dataTable.ajax.reload();
            }, 300); // Wait 300ms after user stops typing
        });
        $('#customersearchsubmit').click(function(){
            if (!$("#alreadycusform")[0].checkValidity()) {
                // If the form is invalid, submit it. The form won't actually submit;
                // this will just cause the browser to display the native HTML5 error messages.
                $("#hidecustomersearchsubmit").click();
            } else {
                dataTable.draw();
            }
        });
        $('.categorydiv').click(function(){
            var categoryID=$(this).attr('id');
            $('#hiddencategoryID').val(categoryID);

            $("#collapseThree").collapse('show');
            getgroupcategorylist(categoryID);            
        });
        $('#btnbackone').click(function(){
            $("#collapseOne").collapse('show');
        });        
        $('#btnbacktwo').click(function(){
            // $("#collapseTwo").collapse('show');
            $("#collapseOne").collapse('show');
        });        
        $('#btnbackthree').click(function(){
            $("#collapseThree").collapse('show');
        });

       // Shared calc so quick-qty buttons and manual typing stay in sync
        function recalcSalePrice(){
            var sale = parseFloat($('#hideproductsale').val());
            var qty = parseFloat($('#qtycount').val());

            if(!isNaN(sale) && !isNaN(qty)){
                var autoamount = sale * qty;
                $('#salepriceedit').val(autoamount.toFixed(2));
            }
            else{
                $('#salepriceedit').val('');
            }
        }

        // Sale Price field auto-calculates as (unit price x qty) while qty is typed.
        // It stays readonly until the checkbox is ticked; ticking it just unlocks
        // the already-calculated value so the cashier can adjust it.
        $('#qtycount').on('input', function(){
            recalcSalePrice();
        });

        // Quick-add buttons (250ml / 500ml / 750ml): ADD to whatever qty is already
        // entered rather than overwriting it, then refresh the price the same way
        // manual typing does.
        $(document).on('click', '.qtyquickbtn', function(){
            var addqty = parseFloat($(this).data('qty'));
            var currentqty = parseFloat($('#qtycount').val());

            if(isNaN(currentqty)){
                currentqty = 0;
            }

            // toFixed(2) then parseFloat back guards against JS floating point
            // artifacts like 1 + 0.25 = 1.2500000000000002
            var newqty = parseFloat((currentqty + addqty).toFixed(2));

            $('#qtycount').val(newqty);
            recalcSalePrice();

            // If price editing was on, this recalculated it to the auto amount;
            // keep it editable and re-select so the cashier can adjust if needed
            if($('#enablepriceedit').is(':checked')){
                $('#salepriceedit').focus().select();
            }
            else{
                $('#qtycount').focus();
            }
        });

        // Edit-price checkbox just unlocks the field for manual changes; it does not clear/reset it
        $('#enablepriceedit').change(function(){
            if($(this).is(':checked')){
                $('#salepriceedit').prop('readonly', false).focus().select();
            }
            else{
                $('#salepriceedit').prop('readonly', true);
                // Recalculate back to the auto amount since edit was turned off
                recalcSalePrice();
            }
        });

        $('#qtycount').keypress(function (e) {
            var key = e.which;
            if(key == 13){
                if($('#enablepriceedit').is(':checked')){
                    $('#salepriceedit').focus();
                }
                else{
                    $('#discountpresentage').focus();
                }
                return false;  
            }
        });
        $('#salepriceedit').keypress(function (e) {
            var key = e.which;
            if(key == 13){
                $('#discountpresentage').focus();
                return false;  
            }
        });
        $('#discountpresentage').keypress(function (e) {
            var key = e.which;
            if(key == 13){
                $("#btnaddtolist").click();
                return false;  
            }
        });
        $("#btnaddtolist").click(function () {
            if (!$("#formqtyadd")[0].checkValidity()) {
                // If the form is invalid, submit it. The form won't actually submit;
                // this will just cause the browser to display the native HTML5 error messages.
                $("#btnhideqtysubmit").click();
            } else {
                var productID=$('#hideproductid').val();
                var product=$('#hideproduct').val();
                var productcode=$('#hideproductcode').val();
                var unit=parseFloat($('#hideproductunit').val());
                var unitsale=parseFloat($('#hideproductsale').val()); // per-unit actual price, used only to auto-calc the line amount
                var qty=parseFloat($('#qtycount').val());
                var stock=parseFloat($('#hideproductstock').val());

                if(!isNaN(stock) && qty > stock){
                    alert('Insufficient stock for "' + product + '". Only ' + stock + ' available.');
                    $('#qtycount').focus().select();
                    return;
                }
                var discountpresentage=parseFloat($('#discountpresentage').val());

                var priceedited = $('#enablepriceedit').is(':checked');
                var autolineamount = parseFloat((unitsale*qty).toFixed(2)); // saleprice: actual price x qty, a LINE AMOUNT
                var editedlineamount = parseFloat($('#salepriceedit').val()); // editedprice: cashier-typed LINE AMOUNT
                var editedprice = 0;
                var pricefortotal = autolineamount; // default: total uses the actual-price line amount

                if(priceedited && !isNaN(editedlineamount) && editedlineamount>0){
                    editedprice = editedlineamount; // store exactly what was typed, as a line amount
                    pricefortotal = editedprice; // total uses the edited line amount directly, no further x qty
                    $('#priceeditstatus').val('1');
                    var classname='table-info';
                    var editstatus='1';
                }
                else{
                    editedprice = 0;
                    pricefortotal = autolineamount;
                    var classname='';
                    var editstatus='0';
                }

                // pricefortotal is already a line amount (qty baked in), so total = pricefortotal, not pricefortotal*qty
                var total = parseFloat(pricefortotal);
                var discountamount = parseFloat((total*discountpresentage)/100);
                var totalwithdis = parseFloat(total-discountamount);
                var showtotal = addCommas(parseFloat(totalwithdis).toFixed(2));

                // SALE column displays the per-unit price for reference (qty x this = the line amount used)
                $('#carttable > tbody:last').append('<tr class="pointer '+classname+'"><td>' + product + '</td><td class="text-center">' + qty + '</td><td class="text-right">' + addCommas(parseFloat(unitsale).toFixed(2)) + '</td><td class="text-right">' + addCommas(parseFloat(discountamount).toFixed(2)) + '</td><td class="text-right">' + showtotal + '</td><td class="d-none">' + productID + '</td><td class="d-none">' + productcode + '</td><td class="d-none sale">' + autolineamount + '</td><td class="d-none">' + unit + '</td><td class="total d-none">' + total + '</td><td class="d-none">' + discountpresentage + '</td><td class="distotal d-none">' + discountamount + '</td><td class="nettotal d-none">' + totalwithdis + '</td><td class="d-none editstatus">' + editstatus + '</td><td class="d-none editedprice">' + editedprice + '</td></tr>');

                var sum = 0;
                $(".total").each(function () {
                    sum += parseFloat($(this).text());
                });

                var showsum = addCommas(parseFloat(sum).toFixed(2));

                var dissum = 0;
                $(".distotal").each(function () {
                    dissum += parseFloat($(this).text());
                });

                var showdissum = addCommas(parseFloat(dissum).toFixed(2));

                var netsum = 0;
                $(".nettotal").each(function () {
                    netsum += parseFloat($(this).text());
                });

                var shownetsum = addCommas(parseFloat(netsum).toFixed(2));

                $('#labeltotal').html('Gross Amount: ' + showsum);
                $('#labeldistotal').html('Discount: ' + showdissum);
                $('#labelnettotal').html(shownetsum);
                $('#htmlbillamount').html('Rs. '+shownetsum);
                $('#hiddenfulltotal').val(sum);
                $('#hiddenfulldistotal').val(dissum);
                $('#hiddenfullnettotal').val(netsum);
                $('#btnhideqtyreset').click();
                $('#enablepriceedit').prop('checked', false);
                $('#salepriceedit').prop('readonly', true).val('');
                $('#modalqty').modal('hide');
                $("#collapseOne").collapse('show');
                $("#barcode").focus();
            }
        });
        $('#carttable').on('click', 'tr', function () {
            var r = confirm("Are you sure, You want to remove this product ? ");
            if (r == true) {
                $(this).closest('tr').remove();

                var sum = 0;
                $(".total").each(function () {
                    sum += parseFloat($(this).text());
                });

                var showsum = addCommas(parseFloat(sum).toFixed(2));

                $('#labeltotal').html('Rs. ' + showsum);
                $('#hiddenfulltotal').val(sum);
                $('#btnhideqtyreset').click();
                $('#modalqty').modal('hide');
                $("#collapseOne").collapse('show');
            }
        });
        function resetPaymentModalFields(){
            $('#hidepaymenttotal').val('0');
            $('#tablepayment tbody').empty();
            $('#paynettotal').html('');
            $('#paybalance').html('');
            $('#amount').val('');
            $('#bank').val('').prop('readonly', true).prop('required', false);
            $('#chequeno').val('').prop('readonly', true).prop('required', false);
            $('#chequedate').val('').prop('readonly', true).prop('required', false);
            $('#cardlast4').val('').prop('required', false);
            $('#divcardlast4').hide();
            $('#onlineref').val('').prop('required', false);   // NEW
            $('#divonlineref').hide();                          // NEW
            $('#paymentcomplete').prop('disabled', true);
            $("input[type=radio][name='paymentmethod']").prop('checked', false).parent().removeClass('active');
        }

        $('#paymentbtn').click(function(){
            $('#modalcashcredit').modal('show');
        });
        $('#btncash').click(function(){
            $('#modalcashcredit').modal('hide');
            resetPaymentModalFields();
            $('#billtype').val('1');
            $('#paymentmethod').val('1');
            var fulltotal = $('#hiddenfullnettotal').val();
            $('#amount').val(fulltotal);
            $('#collapsecustomerinfo').collapse('hide');
            $('#cusname').val('');
            $('#cusnic').val('');
            $('#cusmobile').val('');
            $('#modalpayment').modal('show');
            $('#modalpayment').on('shown.bs.modal', function () {
                $('#amount').focus().select();
            });
            $('#alreadyCustomerModal').modal('show');
        });
        $('#btncredit').click(function(){
            $('#modalcashcredit').modal('hide');
            resetPaymentModalFields();
            $('#billtype').val('2');
            $('#paymentmethod').val('1');
            $('#collapsecustomerinfo').collapse('show');
            $('#modalpayment').modal('show');
            $('#modalpayment').on('shown.bs.modal', function () {
                $('#amount').focus().select();
            });
            $('#alreadyCustomerModal').modal('show');
            $('#paymentcomplete').prop('disabled', false);
        });

        //Payment start
        $('#billtype').change(function() {
            if(this.value == '2'){
                resetPaymentModalFields();
                $('#alreadyCustomerModal').modal('show');
                $('#collapsecustomerinfo').collapse('show');
                $('#paymentcomplete').prop('disabled', false);
            }
            else if(this.value == '3'){
                resetPaymentModalFields();
                $('#alreadyCustomerModal').modal('show');
                $('#collapsecustomerinfo').collapse('show');
                $('#amount').prop('readonly', true);
                $('#paymentcomplete').prop('disabled', false);
            }
            else{
                $('#cusname').val('');
                $('#cusnic').val('');
                $('#cusmobile').val('');
                $('#collapsecustomerinfo').collapse('hide');
                $('#divcusoutstanding').hide();
                $('#amount').focus();
            }
        });
        $('#paymentmethod').change(function() {
            if (this.value == '1') {
                $('#bank').prop('readonly', true).prop('required', false).val('');
                $('#chequeno').prop('readonly', true).prop('required', false).val('');
                $('#chequedate').prop('readonly', true).prop('required', false).val('');
                $('#divcardlast4').hide();
                $('#cardlast4').prop('required', false).val('');
                $('#divonlineref').hide();                         
                $('#onlineref').prop('required', false).val('');   
                $('#amount').focus();
            }
            else if (this.value == '2') {
                $('#bank').prop('readonly', true).prop('required', false).val('');
                $('#chequeno').prop('readonly', true).prop('required', false).val('');
                $('#chequedate').prop('readonly', true).prop('required', false).val('');
                $('#divcardlast4').show();
                $('#cardlast4').prop('required', true).focus();
                $('#divonlineref').hide();                         
                $('#onlineref').prop('required', false).val('');   
            }
            else if (this.value == '3') {
                $('#bank').prop('readonly', false).prop('required', true);
                $('#chequeno').prop('readonly', false).prop('required', true);
                $('#chequedate').prop('readonly', false).prop('required', true);
                $('#divcardlast4').hide();
                $('#cardlast4').prop('required', false).val('');
                $('#divonlineref').hide();                         
                $('#onlineref').prop('required', false).val('');   
            }
            else if (this.value == '4') {                          
                $('#bank').prop('readonly', true).prop('required', false).val('');
                $('#chequeno').prop('readonly', true).prop('required', false).val('');
                $('#chequedate').prop('readonly', true).prop('required', false).val('');
                $('#divcardlast4').hide();
                $('#cardlast4').prop('required', false).val('');
                $('#divonlineref').show();
                $('#onlineref').prop('required', true).focus();
            }
        });

        // NEW: let Enter on the card field submit the payment line, same as amount does for cash
        $('#cardlast4').keypress(function (e) {
            if (e.which == 13) {
                $("#btnpayaddlist").click();
                return false;
            }
        });
        $('#onlineref').keypress(function (e) {
            if (e.which == 13) {
                $("#btnpayaddlist").click();
                return false;
            }
        });
        $('#amount').keypress(function (e) {
            var key = e.which;
            if(key == 13){
                var paymentmethod = $("#paymentmethod").val();
                if(paymentmethod == 1){          // CHANGED: was paymentmethod<3
                    $("#btnpayaddlist").click();
                    return false;
                }
            }
        });
        $('#btnpayaddlist').click(function(){
            if (!$("#paymentform")[0].checkValidity()) {
                $("#btnhidepayaddlist").click();
            } else {
                var amount=$('#amount').val();
                var bank=$('#bank').val();
                var chequeno=$('#chequeno').val();
                var chequedate=$('#chequedate').val();
                var cardlast4=$('#cardlast4').val();
                var onlineref=$('#onlineref').val();          
                var paymentmethod = $("#paymentmethod").val();
                var billtype = $('#billtype').val();

                if(paymentmethod==2){
                    if(!/^\d{4}$/.test(cardlast4)){
                        alert('Please enter a valid 4 digit card number.');
                        $('#cardlast4').focus();
                        return;
                    }
                }

                if(paymentmethod==4){                          
                    if(onlineref.trim() == ''){
                        alert('Please enter a reference number for the online transfer.');
                        $('#onlineref').focus();
                        return;
                    }
                }

                if(paymentmethod==1){var paymethod='Cash';}
                else if(paymentmethod==2){var paymethod='Card';}
                else if(paymentmethod==3){var paymethod='Cheque';}
                else if(paymentmethod==4){var paymethod='Online Transfer';}   

                var showcard = (paymentmethod==2) ? cardlast4 : (paymentmethod==4 ? onlineref : '');   // CHANGED

                $('#tablepayment > tbody:last').append('<tr class="pointer"><td class="d-none">' + paymentmethod + '</td><td>' + paymethod + '</td><td>' + bank + '</td><td>' + chequeno + '</td><td>' + chequedate + '</td><td>' + showcard + '</td><td class="d-none paytotal">' + amount + '</td><td class="text-right">' + addCommas(parseFloat(amount).toFixed(2)) + '</td></tr>');

                var sum = 0;
                $(".paytotal").each(function () {
                    sum += parseFloat($(this).text());
                });

                var netbilltotal = parseFloat($('#hiddenfullnettotal').val());
                var showsum = addCommas(parseFloat(sum).toFixed(2));

                var baltotal = netbilltotal - sum;
                var balText;
                if(billtype == 1 && baltotal < 0){
                    balText = 'Change Due: Rs. ' + addCommas(Math.abs(baltotal).toFixed(2));
                } else {
                    balText = 'Rs. ' + addCommas(baltotal.toFixed(2));
                }

                if(billtype==2){$('#paymentcomplete').prop('disabled', false);}
                else if(sum>=netbilltotal){$('#paymentcomplete').prop('disabled', false);}
                else{$('#paymentcomplete').prop('disabled', true);}

                $('#paynettotal').html('Rs. ' + showsum);
                $('#paybalance').html(balText);
                $('#hidepaymenttotal').val(sum);

                $('#amount').val('');
                $('#bank').val('');
                $('#chequeno').val('');
                $('#chequedate').val('');
                $('#cardlast4').val('');
                $('#divcardlast4').hide();
                $('#onlineref').val('');          
                $('#divonlineref').hide();        

                $("input[type=radio][name='paymentmethod']").prop('checked', false).parent().removeClass('active');
                $('#bank').prop('readonly', true).prop('required',false);
                $('#chequeno').prop('readonly', true).prop('required',false);
                $('#chequedate').prop('readonly', true).prop('required',false);
                $('#cardlast4').prop('required', false);
                $('#onlineref').prop('required', false);   
            }
        });
        $('#paymentcomplete').click(function(){
            createinvoice();
        });
        
        //Payment end
         
        $('#alreadyCustomerTable tbody').on('click', 'tr', function() { //alert('IN');
            if ($(this).hasClass('table-primary')) {
                $(this).removeClass('table-primary');
            } else {
                dataTable.$('tr.table-primary').removeClass('table-primary');
                $(this).addClass('table-primary');

                var data = $('#alreadyCustomerTable').DataTable().row('.table-primary').data();
                // console.log(data);
                $('#hidecustomerID').val(data.idtbl_customer);
                $('#cusname').val(data.name);
                $('#cusnic').val(data.nic);
                $('#cusmobile').val(data.phone);
                $('#alreadyCustomerModal').modal('hide');

                getcustomerbalance(data.idtbl_customer);
            }
        });
        $('#btnAddToDB').click(function(){
            $('#alreadyCustomerModal').modal('hide');
            $('#cusname').focus();
        });
        document.getElementById('btnreceiptprint').addEventListener ("click", print);
        document.getElementById('btnreceiptprintpos').addEventListener ("click", printpos);
        $('#modalinvoicereceiptpos').on('hidden.bs.modal', function (e) {
            location.reload();
        });
        $('#modalinvoicereceipt').on('hidden.bs.modal', function (e) {
            location.reload();
        });
        $('#alreadyCustomerModal').on('hidden.bs.modal', function (e) {
            if ($('#modalpayment').hasClass('show')) {
                $('#amount').focus();
            }
        });
    });

    // function subcategoryoption(){
    //     $('.subcategorydiv').click(function(){
    //         var subcategoryID=$(this).attr('id');
    //         $('#hiddensubID').val(subcategoryID);
    //         var categoryID=$('#hiddencategoryID').val();
    //         $("#collapseThree").collapse('show');
            
    //         $.ajax({
    //             method: "POST",
    //             data: {
    //                 categoryID: categoryID
    //             },
    //             url: "getprocess/getgroupcategoryaccosubcategory.php",
    //             success: function (result) { //alert(result)
    //                 $('#divgroupcategory').html(result);
    //                 groupcategoryoption();
    //             }
    //         });
    //     });
    // }

    function getgroupcategorylist(categoryID){
        $.ajax({
            method: "POST",
            data: {
                categoryID: categoryID
            },
            url: "getprocess/getgroupcategoryaccosubcategory.php",
            success: function (result) { //alert(result)
                $('#divgroupcategory').html(result);
                groupcategoryoption();
            }
        });
    }

    function groupcategoryoption(){
        $('.groupcategorydiv').click(function(){
            var groupcategoryID=$(this).attr('id');
            $('#hiddengroupID').val(groupcategoryID);
            $("#collapseFour").collapse('show');

            getgroupproductlist(groupcategoryID);
        });
    }

    function getgroupproductlist(groupcategoryID){
        var categoryID=$('#hiddencategoryID').val();
        var subcategoryID=$('#hiddensubID').val();
        var saletype=$('#saletype').val();
        
        $.ajax({
            method: "POST",
            data: {
                categoryID: categoryID,
                subcategoryID: subcategoryID,
                groupcategoryID: groupcategoryID,
                saletype: saletype
            },
            url: "getprocess/getproductlistaccpallcategory.php",
            success: function (result) { //alert(result)
                $('#divproductlist').html(result);
                productlistoption();

                setTimeout(function() {
                    var $firstRow = $("#tableproductpricelist tbody tr.classfocus:first");
                    if ($firstRow.length) {
                        $firstRow.attr('tabindex', '0').focus();
                    }
                }, 100);
            }
        });
    }

    function productlistoption(){
    $("#tableproductpricelist").delegate("tr.pointer", "click", function(){
            var productID = $(this).children("td:eq(0)").text();
            var productcode = $(this).children("td:eq(1)").text();
            var product = $(this).children("td:eq(2)").text();
            var stock = $(this).children("td:eq(3)").text();
            var unit = $(this).children("td:eq(4)").text();
            var sale = $(this).children("td:eq(5)").text();
            var maxdiscount = $(this).children("td:eq(6)").text();

            var unit = unit.replace(",", ""); 
            var sale = sale.replace(",", ""); 
            var stock = parseFloat(stock.replace(",", "")); // NEW

            $("#discountpresentage").attr('max', maxdiscount);

            $('#hideproductid').val(productID);
            $('#hideproduct').val(product);
            $('#hideproductcode').val(productcode);
            $('#hideproductunit').val(unit);
            $('#hideproductsale').val(sale);
            $('#hideproductstock').val(stock); // NEW

            // Reset qty and price field; price auto-fills once qty is typed
            $('#qtycount').val('');
            $('#enablepriceedit').prop('checked', false);
            $('#salepriceedit').prop('readonly', true).val('');

            $('#selectproduct').html(product);

            $('#modalqty').modal('show');
            $('#modalqty').on('shown.bs.modal', function () {
                $('#qtycount').focus();
            })  
        });
        $("#tableproductpricelist tbody tr.classfocus").on('keydown', function(e) {
            var $current = $(this);
            var $rows = $("#tableproductpricelist tbody tr.classfocus");
            var currentIndex = $rows.index($current);
            console.log(currentIndex);
            console.log($rows.length);
            
            // Only handle arrow keys and Enter
            if ([38, 40, 13].indexOf(e.which) === -1) return;

            e.preventDefault();
            
            switch(e.which) {
                case 38: // Up arrow
                    if (currentIndex > 0) {
                        $rows.eq(currentIndex - 1).focus();
                    }
                    break;
                case 40: // Down arrow
                    if (currentIndex < $rows.length - 1) {
                        $rows.eq(currentIndex + 1).focus();
                    }
                    break;
                case 13: // Enter
                    $current.trigger('click');
                    break;
            }
        });
    }

    // Fetch a customer's total outstanding balance across ALL their invoices (not just this bill)
    function getcustomerbalance(customerID){
        $.ajax({
            method: "POST",
            data: {
                customerID: customerID
            },
            url: "getprocess/getcustomeroutstandingbalance.php",
            success: function (result) {
                var obj = JSON.parse(result);
                if(obj.outstanding && parseFloat(obj.outstanding) > 0){
                    $('#cusoutstanding').val('Rs. ' + addCommas(parseFloat(obj.outstanding).toFixed(2)));
                    $('#divcusoutstanding').show();
                }
                else{
                    $('#cusoutstanding').val('Rs. 0.00');
                    $('#divcusoutstanding').show();
                }
            }
        });
    }

    function createinvoice(){
        var tbody = $('#carttable tbody');
        if (tbody.children().length > 0) {
            jsonObj = []
            $("#carttable tbody tr").each(function () {
                item = {}
                $(this).find('td').each(function (col_idx) {
                    item["col_" + (col_idx + 1)] = $(this).text();
                });
                jsonObj.push(item);
            });
        }        
        // console.log(jsonObj);

        var tbodysecond = $('#tablepayment tbody');
        jsonObjPay = []
        if (tbodysecond.children().length > 0) {
            $("#tablepayment tbody tr").each(function () {
                item = {}
                $(this).find('td').each(function (col_idx) {
                    item["col_" + (col_idx + 1)] = $(this).text();
                });
                jsonObjPay.push(item);
            });
        }        
        var total = $('#hiddenfulltotal').val();
        var distotal = $('#hiddenfulldistotal').val();
        var nettotal = $('#hiddenfullnettotal').val();
        var paytotal = $('#hidepaymenttotal').val();
        var billtype = $("#billtype").val();
        var cusname = $('#cusname').val();
        var cusnic = $('#cusnic').val();
        var cusmobile = $('#cusmobile').val();
        var cusID = $('#hidecustomerID').val();
        var saletype = $('#saletype').val();
        var priceeditstatus = $('#priceeditstatus').val();
        var billapproveuser = $('#hideapproveuser').val();
        // console.log(jsonObjPay);

        if(tbodysecond.children().length > 0 && billtype==1){var paystatus='1';}
        else if(tbodysecond.children().length > 0 && billtype==2){var paystatus='1';}
        else if(tbodysecond.children().length == 0 && billtype==2){var paystatus='1';}
        else if(tbodysecond.children().length == 0 && billtype==3){var paystatus='1';}
        else{var paystatus='0';}
        
        if(paystatus==1 && cusname!='' && cusmobile!=''){
            $.ajax({
                type: "POST",
                data: {
                    tableData: jsonObj,
                    tableDataPay: jsonObjPay,
                    total: total,
                    distotal: distotal,
                    nettotal: nettotal,
                    paytotal: paytotal,
                    billtype: billtype,
                    cusname: cusname,
                    cusnic: cusnic,
                    cusmobile: cusmobile,
                    cusID: cusID,
                    saletype: saletype,
                    priceeditstatus: priceeditstatus,
                    billapproveuser: billapproveuser
                },
                url: 'process/newdirectsaleprocess.php',
                success: function (result) {
                    // console.log(result);
                    var objfirst = JSON.parse(result);
                    if(objfirst.actiontype==1){
                        $('#modalpayment').modal('hide');
                        action(objfirst.action);
                        if(objfirst.saletype==1){
                            if(objfirst.billtype==1){
                                posprintbill(objfirst.invoiceid);
                            }
                            else{
                                creditprintbill(objfirst.invoiceid);
                            }
                        }
                        else{
                            creditprintbill(objfirst.invoiceid);
                        }                          
                    }
                    else{
                        action(objfirst.action);
                    }
                }
            });
        }
        else{
            $("#btnpayaddlist").click();
        }
    }

    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function action(data) { //alert(data);
        var obj = JSON.parse(data);
        $.notify({
            // options
            icon: obj.icon,
            title: obj.title,
            message: obj.message,
            url: obj.url,
            target: obj.target
        }, {
            // settings
            element: 'body',
            position: null,
            type: obj.type,
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "center"
            },
            offset: 100,
            spacing: 10,
            z_index: 1031,
            delay: 5000,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutUp'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
            template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<span data-notify="icon"></span> ' +
                '<span data-notify="title">{1}</span> ' +
                '<span data-notify="message">{2}</span>' +
                '<div class="progress" data-notify="progressbar">' +
                '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                '</div>' +
                '<a href="{3}" target="{4}" data-notify="url"></a>' +
                '</div>'
        });
    }

    function posprintbill(invoiceid){
        window.open("getprocess/invoiceprintpos.php?recordID="+invoiceid, "_blank");
        setTimeout(window.location.reload(), 3000);

        // $('#modalinvoicereceiptpos').modal('show');
        // $('#viewreceiptprint').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

        // $.ajax({
        //     type: "POST",
        //     data: {
        //         recordID: invoiceid
        //     },
        //     url: 'getprocess/invoiceprintpos.php',
        //     success: function(result) { //alert(result);
        //         $('#viewreceiptprintpos').html(result);
        //     }
        // });
    }
    function creditprintbill(invoiceid){
        window.open("getprocess/invoiceprintcredit.php?recordID="+invoiceid, "_blank");
        setTimeout(window.location.reload(), 3000);
        // $('#modalinvoicereceipt').modal('show');
        // $('#viewreceiptprint').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

        // $.ajax({
        //     type: "POST",
        //     data: {
        //         recordID: invoiceid
        //     },
        //     url: 'getprocess/invoiceprintcredit.php',
        //     success: function(result) { //alert(result);
        //         $('#viewreceiptprint').html(result);
        //     }
        // });
    }
    function print() {
        printJS({
            printable: 'viewreceiptprint',
            type: 'html',
            style: '@page { size: A5 portrait; margin:0.25cm; }',
            targetStyles: ['*']
        })
    }
    function printpos() {
        printJS({
            printable: 'viewreceiptprintpos',
            type: 'html',
            // style: '@page { size: A5 portrait; margin:0.25cm; }',
            targetStyles: ['*']
        })
    }
</script>

<?php include "include/footer.php"; ?>