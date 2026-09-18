<div id="register-container">
    <h1>Register</h1>

    <?php if(isset($_SESSION['registersaved']) && $_SESSION['registersaved'] == 'completed successfully'): ?>
        <strong class="alert alert_green">Registration completed successfully</strong>
    <?php elseif(isset($_SESSION['registerfailed']) && $_SESSION['registerfailed'] == 'unable to save'): ?>
        <strong class="alert alert_red">Registration failed</strong>
    <?php elseif(isset($_SESSION['registerfailed']) && $_SESSION['registerfailed'] == 'Please fill all the fields'): ?>
        <strong class="alert alert_red">Please fill all the fields</strong>
    <?php endif; ?>

    <?php Utils::deleteSession('registerfailed'); ?>
    <?php Utils::deleteSession('registersaved'); ?>

    <form action="/user/save" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Utils::generateCsrfToken() ?>">
        <label for="firstname">First name</label>
        <input type="text" id="firstname" name="firstname" required>

        <label for="lastname">Last name</label>
        <input type="text" id="lastname" name="lastname" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Register">
    </form>
</div>