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
        <label for="firstname"> First name</label>
        <input type="text" name="firstname" required>
    
        <label for="lastname"> Last name</label>
        <input type="text" name="lastname" required>
    
        <label for="email"> Email </label>
        <input type="email" name="email" required>
    
        <label for="password"> Password </label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" value="Register">
    </form>
</div>