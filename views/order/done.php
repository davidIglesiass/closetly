<div id="manage-container" class="m-carshop">
    <?php if (isset($_SESSION['ordercreated'])): ?>
        <h1>Order Confirmed</h1>
        <p>Your order has been saved successfully. Once payment is confirmed, your products will be shipped to the address you provided.</p>
        <br>
        <?php if (isset($order)): ?>
            <h3>Order Information:</h3>
            Order Number: <?= $order->id ?><br>
            Total to pay: $<?= htmlspecialchars($order->price) ?><br>
            Products:
        <?php endif; ?>
        <div id="table-container">
            <table aria-label="Order products">
                <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Units</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product):?>
                    <tr>
                        <?php if (isset($product->image)): ?>
                            <td><img src="/public/uploads/products/<?= htmlspecialchars($product->image) ?>" alt="product" width="100"></td>
                        <?php else: ?>
                            <td><img src="<?= Utils::defaultProductImage($product->categoryName ?? null) ?>" alt="Default image" width="100"></td>
                        <?php endif; ?>
                        <td><a href="/product/show&id=<?= $product->id ?>" class="link-product_carshop"><?= htmlspecialchars($product->name) ?></a></td>
                        <td class="price"><?= htmlspecialchars($product->price) ?> USD</td>
                        <td><?= $product->quantity ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php elseif (isset($_SESSION['orderfailed'])): ?>
            <h1>Order Failed</h1>
            <p>There was a problem processing your order. Please try again.</p>
        <?php endif; ?>
</div>