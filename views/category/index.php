<div id="manage-container" class="m-categories">
    <h1>Manage Categories</h1>

    <?php if(isset($_SESSION['categorysaved'])) : ?>
        <div class="alert alert_green"><?= htmlspecialchars($_SESSION['categorysaved']) ?></div>
    <?php elseif(isset($_SESSION['categoryunsaved'])) : ?>
        <div class="alert alert_red"><?= htmlspecialchars($_SESSION['categoryunsaved']) ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION['categorydeleted'])) : ?>
        <div class="alert alert_green"><?= htmlspecialchars($_SESSION['categorydeleted']) ?></div>
    <?php elseif(isset($_SESSION['categoryundeleted'])) : ?>
        <div class="alert alert_red"><?= htmlspecialchars($_SESSION['categoryundeleted']) ?></div>
    <?php endif; ?>
    <?php
    Utils::deleteSession('categorysaved'); Utils::deleteSession('categoryunsaved');
    Utils::deleteSession('categorydeleted'); Utils::deleteSession('categoryundeleted');
    $departments = array_filter($categories, fn($c) => $c->isDepartment());
    ?>

    <form action="/category/save" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
        <label for="department-name">New department (e.g. Men, Women, Children)</label>
        <div class="input-group">
            <input type="text" id="department-name" name="name" placeholder="Department name" required>
            <button type="submit" class="button" aria-label="Create department" title="Create department"><?= Utils::icon('plus') ?></button>
        </div>
    </form>

    <form action="/category/save" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
        <label for="subcategory-name">New subcategory (e.g. T-Shirts, Hoodies)</label>
        <input type="text" id="subcategory-name" name="name" placeholder="Subcategory name" required>
        <label for="subcategory-parent">Department</label>
        <div class="input-group">
            <select id="subcategory-parent" name="parent_id" required>
                <option disabled selected>Select a department</option>
                <?php foreach ($departments as $department) : ?>
                    <option value="<?= $department->id ?>"><?= htmlspecialchars($department->name) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="button" aria-label="Create subcategory" title="Create subcategory"><?= Utils::icon('plus') ?></button>
        </div>
    </form>

    <div id="table-container">
        <table aria-label="Categories list">
            <thead>
            <tr>
                <th>ID</th>
                <th>NAME</th>
                <th>DEPARTMENT</th>
                <th>ACTIONS</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $category) : ?>
                <tr>
                    <td><?= $category->id ?></td>
                    <td><?= htmlspecialchars($category->name) ?><?= $category->isDepartment() ? ' <strong>(department)</strong>' : '' ?></td>
                    <td><?= htmlspecialchars($category->departmentName ?? '—') ?></td>
                    <td>
                        <?php if (!$category->isDepartment()) : ?>
                            <a href="/category/show&id=<?= $category->id ?>" class="alert" aria-label="View" title="View"><?= Utils::icon('eye') ?></a>
                        <?php endif; ?>
                        <form action="/category/delete" method="POST" onsubmit="return confirm('Are you sure?')">
                            <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $category->id ?>">
                            <button type="submit" class="alert alert_red" aria-label="Delete" title="Delete"><?= Utils::icon('trash') ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
