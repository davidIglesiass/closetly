<div id="manage-container" class="m-carshop">
    <?php if (!isset($manage)): ?>
        <h1>My Orders</h1>
    <?php else: ?>
        <h1>Manage Orders</h1>
    <?php endif; ?>
    <?php if (!empty($orders)): ?>
        <div id="table-container">
            <table aria-label="Orders list">
                <thead>
                <tr>
                    <th>Order #</th>
                    <th>Price</th>
                    <th>Requested At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order):?>
                    <tr>
                        <td><?= $order->id ?></td>
                        <td class="price">$<?= htmlspecialchars($order->price) ?> USD</td>
                        <td><?= htmlspecialchars($order->created_at) ?></td>
                        <td><?= htmlspecialchars(Utils::showStatus($order->status)) ?></td>
                        <td><a href="/order/show&id=<?= $order->id ?>" class="alert" aria-label="View order" title="View order"><?= Utils::icon('eye') ?></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php require 'views/partials/_pagination.php'; ?>
    <?php else: ?>
        <p>You have no orders yet.</p>
        <a href="/" class="button button-start-buy alert_green">Start Shopping!</a>
    <?php endif; ?>
</div>