<?php
// Expects $product in scope. Set $galleryEager = true when it's above-the-fold content (product detail), to skip loading="lazy".
$lazyAttr = ($galleryEager ?? false) ? '' : ' loading="lazy"';
$images = !empty($product->images) ? $product->images : [];
?>
<?php if (count($images) > 1): ?>
    <div class="product-gallery" data-gallery>
        <div class="product-gallery-track">
            <?php foreach ($images as $image): ?>
                <img src="/public/uploads/products/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($product->name) ?>"<?= $lazyAttr ?>>
            <?php endforeach; ?>
        </div>
        <button type="button" class="product-gallery-arrow product-gallery-prev" aria-label="Previous image">&lsaquo;</button>
        <button type="button" class="product-gallery-arrow product-gallery-next" aria-label="Next image">&rsaquo;</button>
        <div class="product-gallery-dots">
            <?php foreach ($images as $i => $image): ?>
                <button type="button" class="product-gallery-dot<?= $i === 0 ? ' active' : '' ?>" aria-label="Image <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
<?php elseif ($images): ?>
    <img src="/public/uploads/products/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($product->name) ?>"<?= $lazyAttr ?>>
<?php else: ?>
    <img src="<?= Utils::defaultProductImage($product->categoryName ?? null) ?>" alt="Default image for <?= htmlspecialchars($product->name) ?>">
<?php endif; ?>
