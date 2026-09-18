<div id="manage-container" class="m-products">
    <h1>Manage Products</h1>
    <?php if(isset($_SESSION['productsaved'])) : ?>
        <div class="alert alert_green"><?= htmlspecialchars($_SESSION['productsaved']) ?></div>
    <?php elseif(isset($_SESSION['productunsaved'])) : ?>
        <div class="alert alert_red"><?= htmlspecialchars($_SESSION['productunsaved']) ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION['productdeleted'])) : ?>
        <div class="alert alert_green"><?= htmlspecialchars($_SESSION['productdeleted']) ?></div>
    <?php elseif(isset($_SESSION['productundeleted'])) : ?>
        <div class="alert alert_red"><?= htmlspecialchars($_SESSION['productundeleted']) ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION['productupdated'])) : ?>
        <div class="alert alert_green"><?= htmlspecialchars($_SESSION['productupdated']) ?></div>
    <?php endif; ?>
    <?php
    Utils::deleteSession('productsaved'); Utils::deleteSession('productunsaved'); Utils::deleteSession('productdeleted'); Utils::deleteSession('productundeleted'); Utils::deleteSession('productupdated');
    ?>
    <a href="/product/create" class="button" aria-label="Create new product" title="Create new product"><?= Utils::icon('plus') ?></a>
    <div id="table-container">
        <table aria-label="Products management">
            <thead>
            <tr>
                <th>ID</th>
                <th>NAME</th>
                <th>PRICE</th>
                <th>STOCK</th>
                <?php if ($isAdmin) : ?><th>SELLER</th><?php endif; ?>
                <th>ACTIONS</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?= $product->id ?></td>
                    <td><?= htmlspecialchars($product->name) ?></td>
                    <td class="price"><?= htmlspecialchars($product->price) ?> USD</td>
                    <td><?= $product->stock ?></td>
                    <?php if ($isAdmin) : ?><td><?= htmlspecialchars($product->sellerName ?? 'Store') ?></td><?php endif; ?>
                    <td>
                        <a href="/product/show&id=<?= $product->id ?>" class="alert" aria-label="View" title="View"><?= Utils::icon('eye') ?></a>
                        <a href="/product/update&id=<?= $product->id ?>" class="alert alert_green" aria-label="Edit" title="Edit"><?= Utils::icon('edit') ?></a>
                        <form action="/product/delete" method="POST" onsubmit="return confirm('Are you sure?')">
                            <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $product->id ?>">
                            <button type="submit" class="alert alert_red" aria-label="Delete" title="Delete"><?= Utils::icon('trash') ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php require 'views/partials/_pagination.php'; ?>
</div>