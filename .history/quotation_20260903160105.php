<?php
include "include/header.php";

$userID     = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
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
                            <div class="page-header-icon"><i class="fas fa-file-alt"></i></div>
                            <span>Quotation</span>
                        </h1>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-2 p-0 p-2">

                <div class="card mb-3">
                    <div class="card-header py-2"><strong>New Quotation</strong></div>
                    <div class="card-body p-3">
                        <form id="quotationForm" autocomplete="off">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-dark">Customer <span class="text-danger">*</span></label>
                                    <select name="customerid" id="customerid" class="form-control form-control-sm select2-customer" style="width:100%" required>
                                        <option value="">Search customer...</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold text-dark">Quotation Date</label>
                                    <input type="date" name="quotationdate" id="quotationdate" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold text-dark">VAT Type</label>
                                    <select name="vattype" id="vattype" class="form-control form-control-sm">
                                        <option value="2">Exclusive</option>
                                        <option value="1" selected>Inclusive</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="small font-weight-bold text-dark">VAT %</label>
                                    <input type="number" step="0.01" min="0" name="vatpercent" id="vatpercent" class="form-control form-control-sm" value="<?php echo $vatPercent; ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold text-dark">Discount</label>
                                    <input type="number" step="0.01" min="0" name="discounttotal" id="discounttotal" class="form-control form-control-sm" value="0">
                                </div>
                                <div class="col-md-1">
                                    <label class="small font-weight-bold text-dark">Valid (days)</label>
                                    <input type="number" min="0" name="validitydays" id="validitydays" class="form-control form-control-sm" value="7">
                                </div>
                            </div>

                            <hr>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle" id="itemsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:12%">Product</th>
                                            <th style="width:28%">Description</th>
                                            <th style="width:12%">Qty</th>
                                            <th style="width:14%">Unit Price</th>
                                            <th style="width:14%">Amount</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="addRowBtn" class="btn btn-sm btn-outline-secondary mb-3">
                                <i class="fas fa-plus mr-1"></i>Add Line
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

                            <div class="mb-3">
                                <label class="small font-weight-bold text-dark">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control form-control-sm" rows="2"></textarea>
                            </div>

                            <div class="text-right mt-2">
                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                    <i class="far fa-save mr-2"></i>Save Quotation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ============ Quotation List ============ -->
                <div class="card">
                    <div class="card-header py-2"><strong>Quotations</strong></div>
                    <div class="card-body p-3">
                        <table id="quotationsTable" class="table table-bordered table-hover table-sm w-100">
                            <thead class="thead-light">
                                <tr>
                                    <th>Quotation No</th>
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

    // 1 = inclusive (prices already include VAT), 2 = exclusive (VAT added on top)
    function currentVatType() {
        return $('#vattype').val() || '2';
    }

    function newProductSelect($el) {
        $el.select2({
            width: '100%',
            placeholder: 'Product (optional)',
            minimumInputLength: 0,
            allowClear: true,
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
                '<td><input type="text" class="form-control form-control-sm description-input" placeholder="Description"></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm qty-input" value="1"></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm unitprice-input" value="0"></td>' +
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

        if (vatType === '1') {
            // Inclusive - prices already include VAT, don't add again
            var totalWithVat = total - discount;
            netTotal = vatPercent > 0 ? (totalWithVat / (1 + (vatPercent / 100))) : totalWithVat;
            vatAmount = totalWithVat - netTotal;
            grandTotal = totalWithVat;
        } else {
            // Exclusive - add VAT on top
            netTotal = total - discount;
            vatAmount = netTotal * vatPercent / 100;
            grandTotal = netTotal + vatAmount;
        }

        $('#lblTotal').text(currency(total));
        $('#lblNetTotal').text(currency(netTotal));
        $('#lblVatAmount').text(currency(vatAmount));
        $('#lblGrandTotal').text(currency(grandTotal));
    }

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

    // Auto-fill description + unit price when a product is picked (still editable after)
    $('#itemsTableBody').on('select2:select', '.product-select', function (e) {
        var data = e.params.data;
        var $row = $(this).closest('tr');
        $row.find('.description-input').val(data.text.replace(/^\[.*?\]\s*/, ''));
        var price = parseFloat(data.saleprice) || parseFloat(data.unitprice) || 0;
        $row.find('.unitprice-input').val(currency(price));
        recalcRow($row);
    });

    $('#itemsTableBody').on('input change', '.qty-input, .unitprice-input', function () {
        recalcRow($(this).closest('tr'));
    });

    $('#itemsTableBody').on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        recalcTotals();
    });

    $('#discounttotal, #vatpercent').on('input change', recalcTotals);
    $('#vattype').on('change', recalcTotals);

    $('#addRowBtn').on('click', addRow);
    addRow();

    $('#quotationForm').on('submit', function (e) {
        e.preventDefault();

        var customerId = $('#customerid').val();
        if (!customerId) {
            alert('Please select a customer.');
            return;
        }

        var productIds = [], descriptions = [], qtys = [], unitPrices = [];

        $('#itemsTableBody tr').each(function () {
            var $row = $(this);
            var pid = $row.find('.product-select').val();
            var desc = $row.find('.description-input').val().trim();
            var qty = parseFloat($row.find('.qty-input').val()) || 0;
            var unitPrice = parseFloat($row.find('.unitprice-input').val()) || 0;

            if (desc !== '' && qty > 0) {
                productIds.push(pid || 0);
                descriptions.push(desc);
                qtys.push(qty);
                unitPrices.push(unitPrice);
            }
        });

        if (descriptions.length === 0) {
            alert('Please add at least one valid line.');
            return;
        }

        var payload = {
            customerid: customerId,
            quotationdate: $('#quotationdate').val(),
            discounttotal: $('#discounttotal').val() || 0,
            vatpercent: $('#vatpercent').val() || 0,
            vattype: currentVatType(),
            validitydays: $('#validitydays').val() || 0,
            remarks: $('#remarks').val() || '',
            productid: productIds,
            description: descriptions,
            qty: qtys,
            unitprice: unitPrices
        };

        var $btn = $(this).find('button[type=submit]').prop('disabled', true);

        $.post('process/quotationprocess.php', payload, function (res) {
            $btn.prop('disabled', false);
            if (res.status === 'success') {
                alert('Quotation ' + res.quotationno + ' saved successfully.');
                window.open('printquotation.php?id=' + res.quotationid, '_blank');
                resetForm();
                quotationsTable.ajax.reload(null, false);
            } else {
                alert(res.message || 'Failed to save quotation.');
            }
        }, 'json').fail(function () {
            $btn.prop('disabled', false);
            alert('Something went wrong while saving the quotation.');
        });
    });

    function resetForm() {
        $('#customerid').val(null).trigger('change');
        $('#quotationdate').val('<?php echo date('Y-m-d'); ?>');
        $('#discounttotal').val(0);
        $('#vattype').val('2');
        $('#remarks').val('');
        $('#itemsTableBody').empty();
        rowIndex = 0;
        addRow();
        recalcTotals();
        $('#quotationForm button[type=submit]').prop('disabled', false);
    }

    var quotationsTable = $('#quotationsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'scripts/quotationdatatable.php',
            type: 'POST'
        },
        columns: [
            { data: 'quotation_no' },
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
                data: 'idtbl_quotation',
                orderable: false,
                searchable: false,
                render: function (data) {
                    return '<div class="text-right">' +
                        '<a href="printquotation.php?id=' + data + '" target="_blank" class="btn btn-sm btn-outline-primary">' +
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