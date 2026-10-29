<?php
require_once 'db.php';
check_session_guard();

$ta_name = isset($_GET['ta_name']) ? trim($_GET['ta_name']) : '';
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

function validDate(string $date): bool {
    $d = DateTime::createFromFormat('!Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

$error = '';
$client_profile = [];
$statement_jobs = [];
$total_jobs_count = 0;
$gross_billed_sum = 0.00;
$total_paid_sum = 0.00;
$current_balance = 0.00;
$statement_ready = false;

if ($ta_name !== '' && $start_date !== '' && $end_date !== '') {
    if (!validDate($start_date) || !validDate($end_date)) {
        $error = 'Please select valid start and end dates.';
    } elseif ($start_date > $end_date) {
        $error = 'Start Date cannot be later than Till Date.';
    } else {
        $stmt_client = $pdo->prepare("SELECT * FROM clients WHERE name = ? LIMIT 1");
        $stmt_client->execute([$ta_name]);
        $client_profile = $stmt_client->fetch(PDO::FETCH_ASSOC) ?: [];

        // Include both selected dates. Using a half-open range also works if received_date is DATETIME.
        $range_end = (new DateTime($end_date))->modify('+1 day')->format('Y-m-d');
        $stmt_jobs = $pdo->prepare(
            "SELECT * FROM work_tracker
             WHERE ta_name = ? AND received_date >= ? AND received_date < ?
             ORDER BY received_date ASC, id ASC"
        );
        $stmt_jobs->execute([$ta_name, $start_date, $range_end]);
        $statement_jobs = $stmt_jobs->fetchAll(PDO::FETCH_ASSOC);

        $total_jobs_count = count($statement_jobs);
        foreach ($statement_jobs as $job) {
            $gross_billed_sum += (float)($job['bill_amount'] ?? 0);
            $total_paid_sum += (float)($job['paid_amount'] ?? 0);
        }
        $current_balance = $gross_billed_sum - $total_paid_sum;
        $statement_ready = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Payment Statement<?php echo $ta_name !== '' ? ' - ' . htmlspecialchars($ta_name, ENT_QUOTES, 'UTF-8') : ''; ?></title>
<style>
    * { box-sizing: border-box; }
    body { margin: 0; padding: 24px; font-family: "Segoe UI", Arial, sans-serif; color: #243047; background: #f3f6fb; font-size: 14px; }
    .page { max-width: 1200px; margin: 0 auto; }
    .toolbar, .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 18px rgba(25, 42, 70, .04); }
    .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
    h1 { margin: 0 0 5px; font-size: 24px; color: #172554; }
    .muted { color: #64748b; }
    .filters { display: grid; grid-template-columns: repeat(4, minmax(160px, 1fr)); gap: 14px; align-items: end; }
    label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 7px; }
    input { width: 100%; border: 1px solid #cbd5e1; border-radius: 7px; padding: 11px 12px; font: inherit; background: white; color: #0f172a; }
    button, .btn { border: 0; border-radius: 7px; padding: 11px 16px; font: inherit; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; }
    .primary { background: #1d4ed8; color: #fff; }
    .secondary { background: #e2e8f0; color: #1e293b; }
    .print { background: #0f766e; color: #fff; }
    .error { padding: 12px 14px; border-radius: 8px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; margin-bottom: 18px; }
    .statement-head { display: flex; justify-content: space-between; gap: 18px; flex-wrap: wrap; border-bottom: 2px solid #1e3a8a; padding-bottom: 18px; margin-bottom: 20px; }
    .brand { font-weight: 800; font-size: 23px; color: #172554; letter-spacing: .3px; }
    .brand span { color: #0d9488; }
    .statement-title { text-align: right; color: #475569; }
    .statement-title strong { display: block; font-size: 18px; color: #172554; }
    .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .meta-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; }
    .meta-box h3 { margin: 0 0 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .7px; color: #64748b; }
    .meta-box p { margin: 5px 0; }
    .cards { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin: 18px 0 24px; }
    .metric { border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; background: #fff; }
    .metric .label { color: #64748b; font-size: 12px; font-weight: 700; margin-bottom: 8px; }
    .metric .amount { font-size: 23px; font-weight: 800; color: #172554; overflow-wrap: anywhere; }
    .metric.paid .amount { color: #15803d; }
    .metric.balance .amount { color: <?php echo $current_balance > 0 ? '#b91c1c' : '#15803d'; ?>; }
    .table-wrap { width: 100%; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 850px; }
    th { background: #172554; color: #fff; text-align: left; padding: 11px 9px; font-size: 11px; text-transform: uppercase; letter-spacing: .3px; }
    td { border-bottom: 1px solid #e2e8f0; padding: 10px 9px; vertical-align: top; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    .num { text-align: right; white-space: nowrap; }
    .center { text-align: center; }
    .empty { padding: 26px; text-align: center; color: #64748b; }
    .footnote { margin-top: 22px; border-top: 1px solid #e2e8f0; padding-top: 12px; text-align: center; color: #64748b; font-size: 11px; }
    @media (max-width: 760px) {
        body { padding: 12px; }
        .filters { grid-template-columns: 1fr 1fr; }
        .meta { grid-template-columns: 1fr; }
        .cards { grid-template-columns: 1fr; }
        .statement-title { text-align: left; }
    }
    @media print {
        body { background: #fff; padding: 0; font-size: 11px; }
        .no-print, .filters-panel { display: none !important; }
        .panel { border: 0; box-shadow: none; padding: 0; margin: 0; }
        .toolbar { display: none; }
        .metric { padding: 10px; }
        .metric .amount { font-size: 17px; }
        .statement-head { margin-top: 0; }
        .meta-box { background: #fff; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
    }
</style>
</head>
<body>
<div class="page">
    <div class="toolbar no-print">
        <div>
            <h1>Payment Statement</h1>
            <div class="muted">Generate a statement for any selected date range.</div>
        </div>
        <?php if ($statement_ready): ?>
            <button class="print" type="button" onclick="window.print()">Print Statement</button>
        <?php endif; ?>
    </div>

    <div class="panel filters-panel no-print">
        <?php if ($error !== ''): ?><div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
        <form method="get" class="filters">
            <div>
                <label for="ta_name">Technical Assistant (TA)</label>
                <input id="ta_name" name="ta_name" value="<?php echo htmlspecialchars($ta_name, ENT_QUOTES, 'UTF-8'); ?>" required placeholder="Enter TA name">
            </div>
            <div>
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div>
                <label for="end_date">Till Date</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div><button type="submit" class="primary">Generate Statement</button></div>
        </form>
    </div>

    <?php if ($statement_ready): ?>
    <div class="panel">
        <div class="statement-head">
            <div>
                <div class="brand">SECURE <span>SUITE V3</span></div>
                <div class="muted">Work and payment account statement</div>
            </div>
            <div class="statement-title">
                <strong>Payment Statement</strong>
                <div><?php echo date('d-M-Y', strtotime($start_date)); ?> to <?php echo date('d-M-Y', strtotime($end_date)); ?></div>
                <div class="muted">Generated: <?php echo date('d-M-Y h:i A'); ?></div>
            </div>
        </div>

        <div class="meta">
            <div class="meta-box">
                <h3>Account Details</h3>
                <p><strong>TA Name:</strong> <?php echo htmlspecialchars($ta_name, ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Block:</strong> <?php echo htmlspecialchars($client_profile['block'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($client_profile['phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div class="meta-box">
                <h3>Statement Period</h3>
                <p><strong>From:</strong> <?php echo date('d-m-Y', strtotime($start_date)); ?></p>
                <p><strong>Till:</strong> <?php echo date('d-m-Y', strtotime($end_date)); ?></p>
                <p><strong>Total Jobs:</strong> <?php echo $total_jobs_count; ?></p>
            </div>
        </div>

        <div class="cards">
            <div class="metric">
                <div class="label">TOTAL BILLED AMOUNT</div>
                <div class="amount">₹<?php echo number_format($gross_billed_sum, 2); ?></div>
            </div>
            <div class="metric paid">
                <div class="label">TOTAL PAYMENT COLLECTED</div>
                <div class="amount">₹<?php echo number_format($total_paid_sum, 2); ?></div>
            </div>
            <div class="metric balance">
                <div class="label">CURRENT BALANCE</div>
                <div class="amount">₹<?php echo number_format($current_balance, 2); ?></div>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="center">Job ID</th>
                        <th>Estimate No.</th>
                        <th>Work Description</th>
                        <th>Gram Panchayat</th>
                        <th>Work Date</th>
                        <th>Status</th>
                        <th class="num">Billed Amount</th>
                        <th class="num">Payment Collected</th>
                        <th class="num">Balance</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total_jobs_count > 0): ?>
                    <?php foreach ($statement_jobs as $job):
                        $job_billed = (float)($job['bill_amount'] ?? 0);
                        $job_paid = (float)($job['paid_amount'] ?? 0);
                        $job_balance = $job_billed - $job_paid;
                        $status = (string)($job['status'] ?? 'Pending');
                    ?>
                    <tr>
                        <td class="center">#<?php echo (int)$job['id']; ?></td>
                        <td><?php echo htmlspecialchars($job['estimate_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($job['work_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($job['gp_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo !empty($job['received_date']) ? date('d-m-Y', strtotime($job['received_date'])) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="num">₹<?php echo number_format($job_billed, 2); ?></td>
                        <td class="num">₹<?php echo number_format($job_paid, 2); ?></td>
                        <td class="num">₹<?php echo number_format($job_balance, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="6" class="num"><strong>TOTAL</strong></td>
                        <td class="num"><strong>₹<?php echo number_format($gross_billed_sum, 2); ?></strong></td>
                        <td class="num"><strong>₹<?php echo number_format($total_paid_sum, 2); ?></strong></td>
                        <td class="num"><strong>₹<?php echo number_format($current_balance, 2); ?></strong></td>
                    </tr>
                <?php else: ?>
                    <tr><td colspan="9" class="empty">No work entries found for this TA within the selected date range.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="footnote">This statement covers work records from <?php echo date('d-m-Y', strtotime($start_date)); ?> through <?php echo date('d-m-Y', strtotime($end_date)); ?>, inclusive.</div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
