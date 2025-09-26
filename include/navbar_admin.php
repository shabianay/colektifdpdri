<?php
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo ($role == 'setjen') ? 'setjen_dashboard.php' : 'dewan_dashboard.php'; ?>">
        <img src="../img/logodpdri.png" class="mb-2" style="width:25%; height: auto;">
        <div class="sidebar-brand-text mx-3">Colektif</div>
    </a>

    <hr class="sidebar-divider my-0" />

    <!-- Dashboard -->
    <li class="nav-item <?php echo $current_page == (($role == 'setjen') ? 'setjen_dashboard.php' : 'dewan_dashboard.php') ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo ($role == 'setjen') ? 'setjen_dashboard.php' : 'dewan_dashboard.php'; ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider my-0" />

    <!-- Menu Umum -->
    <?php
    $menu_items = [
        ['page' => 'input_berkas.php', 'icon' => 'fa-solid fa-book', 'label' => 'Input Berkas'],
        ['page' => 'edit_berkas.php', 'icon' => 'fa-solid fa-book', 'label' => 'Edit Berkas'],
        ['page' => 'detail_berkas.php', 'icon' => 'fa-solid fa-book', 'label' => 'Detail Berkas'],
        ['page' => 'daftar_berkas_masuk.php', 'icon' => 'fa-solid fa-book', 'label' => 'Daftar Berkas Masuk'],
        ['page' => 'cari_berkas.php', 'icon' => 'fa-solid fa-magnifying-glass', 'label' => 'Cari Berkas']
    ];

    foreach ($menu_items as $item) {
        $active = ($current_page == $item['page']) ? 'active' : '';
        echo '<hr class="sidebar-divider my-0" />';
        echo '<li class="nav-item ' . $active . '">';
        echo '<a class="nav-link" href="' . $item['page'] . '">';
        echo '<i class="' . $item['icon'] . '"></i>';
        echo '<span>' . $item['label'] . '</span>';
        echo '</a></li>';
    }
    ?>

    <!-- Tambahkan CSS override -->
    <style>
        .sidebar .sidebar-brand-text {
            text-transform: none !important;
            font-size: 1.5rem;
        }

        .sidebar .nav-item .nav-link i {
            font-size: 1.2rem;
        }

        .sidebar .nav-item .nav-link span {
            font-size: 1rem;
        }
    </style>
    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="logoutConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="logoutConfirmationModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="../include/logout.php" class="btn btn-primary">Yakin</a>
                </div>
            </div>
        </div>
    </div>

    <hr class="sidebar-divider d-none d-md-block" />

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get all the navigation links
        var navLinks = document.querySelectorAll('.nav-link');

        // Add click event listeners to each link
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                // Remove the "active" class from all links
                navLinks.forEach(function(navLink) {
                    navLink.classList.remove('active');
                });

                // Add the "active" class to the clicked link
                this.classList.add('active');
            });
        });
    });
</script>