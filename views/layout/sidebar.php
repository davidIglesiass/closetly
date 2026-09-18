<?php
$onRegisterPage = ($_GET['controller'] ?? '') === 'user' && ($_GET['action'] ?? '') === 'create';
$showLoginPanel = !$onRegisterPage || isset($_SESSION['identity']);

$showCategoryFilter = (($_GET['controller'] ?? 'product') === 'product' && ($_GET['action'] ?? 'index') === 'index')
    || (($_GET['controller'] ?? '') === 'category' && ($_GET['action'] ?? '') === 'show');
?>
<?php if ($showLoginPanel || $showCategoryFilter) : ?>
<div id="aside-container">
    <?php if ($showCategoryFilter) : ?>
    <aside id="category-filter" aria-label="Filter by category">
        <div class="aside-block-container">
            <h2>Shop by Category</h2>
            <ul class="category-filter-list">
                <?php foreach ($departments as $department) : ?>
                    <?php $subcategories = array_filter($categories, fn($c) => $c->parent_id == $department->id); ?>
                    <li>
                        <span class="category-filter-department"><?= htmlspecialchars($department->name) ?></span>
                        <?php if ($subcategories) : ?>
                        <ul>
                            <?php foreach ($subcategories as $subcategory) : ?>
                                <li><a href="/category/show&id=<?= $subcategory->id ?>" class="<?= (isset($_GET['id']) && $_GET['id'] == $subcategory->id) ? 'active' : '' ?>"><?= htmlspecialchars($subcategory->name) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>
    <?php endif; ?>
    <?php if ($showLoginPanel) : ?>
    <!-- ASIDE -->
    <aside id="aside-block" class="<?= isset($_SESSION['identity']) ? '' : 'guest-login' ?>">
        <div id="login" class="aside-block-container">
                <?php if (!isset($_SESSION['identity'])) : ?>
                <h2>Login</h2>
                <?php if (isset($_SESSION['loginfailed']) && $_SESSION['loginfailed'] == 'unable to login'): ?>
                    <strong class="alert alert_red">Email or password incorrect</strong>
                <?php endif; ?>
                <form action="/user/login" method="post">
                    <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <input type="submit" value="Login">
                </form>
                <br>
                <br>
                <?php endif; ?>
                <div class="aside-menu">
                <?php if (isset($_SESSION['admin'])) : ?>
                <a href="/category/index">Manage Categories</a>
                <a href="/product/manage">Manage Products</a>
                <a href="/order/manage">Manage Orders</a>
                <a href="/user/manage">Manage Users</a>
                <?php elseif (($_SESSION['identity']['rol'] ?? null) === 'vendedor') : ?>
                <a href="/product/manage">Manage Products</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['identity'])) : ?>
                <a href="/order/myorders">My orders</a>
                <a href="/user/logout">Logout</a>
                <?php else: ?>
                <a href="/user/create">Or register now!!</a>
                <?php endif; ?>
                </div>

            </div>
    </aside>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php Utils::deleteSession('loginfailed'); ?>