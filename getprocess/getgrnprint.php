<?php
require_once('../connection/db.php');

$grnid=intval($_POST['grnid']);

$sqlheader="SELECT g.`idtbl_grn`, g.`date`, g.`invoicenum`, g.`dispatchnum`, g.`total`, g.`porder_id`, g.`confirm_status`, g.`updatedatetime`,
        l.`location`, l.`code`,
        s.`suppliername`, s.`address` as supplier_address, s.`contactone` as supplier_phone
    FROM `tbl_grn` AS g
    LEFT JOIN `tbl_porder` AS po ON po.`idtbl_porder` = g.`porder_id` AND g.`porder_id` > 0
    LEFT JOIN `tbl_location` AS l ON l.`idtbl_location` = g.`tbl_location_idtbl_location`
    LEFT JOIN `tbl_supplier` AS s ON s.`idtbl_supplier` = COALESCE(g.`tbl_supplier_idtbl_supplier`, po.`tbl_supplier_idtbl_supplier`)
    WHERE g.`idtbl_grn` = '$grnid'";
$resultheader = $conn->query($sqlheader);

if(!$resultheader){
    die("Query Error: " . $conn->error);
}

$rowheader = $resultheader->fetch_assoc();

if(!$rowheader){
    die("GRN Record Not Found for ID: " . $grnid);
}

