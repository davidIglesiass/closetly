<h1 class="visually-hidden">Shop Closetly</h1>
<div id="product-container" role="main" aria-label="Product listing">
    <?php foreach ($products as $product): ?>
        <article class="product">
            <?php require 'views/product/_gallery.php'; ?>
            <h2><a class="link-product" href="/product/show&id=<?= $product->id ?>"><?= htmlspecialchars($product->name) ?></a></h2>
            <p class="price"><?= htmlspecialchars($product->price) ?> USD</p>
            <a href="/carshop/add&id=<?= $product->id ?>" class="button buy">Buy</a>
        </article>
    <?php endforeach; ?>
</div>