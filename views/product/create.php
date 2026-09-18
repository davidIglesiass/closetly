<div id="register-container">
    <?php if (isset($edit) && isset($getOneProduct)): ?>
        <h1>Edit Product</h1>
        <?php $url = '/product/save&id='.$getOneProduct->id; ?>
    <?php else: ?>
        <h1>Create Product</h1>
        <?php $url = '/product/save'; ?>
    <?php endif; ?>
    <form action="<?=$url?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php if (isset($edit) && isset($getOneProduct)) echo htmlspecialchars($getOneProduct->name); ?>" required>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php if (isset($edit) && isset($getOneProduct)) echo htmlspecialchars($getOneProduct->price); ?>" required>

        <label for="stock">Stock</label>
        <input type="number" id="stock" name="stock" value="<?php if (isset($edit) && isset($getOneProduct)) echo htmlspecialchars($getOneProduct->stock); ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description"><?php if (isset($edit) && isset($getOneProduct)) echo htmlspecialchars($getOneProduct->description); ?></textarea>

        <label for="category_id">Category</label>
        <?php $departments = array_filter($categories, fn($c) => $c->isDepartment()); ?>
        <select id="category_id" name="category_id" required>
            <option disabled <?= !isset($edit) ? 'selected' : '' ?>>Select a category</option>
            <?php foreach ($departments as $department) : ?>
                <optgroup label="<?= htmlspecialchars($department->name) ?>">
                    <?php foreach ($categories as $category) : ?>
                        <?php if ($category->parent_id == $department->id) : ?>
                            <option <?php if (isset($edit) && isset($getOneProduct) && $getOneProduct->category_id == $category->id) echo 'selected'; ?> value="<?= $category->id ?>"><?= htmlspecialchars($category->name) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>

        <label for="image">Cover Image</label>
        <?php if (isset($edit) && isset($getOneProduct) && $getOneProduct->image): ?>
            <img src="/public/uploads/products/<?= htmlspecialchars($getOneProduct->image) ?>" alt="<?= htmlspecialchars($getOneProduct->name) ?>" class="form-preview-img">
        <?php endif; ?>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">

        <label for="images">Additional Images</label>
        <?php if (isset($edit) && isset($getOneProduct) && !empty($getOneProduct->images)): ?>
            <div class="form-preview-gallery">
                <?php foreach (array_slice($getOneProduct->images, 1) as $extraImage): ?>
                    <img src="/public/uploads/products/<?= htmlspecialchars($extraImage) ?>" alt="" class="form-preview-img">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <input type="file" id="images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>

        <input type="submit" value="<?= (isset($edit) && isset($getOneProduct)) ? 'Update' : 'Create'?>">
    </form>
</div>