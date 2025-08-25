<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Flux</title>
    <link rel="stylesheet" href="../assets/css/app.css">
    <link rel="icon" href="../assets/img/logo.png">
</head>

<body>
    <div id="container">
        <!-- HEADER -->
        <header id="header">
            <div id="logo">
                <a href="index.php"><img src="../assets/img/logo.png" alt="logo-tshirt"></a>
            </div>

            <!-- MENU -->
            <?php $categories = Utils::showCategories(); ?>
            <nav id="menu">
                <ul>
                    <li><a href="/">Home</a></li>
                    <?php foreach ($categories as $category) : ?>
                        <li><a href="/category/show&id=<?= $category->id ?>"><?= $category->name ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </header>
        <div class="info-session">
            <?php if (isset($_SESSION['identity'])) : ?>
                <div id="identity">
                    <p>Welcome, <strong><?= explode(' ', $_SESSION['identity']['name'])[0] ?></strong></p>
                </div>
            <?php endif; ?>
            <div id="stats">
                <a href="/carshop/index">🛒</a>
                <?php $stats = Utils::statsCarShop(); ?>
                <a href="/carshop/index"><?= $stats['count'] ?></a>
                <a href="/carshop/index">$<?= $stats['total'] ?></a>
            </div>
        </div>
        <!-- MAIN -->
        <div id="main-container">