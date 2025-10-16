<footer class="text-center py-4 bg-light mt-5 border-top">
    <p class="mb-0">&copy; <?= date('Y') ?> Mohammad Tri Putra Teamplate | Dibuat dengan ❤️ dan Bootstrap 5</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/main.js') ?>"></script>

<!-- Library SweetAlert JS -->
<script src="<?= base_url('assets/lib/sweetalert2/sweetalert2.min.js') ?>"></script>

<?php
$flash = get_flash('swal');
if ($flash): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        text: <?= json_encode($flash['msg']) ?>,
        icon: <?= json_encode($flash['type']) ?>,
        confirmButtonText: 'OK'
    });
});
</script>
<?php endif; ?>
</body>

</html>