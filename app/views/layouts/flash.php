<?php 
declare(strict_types=1);
$flash = Flash::get(); 
?>
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>
