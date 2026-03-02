 <nav id="sidebar" class="p-4 d-flex flex-column">
     <div class="mb-5 px-3">
         <h4 class="fw-bold text-primary mb-0"><i class="fa-solid fa-chart-line me-2"></i>TradePulse</h4>
         <small class="text-muted">Stock Portfolio v2.0</small>
     </div>
     <a href="index.php" id="btn-dashboard" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fa-solid fa-house me-2"></i> Dashboard</a>
     <a href="history.php" id="btn-history" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>"><i class="fa-solid fa-history me-2"></i> Riwayat Transaksi</a>
     <div class="mt-auto p-3 bg-light rounded-4">
         <span class="text-uppercase ls-1 d-block text-muted mb-1" style="font-size: 0.65rem; font-weight: 800;">Realized Profit / Loss</span>
         <div class="d-flex align-items-baseline gap-2">
             <h4 id="stat-total-pl" class="fw-black mb-0 font-numeric" style="font-size: 1.15rem!important;">Rp 0</h4>
             <span id="stat-badge-percent" class="badge rounded-pill bg-success-subtle text-success" style="font-size: 0.6rem;">+0%</span>
         </div>
     </div>
 </nav>