<div id="product-container" role="main" aria-label="Category products">
    <?php if (isset($categorie)): ?>
        <h1><?= $categorie->departmentName ? htmlspecialchars($categorie->departmentName) . ' / ' : '' ?><?= htmlspecialchars($categorie->name) ?></h1>
        <?php if (empty($productByCategory)): ?>
            <p>There are no products in this category yet.</p>
        <?php else: ?>
            <?php foreach ($productByCategory as $product): ?>
                <article class="product">
                    <?php require 'views/product/_gallery.php'; ?>
                    <h2><a class="link-product" href="/product/show&id=<?= $product->id ?>"><?= htmlspecialchars($product->name) ?></a></h2>
                    <p class="price"><?= htmlspecialchars($product->price) ?> USD</p>
                    <a href="/carshop/add&id=<?= $product->id ?>" class="button buy">Buy</a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: ?>
        <h1>The category doesn't exist</h1>
    <?php endif; ?>
</div>