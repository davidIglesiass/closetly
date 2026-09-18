<?php
$isEdit = isset($edit) && isset($user);
if ($isEdit) {
    $nameParts = explode(' ', $user->name, 2);
    $firstname = $nameParts[0];
    $lastname = $nameParts[1] ?? '';
}
?>
<div id="register-container">
    <h1><?= $isEdit ? 'Edit User' : 'Create User' ?></h1>

    <form action="/user/adminSave<?= $isEdit ? '&id='.$user->id : '' ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
        <label for="firstname">First name</label>
        <input type="text" id="firstname" name="firstname" value="<?= $isEdit ? htmlspecialchars($firstname) : '' ?>" required>

        <label for="lastname">Last name</label>
        <input type="text" id="lastname" name="lastname" value="<?= $isEdit ? htmlspecialchars($lastname) : '' ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= $isEdit ? htmlspecialchars($user->email) : '' ?>" required>

        <label for="password">Password<?= $isEdit ? ' (leave blank to keep current)' : '' ?></label>
        <input type="password" id="password" name="password" <?= $isEdit ? '' : 'required' ?>>

        <label for="rol">Role</label>
        <select id="rol" name="rol" required>
            <option value="user" <?= $isEdit && $user->rol === 'user' ? 'selected' : '' ?>>Customer</option>
            <option value="vendedor" <?= $isEdit && $user->rol === 'vendedor' ? 'selected' : '' ?>>Seller</option>
            <option value="admin" <?= $isEdit && $user->rol === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <input type="submit" value="<?= $isEdit ? 'Update User' : 'Create User' ?>">
    </form>
</div>
