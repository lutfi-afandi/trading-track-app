<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - TradePulse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/svg+xml" href="favicon.svg">


</head>

<body>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <button id="mobile-toggle" class="btn btn-white border shadow-sm rounded-3 px-3 py-2" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars me-2"></i> Menu
            </button>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">Transaction History</h3>
            </div>

            <!-- card filter -->
            <style>
                /* Menggunakan font utama kamu */
                .filter-card {
                    background: linear-gradient(to right, #ffffff, #f8fafc);
                    border-left: 5px solid #4338ca !important;
                    /* Aksen warna Indigo */
                }

                .accent-line {
                    width: 4px;
                    height: 20px;
                    background-color: #4338ca;
                    border-radius: 10px;
                }

                /* Styling Floating Label & Input */
                .form-floating>.form-control {
                    height: calc(3.5rem + 2px);
                    line-height: 1.25;
                    font-weight: 500;
                    color: #1e293b;
                }

                .form-floating>label {
                    padding: 1rem 0.75rem;
                    color: #64748b !important;
                    font-size: 0.75rem !important;
                }

                /* Custom Buttons */
                .btn-indigo {
                    background-color: #4338ca;
                    color: white;
                    transition: all 0.3s ease;
                }

                .btn-indigo:hover {
                    background-color: #3730a3;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(67, 56, 202, 0.2);
                    color: white;
                }

                .btn-soft-light {
                    background-color: #f1f5f9;
                    border: 1px solid #e2e8f0;
                    transition: all 0.3s ease;
                }

                .btn-soft-light:hover {
                    background-color: #e2e8f0;
                    color: #334155;
                }

                .btn-emerald {
                    background-color: #10b981;
                    color: white;
                    transition: all 0.3s ease;
                }

                .btn-emerald:hover {
                    background-color: #059669;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
                    color: white;
                }

                /* Memisahkan tombol export di layar desktop */
                @media (min-width: 768px) {
                    .ms-auto-md {
                        margin-left: auto !important;
                    }
                }
            </style>
            <div class="card p-4 mb-4 rounded-4 shadow-sm border-0 filter-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="accent-line me-2"></div>
                    <h6 class="mb-0 fw-bold text-dark" style="letter-spacing: 0.5px;">FILTER LAPORAN</h6>
                </div>

                <form id="filter-form" class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <div class="form-floating custom-input-group">
                            <input type="date" class="form-control border-0 bg-light rounded-3" id="start_date" name="start_date" placeholder="Dari Tanggal">
                            <label for="start_date" class="text-muted small fw-bold">DARI TANGGAL</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating custom-input-group">
                            <input type="date" class="form-control border-0 bg-light rounded-3" id="end_date" name="end_date" placeholder="Sampai Tanggal">
                            <label for="end_date" class="text-muted small fw-bold">SAMPAI TANGGAL</label>
                        </div>
                    </div>

                    <div class="col-md-6 d-flex gap-2">
                        <button type="button" id="btn-filter" class="btn btn-indigo px-4 rounded-3 shadow-sm flex-grow-1 flex-md-grow-0">
                            <i class="fa-solid fa-filter me-2"></i> Filter
                        </button>
                        <button type="button" id="btn-reset" class="btn btn-soft-light px-4 rounded-3 text-secondary flex-grow-1 flex-md-grow-0">
                            <i class="fa-solid fa-rotate-left me-2"></i> Reset
                        </button>

                        <div class="ms-auto-md">
                            <button type="button" id="btn-export" class="btn btn-emerald px-4 rounded-3 shadow-sm">
                                <i class="fa-solid fa-file-excel me-2"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card p-4 rounded-4 shadow-sm border-0 bg-white">
                <div class="table-responsive">
                    <table id="table-history" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Tipe</th>
                                <th>Saham</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Lot</th>
                                <th class="text-end">Net</th>
                                <th class="text-end">P/L</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val);
        const formatTanggalIndo = (dateString) => {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
        };



        $(document).ready(function() {

            // Initialize DataTable
            const table = $('#table-history').DataTable({
                ajax: {
                    url: 'api.php?action=get_history_all',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                order: [
                    [0, 'desc']
                ],
                responsive: true,
                // --- TAMBAHKAN BAGIAN INI ---
                columnDefs: [{
                        width: "15%",
                        targets: 0
                    }, // Waktu
                    {
                        width: "8%",
                        targets: 1
                    }, // Tipe (BUY/SELL)
                    {
                        width: "10%",
                        targets: 2
                    }, // Saham
                    {
                        width: "12%",
                        targets: 3
                    }, // Harga
                    {
                        width: "7%",
                        targets: 4
                    }, // Lot
                    {
                        width: "15%",
                        targets: 5
                    }, // Net
                    {
                        width: "15%",
                        targets: 6
                    }, // P/L
                    {
                        width: "18%",
                        targets: 7
                    } // Catatan (lebih lebar biar muat teks)
                ],
                autoWidth: false, // Wajib set false agar ukuran di atas dipatuhi
                // ----------------------------
                columns: [{
                        data: 'transaction_date',
                        render: function(data, type, row) {
                            return type === 'display' ? formatTanggalIndo(data) : data;
                        }
                    },
                    {
                        data: 'type',
                        render: d => `<span class="badge ${d=='BUY'?'bg-success-subtle text-success':'bg-danger-subtle text-danger'}">${d}</span>`
                    },
                    {
                        data: 'stock_code',
                        render: d => `<span class="fw-bold text-dark">${d}</span>`
                    },
                    {
                        data: 'price_per_lot',
                        className: 'text-end',
                        render: d => formatIDR(d)
                    },
                    {
                        data: 'lot',
                        className: 'text-end'
                    },
                    {
                        data: 'net_amount',
                        className: 'text-end',
                        render: d => formatIDR(d)
                    },
                    {
                        data: 'profit_loss',
                        className: 'text-end',
                        render: d => (d && d != 0) ? `<span class="${d>0?'text-success':'text-danger'} fw-bold">${formatIDR(d)}</span>` : '-'
                    },
                    {
                        data: 'notes',
                        render: d => `<div class="text-truncate" style="max-width: 150px;" title="${d || ''}"><small class="text-muted">${d || '-'}</small></div>`
                    }
                ]
            });
            // Action Filter
            $('#btn-filter').click(() => table.ajax.reload());

            // Action Reset
            $('#btn-reset').click(() => {
                $('#start_date, #end_date').val('');
                table.ajax.reload();
            });

            // Action Export Excel
            $('#btn-export').click(() => {
                const start = $('#start_date').val();
                const end = $('#end_date').val();
                // Arahkan ke file PHP khusus export
                window.location.href = `export_excel.php?start_date=${start}&end_date=${end}`;
            });
        });

        function toggleSidebar() {
            $('#sidebar').toggleClass('active');
            $('.sidebar-overlay').toggleClass('show');
        }
    </script>
</body>

</html>