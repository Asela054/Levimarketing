<?php 
include "include/header.php";

$userID = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
$locationID = isset($_SESSION['location_id']) ? $_SESSION['location_id'] : 0;

$vatSql = "SELECT `vat` FROM `tbl_vat_info` WHERE `status`=1 ORDER BY `date_from` DESC, `idtbl_vat_info` DESC LIMIT 1";
$vatResult = $conn->query($vatSql);
$vatPercent = 0;
if ($vatResult && $vatResult->num_rows > 0) {
    $vatRow = $vatResult->fetch_assoc();
    $vatPercent = floatval($vatRow['vat']);
}

include "include/topnavbar.php"; 
?>
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <?php include "include/menubar.php"; ?>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="page-header page-header-light bg-white shadow">
                <div class="container-fluid">
                    <div class="page-header-content py-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            <span>VAT Invoice</span>
                        </h1>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-2 p-0 p-2">

                <div class="card mb-3">
                    <div class="card-header py-2"><strong>New Tax Invoice</strong></div>
                    <div class="card-body p-3">
                        <form id="invoiceForm" autocomplete="off">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="small font-weight-bold text-dark">Customer <span class="text-danger">*</span></label>
                                    <select name="customerid" id="customerid" class="form-control form-control-sm select2-customer" style="width:100%" required>
                                        <option value="">Search customer...</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold text-dark">Sale Type</label>
                                    <select name="saletype" id="saletype" class="form-control form-control-sm">
                                        <option value="1" selected>Retail</option>
                                        <option value="2">Wholesale</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold text-dark">Invoice Date</label>
                                    <input type="date" name="invoicedate" id="invoicedate" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold text-dark">VAT Type</label>
                                    <select name="vattype" id="vattype" class="form-control form-control-sm">
                                        <option value="inclusive" selected>Inclusive</option>
                                        <option value="exclusive">Exclusive</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="small font-weight-bold text-dark">VAT %</label>
                                    <input type="number" step="0.01" min="0" name="vatpercent" id="vatpercent" class="form-control form-control-sm" value="<?php echo $vatPercent; ?>" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold text-dark">Discount</label>
                                    <input type="number" step="0.01" min="0" name="discounttotal" id="discounttotal" class="form-control form-control-sm" value="0">
                                </div>
                            </div>

                            <hr>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle" id="itemsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:35%">Product</th>
                                            <th style="width:12%">Unit Price</th>
                                            <th style="width:12%">Qty</th>
                                            <th style="width:14%">Amount</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="addRowBtn" class="btn btn-sm btn-outline-secondary mb-3">
                                <i class="fas fa-plus mr-1"></i>Add Product
                            </button>

                            <div class="row justify-content-end">
                                <div class="col-md-4">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="text-right font-weight-bold">Gross Total</td>
                                            <td class="text-right" style="width:120px"><span id="lblTotal">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right font-weight-bold">Net Total</td>
                                            <td class="text-right"><span id="lblNetTotal">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right font-weight-bold">VAT Amount</td>
                                            <td class="text-right"><span id="lblVatAmount">0.00</span></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="text-right font-weight-bold">Total With VAT</td>
                                            <td class="text-right"><strong><span id="lblGrandTotal">0.00</span></strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="text-right mt-2">
                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                    <i class="far fa-save mr-2"></i>Save Invoice
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ============ Invoice List ============ -->
                <div class="card">
                    <div class="card-header py-2"><strong>Invoices</strong></div>
                    <div class="card-body p-3">
                        <table id="invoicesTable" class="table table-bordered table-hover table-sm w-100">
                            <thead class="thead-light">
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Net Total</th>
                                    <th>VAT Amount</th>
                                    <th>Total</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>

