<div id="product-container">
    <?php if (isset($categorie)): ?>
        <?php if (empty($productByCategory)): ?>
            <h1>There are no products in <em>"<?= $categorie->name ?>"</em> category</h1>
        <?php else: ?>
            <?php foreach ($productByCategory as $product): ?>
                <div class="product">
                    <?php if ($product->image != null): ?>
                        <img src="/public/uploads/products/<?= $product->image ?>" alt="product">
                    <?php else: ?>
                        <img src="/assets/img/tshirt-black.png" alt="product">
                    <?php endif; ?>
                    <h2><a class="link-product" href="/product/show&id=<?= $product->id ?>"><?= $product->name ?></a></h2>
                    <p>$<?= $product->price ?> USD</p>
                    <a href="/carshop/add&id=<?= $product->id ?>" class="button alert alert-info buy">Buy</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: ?>
        <h1>The category doesn't exist</h1>
    <?php endif; ?>
</div>