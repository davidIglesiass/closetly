<div class="product-details">
    <?php $galleryEager = true; require 'views/product/_gallery.php'; ?>
    <div class="details">
        <h1><?= htmlspecialchars($product->name) ?></h1>
        <p class="price"><?= htmlspecialchars($product->price) ?> USD</p>
        <p><?= nl2br(htmlspecialchars($product->description)) ?></p>
        <a href="/carshop/add&id=<?= $product->id ?>" class="button buy">Buy</a>
    </div>
</div>