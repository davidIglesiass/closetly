<?php $hasOrderForm = isset($_SESSION['identity']) && !empty($carshop); ?>
<div id="<?= $hasOrderForm ? 'manage-container' : 'register-container' ?>" class="<?= $hasOrderForm ? 'm-carshop' : '' ?>">
    <?php if (isset($_SESSION['identity'])): ?>
        <h1>Make your order</h1> <br>

        <?php if (empty($carshop)): ?>
            <p>Your cart is empty.</p>
            <a href="/" class="button button-start-buy alert_green">Start Shopping!</a>
        <?php else: ?>
            <a href="/carshop/index" class="buy">Go to products and prices</a>

            <div id="table-container">
                <table aria-label="Order summary">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Units</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($carshop as $item): $product = $item['product']; ?>
                        <tr>
                            <td><?= htmlspecialchars($product->name) ?></td>
                            <td class="price"><?= htmlspecialchars($product->price) ?> USD</td>
                            <td><?= $item['units'] ?></td>
                            <td class="price"><?= htmlspecialchars($product->price * $item['units']) ?> USD</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td><strong>$<?= htmlspecialchars(Utils::statsCarShop()['total']) ?> USD</strong></td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <form action="/order/add" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
                <label for="state">State</label>
                <input type="text" id="state" name="state" required>
                <label for="city">City</label>
                <input type="text" id="city" name="city">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>

                <input type="submit" value="Order">
            </form>
        <?php endif; ?>

    <?php else: ?>
        <h1>Log in to make your order</h1>
    <?php endif; ?>
</div>