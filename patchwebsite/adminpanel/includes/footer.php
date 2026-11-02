<?php
// /patchwebsite/adminpanel/includes/footer.php
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo SITE_URL; ?>assets/js/admin.js"></script>
<script>
    // Fetch order count for badge
    fetch('<?php echo ADMIN_URL; ?>ajax/order-count.php')
        .then(res => res.json())
        .then(data => {
            if (data.count > 0) {
                document.getElementById('orderBadge').textContent = data.count;
                document.getElementById('orderBadge').style.display = 'inline';
            }
        })
        .catch(() => {});
</script>
</body>
</html>