<?php
if (!$user) {
    header("Location: /", true, 302);
    exit;
}
?>

<!-- Initialize app with data -->
<script>
    // @ts-check
    window.APP_INITIAL_DATA = {
        user: <?= json_encode($user->toFlatArray()) ?>
    };
</script>

<div v-scope class="flex items-center justify-center min-h-[60vh] sm:min-h-[70vh] py-6 sm:py-12" v-cloak>
    <?php include '_card.php'; ?>
</div>