<script>
$(document).ready(function () {
    var rowIndex = 0;

    function currency(n) {
        return (parseFloat(n) || 0).toFixed(2);
    }

    function currentSaleType() {
        // 1 = Retail, 2 = Wholesale
        return $('#saletype').val() || '1';
    }

    function currentVatType() {
        // 'exclusive' = prices entered do NOT include VAT -> VAT gets added to totals
        // 'inclusive' = prices entered already include VAT -> VAT is NOT added again
        return $('#vattype').val() || 'inclusive';
    }

    function priceForType(productData, saleType) {
        var retailPrice = parseFloat(productData.saleprice) || 0;
        var wholesalePrice = parseFloat(productData.wholesaleprice) || 0;
        if (saleType === '2') {
            return wholesalePrice > 0 ? wholesalePrice : retailPrice;
        }
        return retailPrice > 0 ? retailPrice : (parseFloat(productData.unitprice) || 0);
    }

    // ---- Stock helpers ----
    function checkRowStock($row) {
        var available = $row.data('availableqty');
        if (available === undefined) return; // no product picked yet

        var qty = parseFloat($row.find('.qty-input').val()) || 0;
        var $warning = $row.find('.stock-warning');

        if (available <= 0) {
            $warning.text('Out of stock').show();
        } else if (qty > available) {
            $warning.text('Only ' + available + ' in stock').show();
        } else {
            $warning.hide();
        }
        updateSaveButtonState();
    }

    function updateSaveButtonState() {
        var hasStockIssue = false;
        $('#itemsTableBody tr').each(function () {
            var $row = $(this);
            var pid = $row.find('.product-select').val();
            var qty = parseFloat($row.find('.qty-input').val()) || 0;
            var available = $row.data('availableqty');

            if (pid && qty > 0 && available !== undefined && qty > available) {
                hasStockIssue = true;
            }
        });
        $('#invoiceForm button[type=submit]').prop('disabled', hasStockIssue);
    }

    function newProductSelect($el) {
        $el.select2({
            width: '100%',
            placeholder: 'Search product...',
            minimumInputLength: 0,
            ajax: {
                url: 'getprocess/getproductselect2.php',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { searchTerm: params.term || '' };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });
    }

    function addRow() {
        rowIndex++;
        var rowHtml =
            '<tr data-row="' + rowIndex + '">' +
                '<td><select class="form-control form-control-sm product-select" style="width:100%"></select></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm unitprice-input" value="0"></td>' +
                '<td>' +
                    '<input type="number" step="0.01" min="0" class="form-control form-control-sm qty-input" value="1">' +
                    '<div class="small text-danger stock-warning" style="display:none;"></div>' +
                '</td>' +
                '<td class="text-right line-amount">0.00</td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-trash"></i></button></td>' +
            '</tr>';
        var $row = $(rowHtml);
        $('#itemsTableBody').append($row);
        newProductSelect($row.find('.product-select'));
    }

    function recalcRow($row) {
        var qty = parseFloat($row.find('.qty-input').val()) || 0;
        var unitPrice = parseFloat($row.find('.unitprice-input').val()) || 0;
        var amount = qty * unitPrice;
        $row.find('.line-amount').text(currency(amount));
        recalcTotals();
    }

    function recalcTotals() {
        var total = 0;
        $('#itemsTableBody tr').each(function () {
            var qty = parseFloat($(this).find('.qty-input').val()) || 0;
            var unitPrice = parseFloat($(this).find('.unitprice-input').val()) || 0;
            total += qty * unitPrice;
        });

        var discount = parseFloat($('#discounttotal').val()) || 0;
        if (discount > total) { discount = total; }

        var vatPercent = parseFloat($('#vatpercent').val()) || 0;
        var vatType = currentVatType();

        var netTotal, vatAmount, grandTotal;

        if (vatType === 'inclusive') {
            // Entered line prices already include VAT.
            // Do NOT add VAT again to the totals -- just extract it for display.
            var totalWithVat = total - discount;
            netTotal = vatPercent > 0 ? (totalWithVat / (1 + (vatPercent / 100))) : totalWithVat;
            vatAmount = totalWithVat - netTotal;
            grandTotal = totalWithVat; // VAT not added on top
        } else {
            // Entered line prices do NOT include VAT -- add VAT to the totals.
            netTotal = total - discount;
            vatAmount = netTotal * vatPercent / 100;
            grandTotal = netTotal + vatAmount; // VAT added on top
        }

        $('#lblTotal').text(currency(total));
        $('#lblNetTotal').text(currency(netTotal));
        $('#lblVatAmount').text(currency(vatAmount));
        $('#lblGrandTotal').text(currency(grandTotal));
    }

    // Customer select2 (searches, doesn't load the whole table)
    $('#customerid').select2({
        width: '100%',
        placeholder: 'Search customer...',
        minimumInputLength: 0,
        ajax: {
            url: 'getprocess/getcustomerselect2.php',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { searchTerm: params.term || '' };
            },
            processResults: function (data) {
                return { results: data };
            },
            cache: true
        }
    });

    // Auto-fill unit price + check stock when a product is picked on a row
    $('#itemsTableBody').on('select2:select', '.product-select', function (e) {
        var data = e.params.data;
        var $row = $(this).closest('tr');

        $row.data('saleprice', parseFloat(data.saleprice) || 0);
        $row.data('wholesaleprice', parseFloat(data.wholesaleprice) || 0);
        $row.data('unitprice', parseFloat(data.unitprice) || 0);
        $row.data('availableqty', parseFloat(data.availableqty) || 0);

        $row.find('.unitprice-input').val(currency(priceForType(data, currentSaleType())));
        recalcRow($row);
        checkRowStock($row);
    });

    $('#itemsTableBody').on('input change', '.qty-input, .unitprice-input', function () {
        var $row = $(this).closest('tr');
        recalcRow($row);
        checkRowStock($row);
    });

    $('#itemsTableBody').on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        recalcTotals();
        updateSaveButtonState();
    });

    $('#discounttotal').on('input change', recalcTotals);
    $('#vattype').on('change', recalcTotals);

    $('#saletype').on('change', function () {
        var saleType = currentSaleType();
        $('#itemsTableBody tr').each(function () {
            var $row = $(this);
            if (!$row.find('.product-select').val()) {
                return;
            }
            var productData = {
                saleprice: $row.data('saleprice'),
                wholesaleprice: $row.data('wholesaleprice'),
                unitprice: $row.data('unitprice')
            };
            $row.find('.unitprice-input').val(currency(priceForType(productData, saleType)));
            recalcRow($row);
        });
    });

    $('#addRowBtn').on('click', addRow);
    addRow(); // start with one empty row

    // ---- Submit invoice ----
    $('#invoiceForm').on('submit', function (e) {
        e.preventDefault();

        var customerId = $('#customerid').val();
        if (!customerId) {
            alert('Please select a customer.');
            return;
        }

        var productIds = [];
        var qtys = [];
        var unitPrices = [];

        $('#itemsTableBody tr').each(function () {
            var $row = $(this);
            var pid = $row.find('.product-select').val();
            var qty = parseFloat($row.find('.qty-input').val()) || 0;
            var unitPrice = parseFloat($row.find('.unitprice-input').val()) || 0;

            if (pid && qty > 0) {
                productIds.push(pid);
                qtys.push(qty);
                unitPrices.push(unitPrice);
            }
        });

        if (productIds.length === 0) {
            alert('Please add at least one valid product line.');
            return;
        }

        updateSaveButtonState();
        if ($('#invoiceForm button[type=submit]').prop('disabled')) {
            alert('One or more items exceed available stock. Please adjust quantities before saving.');
            return;
        }

        var payload = {
            customerid: customerId,
            invoicedate: $('#invoicedate').val(),
            discounttotal: $('#discounttotal').val() || 0,
            vatpercent: $('#vatpercent').val() || 0,
            vattype: currentVatType(),
            saletype: currentSaleType(),
            productid: productIds,
            qty: qtys,
            unitprice: unitPrices
        };

        var $btn = $(this).find('button[type=submit]').prop('disabled', true);

        $.post('process/vatinvoiceprocess.php', payload, function (res) {
            $btn.prop('disabled', false);
            if (res.status === 'success') {
                alert('Invoice ' + res.taxinvoiceno + ' saved successfully.');
                window.open('printinvoice.php?id=' + res.invoiceid, '_blank');
                resetForm();
                invoicesTable.ajax.reload(null, false);
            } else {
                alert(res.message || 'Failed to save invoice.');
            }
        }, 'json').fail(function () {
            $btn.prop('disabled', false);
            alert('Something went wrong while saving the invoice.');
        });
    });

    function resetForm() {
        $('#customerid').val(null).trigger('change');
        $('#invoicedate').val('<?php echo date('Y-m-d'); ?>');
        $('#discounttotal').val(0);
        $('#saletype').val('1');
        $('#vattype').val('inclusive');
        $('#itemsTableBody').empty();
        rowIndex = 0;
        addRow();
        recalcTotals();
        $('#invoiceForm button[type=submit]').prop('disabled', false);
    }

    // ---- Invoices list DataTable ----
    var invoicesTable = $('#invoicesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'scripts/invoicedatatable.php',
            type: 'POST'
        },
        columns: [
            { data: 'taxinvoice_no' },
            {
                data: 'date',
                render: function (data) {
                    return data ? data.substring(0, 10) : '';
                }
            },
            { data: 'name' },
            {
                data: 'nettotal',
                className: 'text-right',
                render: function (data) { return (parseFloat(data) || 0).toFixed(2); }
            },
            {
                data: 'vatamount',
                className: 'text-right',
                render: function (data) { return (parseFloat(data) || 0).toFixed(2); }
            },
            {
                data: 'nettotal_with_vat',
                className: 'text-right',
                render: function (data) { return (parseFloat(data) || 0).toFixed(2); }
            },
            {
                data: 'idtbl_invoice',
                orderable: false,
                searchable: false,
                render: function (data) {
                    return '<div class="text-right">' +
                        '<a href="printinvoice.php?id=' + data + '" target="_blank" class="btn btn-sm btn-outline-primary">' +
                        '<i class="fas fa-print"></i></a>' +
                        '</div>';
                }
            }
        ],
        order: [[0, 'desc']]
    });
});
</script>

<?php include "include/footer.php"; ?>