</div> <!-- End container-fluid -->
    
    <!-- Footer -->
    <footer class="footer mt-5 py-3 bg-light">
        <div class="container-fluid text-center">
            <span class="text-muted">
                &copy; <?php echo date('Y'); ?> Cao đẳng Nghề TP.HCM - Hệ thống Quản lý Hợp đồng Thỉnh giảng
                <?php if (defined('APP_VERSION')): ?>
                    | Version <?php echo APP_VERSION; ?>
                <?php endif; ?>
            </span>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (cho DataTables và tiện ích khác) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS (nếu cần) -->
    <?php if (isset($useDataTables) && $useDataTables): ?>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <?php endif; ?>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/public/js/main.js"></script>
    
    <!-- Page-specific scripts -->
    <?php if (isset($additionalScripts)): ?>
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
    
    <script>
        // Auto dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>