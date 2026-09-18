<div id="manage-container" class="m-carshop">
    <h1>Your Cart</h1>
    <?php if (!empty($carshop) && count($carshop) > 0): ?>
        <div id="table-container">
            <table aria-label="Shopping cart">
                <thead>
                <tr>
                    <th>Remove</th>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Units</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($carshop as $key => $value):
                    $product = $value["product"];
                ?>
                <tr>
                    <td>
                        <a href="/carshop/remove&index=<?= $key ?>" class="alert alert_red" aria-label="Remove <?= htmlspecialchars($product->name) ?>">✕</a>
                    </td>
                    <?php if (isset($product->image)): ?>
                        <td><img src="/public/uploads/products/<?= htmlspecialchars($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>" width="100"></td>
                    <?php else: ?>
                        <td><img src="<?= Utils::defaultProductImage($product->categoryName ?? null) ?>" alt="Default image" width="100"></td>
                    <?php endif; ?>
                    <td><a href="/product/show&id=<?= $product->id ?>" class="link-product_carshop"><?= htmlspecialchars($product->name) ?></a></td>
                    <td class="price"><?= htmlspecialchars($product->price) ?> USD</td>
                    <td>
                        <div class="units">
                            <a href="/carshop/down&index=<?= $key?>" class="alert alert_red" aria-label="Decrease quantity">−</a>
                            <span class="qty"><?= $value["units"] ?></span>
                            <a href="/carshop/up&index=<?= $key ?>" class="alert alert_green" aria-label="Increase quantity">+</a>
                        </div>
                    </td>
                    <td class="price"><?= htmlspecialchars(($product->price) * $value["units"]) ?> USD</td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                    <td><strong>Total:</strong></td>
                    <td><strong>$<?= htmlspecialchars(Utils::statsCarShop()["total"]) ?> USD</strong></td>
                </tr>
                </tfoot>
            </table>
            <br>
            <div id="carshop-buttons-actions">
                <a href="/carshop/delete" class="button alert_red">Clear Cart</a>
                <a href="/order/index" class="button alert_green">Checkout</a>
            </div>
        </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
        <a href="/" class="button button-start-buy alert_green">Start Shopping!</a>
    <?php endif; ?>
</div>