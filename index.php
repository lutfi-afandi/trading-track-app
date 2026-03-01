<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTrade | Stock Portfolio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7fa;
            color: #334155;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            min-height: 100vh;
            position: fixed;
            transition: all 0.3s;
        }

        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 40px;
        }

        .nav-link {
            color: #64748b;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 5px;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .nav-link:hover {
            background: #f8fafc;
            color: #4f46e5;
        }

        .nav-link.active {
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* Modern Card */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: none;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px;
            font-weight: 700;
        }

        /* Trade Toggle Buy/Sell */
        .trade-toggle {
            background: #f1f5f9;
            padding: 5px;
            border-radius: 12px;
            display: flex;
        }

        .trade-toggle input[type="radio"] {
            display: none;
        }

        .trade-toggle label {
            flex: 1;
            text-align: center;
            padding: 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.85rem;
            transition: 0.2s;
            color: #64748b;
        }

        #buy-radio:checked+label {
            background: #fff;
            color: #059669;
        }

        #sell-radio:checked+label {
            background: #fff;
            color: #dc2626;
        }

        /* Table Style */
        .table-portfolio thead th {
            background-color: #f8fafc;
            padding: 1.25rem 1rem !important;
            color: #64748b;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #f1f5f9;
        }

        .table-portfolio tbody td {
            padding: 1.05rem 1rem !important;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-buy {
            background: #ecfdf5;
            color: #059669;
        }

        .badge-sell {
            background: #fef2f2;
            color: #dc2626;
        }

        .stock-badge {
            font-weight: 800;
            color: #1e293b;
            font-size: 1rem;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            min-height: 100vh;
            position: fixed;
            transition: all 0.3s;
            z-index: 1050;
            /* Pastikan di atas konten */
        }

        /* Responsivitas untuk Layar Kecil */
        @media (max-width: 992px) {
            #sidebar {
                margin-left: -260px;
                /* Sembunyikan sidebar ke kiri */
            }

            #sidebar.active {
                margin-left: 0;
                /* Munculkan saat class 'active' ditambah */
            }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 20px !important;
            }

            /* Overlay saat sidebar muncul di mobile */
            .sidebar-overlay {
                display: none;
                position: fixed;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 40px;
            transition: all 0.3s;
        }

        /* Tombol Hamburger (Hanya muncul di mobile) */
        #mobile-toggle {
            display: none;
        }

        @media (max-width: 992px) {
            #mobile-toggle {
                display: block;
                margin-bottom: 20px;
            }
        }
    </style>

    <style>
        /* Luxury Stat Card */
        .stat-card-luxury {
            background: #ffffff;
            padding: 15px 25px;
            border-radius: 24px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .stat-card-luxury:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.06);
        }

        .stat-icon-circle {
            width: 48px;
            height: 48px;
            background: #f8fafc;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            font-size: 1.25rem;
            border: 1px solid #f1f5f9;
        }

        .fw-black {
            font-weight: 900;
        }

        .ls-1 {
            letter-spacing: 1px;
        }

        .font-numeric {
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.5px;
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    <div class="d-flex">
        <nav id="sidebar" class="p-4 d-flex flex-column">
            <div class="mb-5 px-3">
                <h4 class="fw-bold text-primary mb-0"><i class="fa-solid fa-chart-line me-2"></i>TradePulse</h4>
                <small class="text-muted">Stock Portfolio v2.0</small>
            </div>
            <a href="index.php" id="btn-dashboard" class="nav-link active"><i class="fa-solid fa-house me-2"></i> Dashboard</a>
            <a href="history.php" id="btn-history" class="nav-link"><i class="fa-solid fa-history me-2"></i> Riwayat Transaksi</a>
            <div class="mt-auto p-3 bg-light rounded-4">
                <p class="small mb-0 text-muted">Realized Profit</p>
                <h5 id="total-profit" class="fw-bold text-success">Rp 0</h5>
            </div>
        </nav>

        <div class="main-content">
            <div id="page-dashboard" class="page-section">


                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h3 class="fw-bold tracking-tight mb-0">Portfolio Overview</h3>
                        <p class="text-muted small mb-0">Monitoring your investment performance</p>
                    </div>

                    <div class="stat-card-luxury">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon-circle">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <div>
                                <span class="text-uppercase ls-1 d-block text-muted mb-1" style="font-size: 0.65rem; font-weight: 800;">Realized Profit / Loss</span>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h4 id="stat-total-pl" class="fw-black mb-0 font-numeric">Rp 0</h4>
                                    <span id="stat-badge-percent" class="badge rounded-pill bg-success-subtle text-success" style="font-size: 0.7rem;">+0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card p-4 rounded-5 shadow-sm">
                            <h5 class="fw-bold mb-4">New Trade</h5>
                            <form id="form-trade" class="row g-3">
                                <div class="col-12">
                                    <div class="trade-toggle">
                                        <input type="radio" name="type" id="buy-radio" value="BUY" checked>
                                        <label for="buy-radio">BUY</label>
                                        <input type="radio" name="type" id="sell-radio" value="SELL">
                                        <label for="sell-radio">SELL</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Stock Code</label>
                                    <div id="stock-input-container">
                                        <input type="text" name="stock_code" id="stock_input" class="form-control form-control-lg p-3 bg-light border-0 rounded-4 text-uppercase fw-bold" placeholder="ANTM" required>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Price / Lot</label>
                                    <input type="number" name="price" id="trade_price" class="form-control form-control-lg p-3 bg-light border-0 rounded-4" placeholder="0" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Lot Size</label>
                                    <input type="number" name="lot" id="trade_lot" class="form-control form-control-lg p-3 bg-light border-0 rounded-4" placeholder="0" required>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 rounded-4 border-dashed border-2 bg-light/50" style="border-style: dashed; border-color: #dee2e6;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="small text-muted">Est. Net Amount (inc. Fee)</span>
                                            <span id="preview-net" class="fw-bold text-dark">Rp 0</span>
                                        </div>
                                        <div id="preview-pl-row" class="d-flex justify-content-between d-none">
                                            <span class="small text-muted">Est. Profit/Loss</span>
                                            <span id="preview-pl" class="fw-bold">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Date & Time</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar3"></i></span>
                                        <input type="text" name="transaction_date" id="date_picker" class="form-control p-3 bg-light border-0 rounded-end-4" placeholder="Pilih Tanggal & Waktu" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <textarea name="notes" class="form-control bg-light border-0 p-3 rounded-4" rows="4" placeholder="Notes (optional)..."></textarea>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow">Execute Trade</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="d-flex flex-column gap-4">
                            <div class="card overflow-hidden">
                                <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0">Running Portfolio</h5>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2" style="font-size: 0.65rem;">Moving Average</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-portfolio mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">SAHAM</th>
                                                <th class="text-center">LOT</th>
                                                <th class="text-end">AVG PRICE</th>
                                                <th class="text-end pe-4">TOTAL VALUE</th>
                                            </tr>
                                        </thead>
                                        <tbody id="porto-list"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card overflow-hidden">
                                <div class="card-header bg-white py-3">
                                    <span class="fw-bold">Recent Transactions</span>
                                </div>
                                <div id="recent-activity-list" class="list-group list-group-flush"></div>
                                <div class="card-footer bg-white text-center py-3">
                                    <a href="history.php" class="btn btn-link btn-sm text-decoration-none fw-bold">View All History <i class="fa-solid fa-chevron-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val);

        let currentPortfolio = [];

        const formatTanggalIndo = (dateString) => {
            if (!dateString) return '-';
            const date = new Date(dateString);
            const d = String(date.getDate()).padStart(2, '0');
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const y = date.getFullYear();
            const h = String(date.getHours()).padStart(2, '0');
            const min = String(date.getMinutes()).padStart(2, '0');
            return `${d}-${m}-${y} ${h}:${min}`;
        };

        const fp = flatpickr("#date_picker", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            altInput: true,
            altFormat: "d-m-Y H:i",
            time_24hr: true,
            defaultDate: new Date()
        });

        function loadDashboard() {
            $.getJSON('api.php?action=get_dashboard', function(res) {
                currentPortfolio = res.portfolio;
                const plText = formatIDR(res.total_pl);
                $('#total-profit, #stat-total-pl').text(plText).toggleClass('text-success', res.total_pl >= 0).toggleClass('text-danger', res.total_pl < 0);

                let portoHtml = '';
                res.portfolio.forEach(p => {
                    portoHtml += `<tr>
                        <td class="ps-4"><div class="stock-badge">${p.stock_code}</div></td>
                        <td class="text-center"><span class="badge bg-light text-dark rounded-3 px-3 py-2 fw-bold border">${p.total_lot}</span></td>
                        <td class="text-end"><div>${formatIDR(p.avg_price)}</div></td>
                        <td class="text-end pe-4"><div class="total-value-tag fw-bold text-primary">${formatIDR(p.total_cost_inc_fee)}</div></td>
                    </tr>`;
                });
                $('#porto-list').html(portoHtml || '<tr><td colspan="4" class="text-center p-5 text-muted">No holdings.</td></tr>');

                let recentHtml = '';
                res.recent.forEach(h => {
                    const isBuy = h.type === 'BUY';
                    const icon = isBuy ? 'fa-bag-shopping' : 'fa-dollar-sign';
                    const themeColor = isBuy ? '#6366f1' : '#f43f5e';
                    const bgColor = isBuy ? '#e0e7ff' : '#fff1f2';

                    recentHtml += `
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-0 border-bottom hover-light">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle shadow-sm" style="width: 45px; height: 45px; background-color: ${bgColor}; color: ${themeColor}; display: flex; align-items: center; justify-content: center; font-size: 1rem; border: 1px solid rgba(0,0,0,0.05);">
                                <i class="fa-solid ${icon}"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-0 text-dark">${h.stock_code}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge" style="font-size: 0.55rem; background-color: ${bgColor}; color: ${themeColor};">${h.type}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">${formatTanggalIndo(h.transaction_date)}</small>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark">${h.lot} Lot</span>
                            <div class="${h.profit_loss > 0 ? 'text-success' : 'text-danger'} fw-bold" style="font-size: 0.85rem;">
                                ${h.profit_loss != 0 ? (h.profit_loss > 0 ? '+' : '') + formatIDR(h.profit_loss) : ''}
                            </div>
                        </div>
                    </div>`;
                });
                $('#recent-activity-list').html(recentHtml || '<div class="p-4 text-center text-muted">No activity.</div>');
                updateFormLogic();
            });
        }

        function updateFormLogic() {
            const type = $('input[name="type"]:checked').val();
            const container = $('#stock-input-container');

            if (type === 'SELL') {
                let options = `<select name="stock_code" id="stock_select" class="form-select form-select-lg p-3 bg-light border-0 rounded-4 fw-bold" required>
                                <option value="">Pilih Saham...</option>`;
                currentPortfolio.forEach(p => {
                    options += `<option value="${p.stock_code}" data-lot="${p.total_lot}" data-avg="${p.avg_price}" data-lastbuy="${p.last_buy_date}">${p.stock_code} (Avail: ${p.total_lot})</option>`;
                });
                options += `</select>`;
                container.html(options);
                $('#preview-pl-row').removeClass('d-none');
            } else {
                container.html(`<input type="text" name="stock_code" id="stock_input" class="form-control form-control-lg p-3 bg-light border-0 rounded-4 text-uppercase fw-bold" placeholder="ANTM" required>`);
                $('#preview-pl-row').addClass('d-none');
                fp.set('minDate', null);
            }
        }

        // VALIDASI OTOMATIS LOT (TIDAK BISA MELEBIHI STOK)
        $(document).on('input', '#trade_lot', function() {
            const type = $('input[name="type"]:checked').val();
            if (type === 'SELL') {
                const maxLot = parseInt($('#stock_select option:selected').data('lot')) || 0;
                const inputLot = parseInt($(this).val()) || 0;
                if (inputLot > maxLot) {
                    $(this).val(maxLot); // Otomatis isi ke jumlah lot maksimal
                }
            }
        });

        $(document).on('change', '#stock_select', function() {
            const selected = $(this).find(':selected');
            const lastBuy = selected.data('lastbuy');
            if (lastBuy) {
                fp.set('minDate', lastBuy);
                if (fp.selectedDates[0] < new Date(lastBuy)) {
                    fp.setDate(lastBuy);
                }
            }
        });

        $(document).on('input change', '#trade_price, #trade_lot, #stock_select, input[name="type"]', function() {
            const type = $('input[name="type"]:checked').val();
            const price = parseFloat($('#trade_price').val()) || 0;
            const lot = parseFloat($('#trade_lot').val()) || 0;

            if (price > 0 && lot > 0) {
                const totalValue = price * lot * 100;
                const feeRate = type === 'BUY' ? 0.0015 : 0.0025;
                const netAmount = type === 'BUY' ? totalValue + (totalValue * feeRate) : totalValue - (totalValue * feeRate);
                $('#preview-net').text(formatIDR(netAmount));

                if (type === 'SELL') {
                    const avgPrice = parseFloat($('#stock_select option:selected').data('avg')) || 0;
                    if (avgPrice > 0) {
                        const pl = netAmount - (avgPrice * lot * 100);
                        $('#preview-pl').text(formatIDR(pl)).removeClass('text-success text-danger').addClass(pl >= 0 ? 'text-success' : 'text-danger');
                    }
                }
            }
        });

        $('#form-trade').on('submit', function(e) {
            e.preventDefault();
            $.post('api.php', $(this).serialize(), function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Trade Executed!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    $('#form-trade')[0].reset();
                    loadDashboard();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            });
        });

        $(document).on('change', 'input[name="type"]', updateFormLogic);
        $(document).ready(() => loadDashboard());

        function toggleSidebar() {
            $('#sidebar').toggleClass('active');
            $('.sidebar-overlay').toggleClass('show');
        }
    </script>
</body>

</html>