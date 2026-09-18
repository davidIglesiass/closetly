<div id="manage-container" class="m-users">
    <h1>Manage Users</h1>

    <?php if(isset($_SESSION['usersaved'])) : ?>
        <div class="alert alert_green"><?= htmlspecialchars($_SESSION['usersaved']) ?></div>
    <?php elseif(isset($_SESSION['userunsaved'])) : ?>
        <div class="alert alert_red"><?= htmlspecialchars($_SESSION['userunsaved']) ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION['userupdated'])) : ?>
        <div class="alert alert_green"><?= htmlspecialchars($_SESSION['userupdated']) ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION['userdeleted'])) : ?>
        <div class="alert alert_green"><?= htmlspecialchars($_SESSION['userdeleted']) ?></div>
    <?php elseif(isset($_SESSION['userundeleted'])) : ?>
        <div class="alert alert_red"><?= htmlspecialchars($_SESSION['userundeleted']) ?></div>
    <?php endif; ?>
    <?php
    Utils::deleteSession('usersaved'); Utils::deleteSession('userunsaved'); Utils::deleteSession('userupdated');
    Utils::deleteSession('userdeleted'); Utils::deleteSession('userundeleted');
    $roles = ['user' => 'Customer', 'vendedor' => 'Seller', 'admin' => 'Admin'];
    ?>

    <a href="/user/adminCreate" class="button" aria-label="Create new user" title="Create new user"><?= Utils::icon('plus') ?></a>

    <div id="table-container">
        <table aria-label="Users management">
            <thead>
            <tr>
                <th>ID</th>
                <th>NAME</th>
                <th>EMAIL</th>
                <th>ROLE</th>
                <th>ACTIONS</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?= $user->id ?></td>
                    <td><?= htmlspecialchars($user->name) ?></td>
                    <td><?= htmlspecialchars($user->email) ?></td>
                    <td><?= htmlspecialchars($roles[$user->rol] ?? $user->rol) ?></td>
                    <td>
                        <a href="/user/edit&id=<?= $user->id ?>" class="alert alert_green" aria-label="Edit" title="Edit"><?= Utils::icon('edit') ?></a>
                        <?php if ($user->id != $_SESSION['identity']['id']) : ?>
                            <form action="/user/delete" method="POST" onsubmit="return confirm('Are you sure?')">
                                <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
                                <input type="hidden" name="id" value="<?= $user->id ?>">
                                <button type="submit" class="alert alert_red" aria-label="Delete" title="Delete"><?= Utils::icon('trash') ?></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php require 'views/partials/_pagination.php'; ?>
</div>
