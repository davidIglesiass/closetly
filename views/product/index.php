<div id="product-container">
    <?php foreach ($products as $product): ?>
        <div class="product">
            <?php if ($product->image != null): ?>
                <img src="public/uploads/products/<?= $product->image ?>" alt="product">
            <?php else: ?>
                <img src="assets/img/tshirt-black.png" alt="product">
            <?php endif; ?>
            <h2><a class="link-product" href="/product/show&id=<?= $product->id ?>"><?= $product->name ?></a></h2>
            <p>$<?= $product->price ?> USD</p>
            <a href="/carshop/add&id=<?= $product->id ?>" class="button buy">Buy</a>
        </div>
    <?php endforeach; ?>
</div>