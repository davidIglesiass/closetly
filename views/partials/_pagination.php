<?php
// Expects $page, $totalPages, $paginationBase (e.g. '/product/manage') in scope.
?>
<?php if ($totalPages > 1) : ?>
    <nav class="pagination" aria-label="Pagination">
        <?php if ($page > 1) : ?>
            <a href="<?= $paginationBase ?>&page=<?= $page - 1 ?>" class="button">&lsaquo; Prev</a>
        <?php else : ?>
            <span class="button" aria-disabled="true">&lsaquo; Prev</span>
        <?php endif; ?>
        <span class="pagination-status">Page <?= $page ?> of <?= $totalPages ?></span>
        <?php if ($page < $totalPages) : ?>
            <a href="<?= $paginationBase ?>&page=<?= $page + 1 ?>" class="button">Next &rsaquo;</a>
        <?php else : ?>
            <span class="button" aria-disabled="true">Next &rsaquo;</span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