$sqldetail="SELECT `tbl_grndetail`.`qty`, `tbl_grndetail`.`unitprice`, `tbl_grndetail`.`total`, `tbl_grndetail`.`date` as detail_date, `tbl_product`.`product_name`, `tbl_product`.`idtbl_product`, `tbl_product`.`product_code`
    FROM `tbl_grndetail`
    LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_grndetail`.`tbl_product_idtbl_product`
    WHERE `tbl_grndetail`.`tbl_grn_idtbl_grn`='$grnid' AND `tbl_grndetail`.`status`=1
    ORDER BY `tbl_grndetail`.`idtbl_grndetail` ASC";
$resultdetail=$conn->query($sqldetail);

if(!$resultdetail){
    die("Query Error: " . $conn->error);
}

// Calculate totals
$subtotal = 0;
$detailRows = [];
while($row=$resultdetail->fetch_assoc()){
    $subtotal += floatval($row['total']);
    $detailRows[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>GRN Print - GRN-<?php echo $rowheader['idtbl_grn']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            font-family: 'Arial', sans-serif;
            background: #fff;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
        }
        .page {
            width: 210mm;
            margin: 0 auto;
            padding: 15mm;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }
        
        /* Header Section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-info {
            flex: 1;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }
        .company-subtitle {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 9px;
            color: #555;
            line-height: 1.3;
        }
        .document-title {
            flex: 1;
            text-align: center;
        }
        .document-name {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        .document-number {
            font-size: 14px;
            font-weight: bold;
            color: #d32f2f;
            background: #fff3cd;
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
        }
        .grn-badge {
            flex: 1;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 10px;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .info-box {
            border: 1px solid #ddd;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 3px;
        }
        .info-box-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        .info-row {
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
            text-align: right;
            flex: 1;
            margin-left: 10px;
        }
        
        /* Supplier Section */
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .supplier-box, .location-box {
            border: 1px solid #ddd;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 3px;
        }
        .box-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .box-content {
            font-size: 10px;
            line-height: 1.6;
        }
        
        /* Table Styles */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .items-table thead {
            background: linear-gradient(135deg, #000 0%, #0d3a66 100%);
            color: white;
        }
        .items-table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #000;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        .items-table tbody tr:hover {
            background-color: #eff5fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .item-name {
            font-weight: bold;
            color: #000;
        }
        
        /* Totals Section */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .totals-table {
            width: 35%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .totals-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        .totals-table .label {
            font-weight: bold;
            background-color: #e8e8e8;
            width: 60%;
        }
        .totals-table .value {
            text-align: right;
            background-color: #f5f5f5;
            width: 40%;
        }
        .totals-table .grand-total-label {
            background-color: #000;
            color: white;
            font-weight: bold;
        }
        .totals-table .grand-total-value {
            background-color: #000;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        
        /* Notes Section */
        .notes-section {
            border: 1px solid #ddd;
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            font-size: 10px;
        }
        .notes-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .notes-content {
            min-height: 30px;
            color: #555;
        }
        
        /* Signature Section */
        .signature-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 30px;
            font-size: 10px;
        }
        .signature-box {
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 5px;
            min-height: 50px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .signature-label {
            font-size: 9px;
            font-weight: bold;
            color: #555;
        }
        
        /* Footer */
        .footer-section {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        
        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .page {
                margin: 0;
                padding: 15mm;
                box-shadow: none;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header Section -->
        <div class="header-section">
            <div class="company-info">
                <div class="company-name">LEVI MARKETING PVT LTD</div>
                <div class="company-details">
                    Address: No.61/1/C, Jayaweera Mawatha, Gonawala, Kelaniya<br>
                    Tel: 0757000300 / 0701802080 / 0112984823<br>
                    Email: info.levimarketing@gmail.com
                </div>
            </div>
            <div class="document-title">
                <div class="document-name">GOODS RECEIVED NOTE</div>
                <div class="document-number">GRN-<?php echo str_pad($rowheader['idtbl_grn'], 6, '0', STR_PAD_LEFT); ?></div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-box">
                <div class="info-box-title">GRN Details</div>
                <div class="info-row">
                    <span class="info-label">GRN No:</span>
                    <span class="info-value"><strong>GRN-<?php echo str_pad($rowheader['idtbl_grn'], 6, '0', STR_PAD_LEFT); ?></strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">GRN Date:</span>
                    <span class="info-value"><?php echo date('d-M-Y', strtotime($rowheader['date'])); ?></span>
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-title">Document References</div>
                <div class="info-row">
                    <span class="info-label">Invoice No:</span>
                    <span class="info-value"><?php echo htmlspecialchars($rowheader['invoicenum']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dispatch No:</span>
                    <span class="info-value"><?php echo htmlspecialchars($rowheader['dispatchnum']); ?></span>
                </div>
                <?php if($rowheader['porder_id'] > 0): ?>
                <div class="info-row">
                    <span class="info-label">Purchase Order:</span>
                    <span class="info-value"><strong>PO-<?php echo str_pad($rowheader['porder_id'], 6, '0', STR_PAD_LEFT); ?></strong></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="info-box">
                <div class="info-box-title">Location Details</div>
                <div class="info-row">
                    <span class="info-label">Location:</span>
                    <span class="info-value"><strong><?php echo htmlspecialchars($rowheader['location']); ?></strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Location Code:</span>
                    <span class="info-value"><?php echo htmlspecialchars($rowheader['code']); ?></span>
                </div>
            </div>
        </div>

        <!-- Supplier and Location Section -->
        <div class="two-column">
            <div class="supplier-box">
                <div class="box-title">Supplier Information</div>
                <div class="box-content">
                    <strong><?php echo htmlspecialchars($rowheader['suppliername']); ?></strong><br>
                    <?php if($rowheader['supplier_address']): ?>
                        Address: <?php echo htmlspecialchars($rowheader['supplier_address']); ?><br>
                    <?php endif; ?>
                    <?php if($rowheader['supplier_phone']): ?>
                        Phone: <?php echo htmlspecialchars($rowheader['supplier_phone']); ?><br>
                    <?php endif; ?>
                </div>
            </div>
            <div class="location-box">
                <div class="box-title">Receiving Location</div>
                <div class="box-content">
                    <strong><?php echo htmlspecialchars($rowheader['location']); ?></strong><br>
                    Code: <?php echo htmlspecialchars($rowheader['code']); ?><br>
                    Date: <?php echo date('d-M-Y H:i', strtotime($rowheader['updatedatetime'])); ?>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 10%;">Product Code</th>
                    <th style="width: 45%;">Product Name</th>
                    <th style="width: 12%;" class="text-right">Unit Price</th>
                    <th style="width: 12%;" class="text-center">Qty</th>
                    <th style="width: 17%;" class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                $grandtotal = 0;
                foreach($detailRows as $row){ 
                    $grandtotal += floatval($row['total']);
                ?>
                <tr>
                    <td class="text-center"><?php echo $i; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['product_code']); ?></strong></td>
                    <td class="item-name"><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td class="text-right">Rs. <?php echo number_format($row['unitprice'], 2); ?></td>
                    <td class="text-center"><?php echo number_format($row['qty'], 2); ?></td>
                    <td class="text-right">Rs. <?php echo number_format($row['total'], 2); ?></td>
                </tr>
                <?php $i++; } ?>
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">Rs. <?php echo number_format($grandtotal, 2); ?></td>
                </tr>
                <tr>
                    <td class="label">Tax (if applicable):</td>
                    <td class="value">Rs. 0.00</td>
                </tr>
                <tr>
                    <td class="grand-total-label">GRAND TOTAL:</td>
                    <td class="grand-total-value">Rs. <?php echo number_format($grandtotal, 2); ?></td>
                </tr>
            </table>
        </div>

        <!-- Notes Section -->
        <div class="notes-section">
            <div class="notes-title">Additional Notes / Special Instructions</div>
            <div class="notes-content">
                <!-- Leave space for manual notes -->
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Received By<br>(Signature)</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Verified By<br>(Signature)</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Quality Check<br>(Signature)</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Approved By<br>(Signature)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <p>This is a system-generated Goods Received Note. Printed on: <?php echo date('d-M-Y H:i:s'); ?></p>
            <p>For inquiries, please contact your supplier or warehouse manager.</p>
        </div>
    </div>
</body>
</html>