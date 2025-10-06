<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../config/database.php';
require_once '../includes/header.php';
?>

<!-- Load dashboard-specific CSS -->
<link rel="stylesheet" href="../assets/css/dashboard.css">

<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h2>Dashboard <?= ucfirst($_SESSION['role']) ?></h2>
            <p class="small-sub">Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? '') ?> — ini area kerja Anda.</p>
        </div>

        <div class="user-actions">
            <div class="user-info">
                <div class="avatar" aria-hidden="true"><?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?></div>
                <div class="user-meta">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></span>
                    <span class="user-role"><?= ucfirst($_SESSION['role']) ?></span>
                </div>
            </div>

            <div class="header-controls">
                <input id="feature-search" type="search" placeholder="Cari fitur..." aria-label="Cari fitur">

                <!-- Theme toggle: accessible button with visible icons -->
                <button id="theme-toggle" class="icon-btn theme-toggle" title="Ganti tema" aria-pressed="false" aria-label="Toggle light/dark theme">
                    <span class="toggle-icon icon-moon" aria-hidden="true">🌙</span>
                    <span class="toggle-icon icon-sun" aria-hidden="true">☀️</span>
                </button>

                <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="info-card glass">
            <h3>Informasi Akun</h3>
            <div class="info-grid">
                <div>
                    <small>Username</small>
                    <div class="muted"><?= htmlspecialchars($_SESSION['role'] === 'guru' ? ($_SESSION['nip'] ?? '') : ($_SESSION['nis'] ?? '')) ?></div>
                </div>
                <div>
                    <small>Role</small>
                    <div class="muted"><?= ucfirst($_SESSION['role']) ?></div>
                </div>
                <div>
                    <small>Nama Lengkap</small>
                    <div class="muted"><?= htmlspecialchars($_SESSION['nama']) ?></div>
                </div>
            </div>
        </div>

        <div class="features">
            <h3>Fitur Tersedia</h3>
            <div class="feature-grid" id="feature-grid">
                <?php if ($_SESSION['role'] === 'guru'): ?>
                    <div class="feature-card" data-title="Kelola Kelas">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h4>Kelola Kelas</h4>
                        <p>Atur kelas, siswa, dan materi pengajaran.</p>
                    </div>
                    <div class="feature-card" data-title="Input Nilai">
                        <i class="fas fa-clipboard-list"></i>
                        <h4>Input Nilai</h4>
                        <p>Masukkan dan edit nilai siswa dengan mudah.</p>
                    </div>
                    <div class="feature-card" data-title="Jadwal Mengajar">
                        <i class="fas fa-calendar-alt"></i>
                        <h4>Jadwal Mengajar</h4>
                        <p>Kelola jadwal per minggu.</p>
                    </div>
                <?php else: ?>
                    <div class="feature-card" data-title="Lihat Nilai">
                        <i class="fas fa-book"></i>
                        <h4>Lihat Nilai</h4>
                        <p>Cek nilai, jejak akademis, dan raport.</p>
                    </div>
                    <div class="feature-card" data-title="Jadwal Pelajaran">
                        <i class="fas fa-calendar-check"></i>
                        <h4>Jadwal Pelajaran</h4>
                        <p>Lihat jadwal harian dan pengumuman.</p>
                    </div>
                    <div class="feature-card" data-title="Materi Pembelajaran">
                        <i class="fas fa-file-alt"></i>
                        <h4>Materi Pembelajaran</h4>
                        <p>Akses materi dan tugas yang dibagikan guru.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Floating action button -->
    <button id="fab" class="fab" title="Tindakan cepat">+</button>
</div>

<!-- Load dashboard-specific JS -->
<script src="../assets/js/dashboard.js" defer></script>

<?php require_once '../includes/footer.php'; ?>