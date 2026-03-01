<?php
require 'config.php';

$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Cek apakah user sedang melakukan filter atau tidak
$is_filtered = (!empty($start_date) && !empty($end_date));

$query = "SELECT * FROM transaction_history WHERE 1=1";
$params = [];

if (!empty($start_date)) {
    $query .= " AND DATE(transaction_date) >= ?";
    $params[] = $start_date;
}
if (!empty($end_date)) {
    $query .= " AND DATE(transaction_date) <= ?";
    $params[] = $end_date;
}

$query .= " ORDER BY transaction_date ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=TradePulse_Report_" . date('Ymd_His') . ".xls");

function formatRp($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html>

<head>
    <style>
        body,
        table {
            font-family: 'Poppins', 'Montserrat', 'Segoe UI', Arial, sans-serif;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        /* Border halus untuk semua cell agar tetap terasa "Excel" */
        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 8px 10px;
            vertical-align: middle;
        }

        /* Kop Surat di-Merge ke Tengah */
        .kop-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            color: #1e293b;
            border: none !important;
        }

        .kop-subtitle {
            font-size: 10pt;
            text-align: center;
            color: #64748b;
            border: none !important;
        }

        /* Header Nuansa Dark */
        .th-header {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9pt;
        }

        /* Group Pekan Biru Muda */
        .group-header {
            background-color: #e0f2fe;
            /* Sky blue light */
            color: #0369a1;
            font-weight: bold;
            font-size: 9pt;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .buy {
            color: #10b981;
            font-weight: bold;
        }

        .sell {
            color: #f43f5e;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <table class="table">
        <tr>
            <td colspan="8" class="kop-title">TRADEPULSE PORTFOLIO SYSTEM</td>
        </tr>
        <tr>
            <td colspan="8" class="kop-subtitle">Laporan Resmi Riwayat Transaksi Saham</td>
        </tr>
        <tr>
            <td colspan="8" class="kop-subtitle" style="padding-bottom: 10px;">
                Mode: <?= $is_filtered ? "Filtered Report" : "Full History (Grouped)" ?> |
                Periode: <?= $is_filtered ? date('d/m/Y', strtotime($start_date)) . " - " . date('d/m/Y', strtotime($end_date)) : "Keseluruhan" ?>
            </td>
        </tr>
        <tr>
            <td colspan="8" style="border:none;"></td>
        </tr>

        <tr>
            <th class="th-header">Tanggal & Waktu</th>
            <th class="th-header">Tipe</th>
            <th class="th-header">Saham</th>
            <th class="th-header">Harga/Lot</th>
            <th class="th-header">Lot</th>
            <th class="th-header">Total Net</th>
            <th class="th-header">Profit/Loss</th>
            <th class="th-header">Catatan</th>
        </tr>

        <?php
        $current_week = '';

        foreach ($data as $row) {
            $date_obj = new DateTime($row['transaction_date']);

            // Logika Grouping: Hanya jalan jika IS NOT FILTERED
            if (!$is_filtered) {
                $start_of_week = clone $date_obj;
                $start_of_week->modify('Monday this week');
                $end_of_week = clone $start_of_week;
                $end_of_week->modify('+6 days');
                $week_range = $start_of_week->format('d M Y') . " - " . $end_of_week->format('d M Y');

                if ($current_week !== $week_range) {
                    echo "<tr><td colspan='8' class='group-header'>📅 Pekan: $week_range</td></tr>";
                    $current_week = $week_range;
                }
            }

            $type_class = ($row['type'] == 'BUY') ? 'buy' : 'sell';
        ?>
            <tr>
                <td class="text-center"><?= $date_obj->format('d/m/Y H:i') ?></td>
                <td class="text-center <?= $type_class ?>"><?= $row['type'] ?></td>
                <td class="text-center" style="font-weight:bold;"><?= $row['stock_code'] ?></td>
                <td class="text-right"><?= formatRp($row['price_per_lot']) ?></td>
                <td class="text-center"><?= $row['lot'] ?></td>
                <td class="text-right"><?= formatRp($row['net_amount']) ?></td>
                <td class="text-right" style="font-weight:bold; color: <?= ($row['profit_loss'] > 0) ? '#10b981' : (($row['profit_loss'] < 0) ? '#f43f5e' : '#64748b') ?>;">
                    <?= ($row['profit_loss'] != 0) ? formatRp($row['profit_loss']) : '-' ?>
                </td>
                <td><?= htmlspecialchars($row['notes']) ?></td>
            </tr>
        <?php } ?>
    </table>

</body>

</html>