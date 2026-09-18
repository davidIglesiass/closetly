<div id="manage-container" class="m-carshop">
    <?php if (isset($order)): ?>
        <h1>My Order</h1>
            <div class="order-details">
                <?php if (isset($_SESSION['admin'])): ?>
                    <h3>Change Order Status:</h3>
                    <form action="/order/status" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
                        <input type="hidden" name="id" value="<?= $order->id ?>">
                        <div class="units">
                            <select name="status" id="status">
                                <option value="requested" <?= $order->status == 'requested' ? 'selected' : ''  ?>>Requested</option>
                                <option value="paid" <?= $order->status == 'paid' ? 'selected' : ''  ?>>Paid</option>
                                <option value="shipped" <?= $order->status == 'shipped' ? 'selected' : ''  ?>>Shipped</option>
                                <option value="delivered" <?= $order->status == 'delivered' ? 'selected' : ''  ?>>Delivered</option>
                                <option value="cancelled" <?= $order->status == 'cancelled' ? 'selected' : ''  ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="button" aria-label="Update status" title="Update status"><?= Utils::icon('save') ?></button>
                        </div>
                    </form>
                <?php endif; ?>
                <hr>
                <h3>Shipping Address</h3>
                <p><strong>State:</strong> <?= htmlspecialchars($order->state) ?></p>
                <p><strong>City:</strong> <?= htmlspecialchars($order->city) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($order->address) ?></p>
                <hr>
                <h3>Order Details:</h3>
                <p><strong>Status:</strong> <?= htmlspecialchars(Utils::showStatus($order->status)) ?></p>
                <p><strong>Order Number:</strong> <?= $order->id ?></p>
                <p><strong>Total:</strong> $<?= htmlspecialchars($order->price) ?> USD</p>
                <p><strong>Products:</strong></p>
            </div>
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
                <?php foreach ($products as $product): ?>
                    <tr>
                        <?php if (isset($product->image)): ?>
                            <td><img src="/public/uploads/products/<?= htmlspecialchars($product->image) ?>" alt="product" width="100"></td>
                        <?php else: ?>
                            <td><img src="<?= Utils::defaultProductImage($product->categoryName ?? null) ?>" alt="Default image" width="100"></td>
                        <?php endif; ?>
                        <td><a href="/product/show&id=<?= $product->id ?>" class="link-product_carshop"><?= htmlspecialchars($product->name) ?></a></td>
                        <td class="price">$<?= htmlspecialchars($product->price) ?> USD</td>
                        <td><?= $product->quantity ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <h1>Order not found</h1>
    <?php endif; ?>
</div>
