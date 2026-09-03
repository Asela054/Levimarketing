<?php
session_start();
if (!isset($_SESSION['userid'])) {
    http_response_code(403);
    exit;
}
require_once('../connection/db.php');

// Select2 ajax params: term (search text), page (1-based), invtype (optional filter)
$term    = trim($_GET['term'] ?? '');
$page    = max(1, (int) ($_GET['page'] ?? 1));
$invtype = ($_GET['invtype'] ?? '') !== '' ? (int) $_GET['invtype'] : null;

$pageSize = 20;
$offset   = ($page - 1) * $pageSize;

$where  = "WHERE status = 1";
$params = [];
$types  = "";

if ($invtype !== null) {
    $where   .= " AND invtype = ?";
    $params[] = $invtype;
    $types   .= "i";
}

if ($term !== '') {
    // Only active invoices (status = 1) are offered for return.
    $where   .= " AND (taxinvoice_no LIKE ? OR manuelinvno LIKE ?)";
    $like     = "%{$term}%";
    $params[] = $like;
    $params[] = $like;
    $types   .= "ss";
}

// Fetch one extra row so we can tell Select2 whether there is a next page
// without running a separate COUNT(*) query.
$sql = "SELECT idtbl_invoice, invtype, manuelinvno, taxinvoice_no, date, total
        FROM tbl_invoice
        $where
        ORDER BY date DESC, idtbl_invoice DESC
        LIMIT ? OFFSET ?";

$params[] = $pageSize + 1;
$params[] = $offset;
$types   .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();

$hasMore = count($rows) > $pageSize;
if ($hasMore) {
    array_pop($rows);
}

$results = array_map(function ($row) {
    $displayNo = ((int) $row['invtype'] === 1) ? $row['taxinvoice_no'] : ('INV-' . $row['manuelinvno']);
    $typeLabel = ((int) $row['invtype'] === 1) ? 'Tax' : 'Non-Tax';
    $amount    = number_format((float) $row['total'], 2);

    return [
        'id'         => $row['idtbl_invoice'],
        // Plain-text fallback Select2 uses for its internal matching/ARIA label.
        'text'       => "[$typeLabel] $displayNo — {$row['date']} — Rs.$amount",
        // Structured fields the frontend uses to render the badge/amount option.
        'display_no' => $displayNo,
        'invtype'    => (int) $row['invtype'],
        'date'       => $row['date'],
        'total'      => (float) $row['total'],
    ];
}, $rows);

header('Content-Type: application/json');
echo json_encode([
    'results'    => $results,
    'pagination' => ['more' => $hasMore],
]);