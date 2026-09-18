<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Closetly">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="theme-color" content="#17130f">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/app.css?v=<?= filemtime('assets/css/app.css') ?>">
    <link rel="icon" href="../assets/img/logo.svg">
    <script src="../assets/js/app.js?v=<?= filemtime('assets/js/app.js') ?>" defer></script>
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>

<body>
    <div id="container">
        <!-- HEADER -->
        <header id="header">
            <div id="logo">
                <a href="/"><img src="../assets/img/logo.svg" alt="Closetly logo"> CLOSETLY.</a>
            </div>

            <!-- MENU -->
            <?php $categories = Utils::showCategories(); ?>
            <?php $departments = array_filter($categories, fn($c) => $c->isDepartment()); ?>
            <nav id="menu" aria-label="Main navigation">
                <ul>
                    <li><a href="/">Home</a></li>
                    <?php foreach ($departments as $department) : ?>
                        <?php $subcategories = array_filter($categories, fn($c) => $c->parent_id == $department->id); ?>
                        <li class="has-submenu">
                            <a href="#"><?= htmlspecialchars($department->name) ?></a>
                            <?php if ($subcategories) : ?>
                                <ul class="submenu">
                                    <?php foreach ($subcategories as $subcategory) : ?>
                                        <li><a href="/category/show&id=<?= $subcategory->id ?>"><?= htmlspecialchars($subcategory->name) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </header>
        <div class="info-session">
            <?php if (isset($_SESSION['identity'])) : ?>
                <div id="identity">
                    <p>Welcome, <strong><?= htmlspecialchars(explode(' ', $_SESSION['identity']['name'])[0]) ?></strong></p>
                </div>
            <?php endif; ?>
            <div id="stats">
                <a href="/carshop/index" aria-label="Shopping cart">🛒</a>
                <?php $stats = Utils::statsCarShop(); ?>
                <a href="/carshop/index"><?= $stats['count'] ?></a>
                <a href="/carshop/index">$<?= $stats['total'] ?></a>
            </div>
        </div>
        <!-- MAIN -->
        <div id="main-container">