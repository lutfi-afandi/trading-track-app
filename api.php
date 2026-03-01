<?php
require 'config.php';
error_reporting(0);
ini_set('display_errors', 0);

$action = $_GET['action'] ?? '';

if ($action == 'get_dashboard') {
    header('Content-Type: application/json');
    $portfolio = $pdo->query("SELECT p.*, (SELECT MAX(transaction_date) FROM transaction_history WHERE stock_code = p.stock_code AND type = 'BUY') as last_buy_date FROM portfolio p ORDER BY stock_code ASC")->fetchAll(PDO::FETCH_ASSOC);
    $history = $pdo->query("SELECT * FROM transaction_history ORDER BY transaction_date DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $total_pl = $pdo->query("SELECT SUM(profit_loss) FROM transaction_history")->fetchColumn() ?: 0;
    echo json_encode(['portfolio' => $portfolio, 'recent' => $history, 'total_pl' => (float)$total_pl]);
    exit;
}

// Tambahkan ini di dalam api.php setelah bagian get_dashboard
if ($action == 'get_history_all') {
    header('Content-Type: application/json');

    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';

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

    $query .= " ORDER BY transaction_date DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    echo json_encode(['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($action == 'delete_transaction') {
    header('Content-Type: application/json');
    $id = $_POST['id'] ?? 0;

    try {
        $pdo->beginTransaction();

        // 1. Ambil detail transaksi sebelum dihapus
        $stmt = $pdo->prepare("SELECT * FROM transaction_history WHERE id = ?");
        $stmt->execute([$id]);
        $trx = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$trx) throw new Exception("Transaksi tidak ditemukan.");

        $stock = $trx['stock_code'];

        // 2. Hapus transaksi
        $del = $pdo->prepare("DELETE FROM transaction_history WHERE id = ?");
        $del->execute([$id]);

        // 3. HITUNG ULANG PORTFOLIO UNTUK SAHAM INI
        // Ambil semua histori yang tersisa untuk saham ini
        $stmt = $pdo->prepare("SELECT * FROM transaction_history WHERE stock_code = ? ORDER BY transaction_date ASC");
        $stmt->execute([$stock]);
        $all_trx = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hapus data lama di portfolio untuk dikalkulasi ulang
        $pdo->prepare("DELETE FROM portfolio WHERE stock_code = ?")->execute([$stock]);

        if (count($all_trx) > 0) {
            $current_lot = 0;
            $total_cost = 0;

            foreach ($all_trx as $t) {
                if ($t['type'] == 'BUY') {
                    $current_lot += $t['lot'];
                    $total_cost += $t['net_amount']; // net_amount sudah termasuk fee beli
                } else {
                    $current_lot -= $t['lot'];
                    // Saat jual, cost dikurangi secara proporsional dari average sebelumnya
                    // Namun untuk simplifikasi akurasi, kita hitung cost dasar dari sisa lot
                    if ($current_lot <= 0) $total_cost = 0;
                }
            }

            if ($current_lot > 0) {
                $avg_price = $total_cost / ($current_lot * 100);
                $ins = $pdo->prepare("INSERT INTO portfolio (stock_code, total_lot, avg_price, total_cost_inc_fee) VALUES (?, ?, ?, ?)");
                $ins->execute([$stock, $current_lot, $avg_price, $total_cost]);
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $type = $_POST['type'];
    $stock = strtoupper($_POST['stock_code']);
    $price = (float)$_POST['price'];
    $lot = (int)$_POST['lot'];
    $date = $_POST['transaction_date'];
    $notes = $_POST['notes'];

    try {
        $pdo->beginTransaction();
        $st = $pdo->prepare("SELECT * FROM portfolio WHERE stock_code = ?");
        $st->execute([$stock]);
        $p = $st->fetch();

        if ($type === 'BUY') {
            $total_val = $price * $lot * 100;
            $fee = $total_val * 0.0015;
            $net = $total_val + $fee;
            if ($p) {
                $new_lot = $p['total_lot'] + $lot;
                $new_cost = $p['total_cost_inc_fee'] + $net;
                $new_avg = $new_cost / ($new_lot * 100);
                $pdo->prepare("UPDATE portfolio SET total_lot=?, avg_price=?, total_cost_inc_fee=? WHERE stock_code=?")->execute([$new_lot, $new_avg, $new_cost, $stock]);
            } else {
                $pdo->prepare("INSERT INTO portfolio (stock_code, total_lot, avg_price, total_cost_inc_fee) VALUES (?, ?, ?, ?)")->execute([$stock, $lot, ($net / ($lot * 100)), $net]);
            }
            $pdo->prepare("INSERT INTO transaction_history (stock_code, type, transaction_date, price_per_lot, lot, fee, net_amount, notes) VALUES (?, 'BUY', ?, ?, ?, ?, ?, ?)")->execute([$stock, $date, $price, $lot, $fee, $net, $notes]);
        } else {
            if (!$p || $p['total_lot'] < $lot) throw new Exception("Stok tidak cukup!");

            $st_date = $pdo->prepare("SELECT MAX(transaction_date) FROM transaction_history WHERE stock_code = ? AND type = 'BUY'");
            $st_date->execute([$stock]);
            $last_buy = $st_date->fetchColumn();
            if ($last_buy && $date < $last_buy) throw new Exception("Tanggal jual tidak boleh sebelum tanggal beli terakhir ($last_buy)!");

            $total_val = $price * $lot * 100;
            $fee = $total_val * 0.0025;
            $net = $total_val - $fee;
            $cost_basis = $p['avg_price'] * $lot * 100;
            $pl = $net - $cost_basis;
            $new_lot = $p['total_lot'] - $lot;
            if ($new_lot <= 0) {
                $pdo->prepare("DELETE FROM portfolio WHERE stock_code=?")->execute([$stock]);
            } else {
                $new_cost = $p['total_cost_inc_fee'] - $cost_basis;
                $pdo->prepare("UPDATE portfolio SET total_lot=?, total_cost_inc_fee=? WHERE stock_code=?")->execute([$new_lot, $new_cost, $stock]);
            }
            $pdo->prepare("INSERT INTO transaction_history (stock_code, type, transaction_date, price_per_lot, lot, fee, net_amount, profit_loss, notes) VALUES (?, 'SELL', ?, ?, ?, ?, ?, ?, ?)")->execute([$stock, $date, $price, $lot, $fee, $net, $pl, $notes]);
        }
        $pdo->commit();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
