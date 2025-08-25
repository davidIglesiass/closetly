<div class="product-details">
    <?php if ($product->image != null): ?>
        <img src="/public/uploads/products/<?= $product->image ?>" alt="product">
    <?php else: ?>
        <img src="/assets/img/tshirt-black.png" alt="product">
    <?php endif; ?>
    <div class="details">
        <h2><?= $product->name ?></h2>
        <p>$<?= $product->price ?> USD</p>
        <p><?= $product->description ?></p>
        <a href="/carshop/add&id=<?= $product->id ?>" class="button alert alert-info buy">Buy</a>
    </div>
</div>