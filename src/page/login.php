<?php
    require __DIR__.'/../theme/header.php';
?>
<style>
    .login-form{
        margin: 50px auto;
        border: 1px solid lightgrey;
        border-radius: 10px;
        width: 40%;
        padding: 20px;
    }
    form{
        width: 95%;
        margin: 10px auto;
        display: flex;
        flex-flow: column;
        row-gap: 1em;
    }
</style>
<div class="login-form">
    <form action="#" method="POST">
        <div>
            <h2>Log In</h2>
            <p class="sm">Don't have an account? Register <a href="/register.php" class="bluetext">here</a></p>
        </div>
        <section>
            <label for="loginid">Username or Email</label>
            <input type="text" name="loginid" id="" placeholder="Enter your username or email address..." required>
        </section>
        <section>
            <label for="loginid">Password</label>
            <input type="password" name="password" id="" placeholder="Enter your password" required>
        </section>
        <button type="submit" class="blue">Login</button>
    </form>
</div>
<?php
    require ROOTDIR.'theme/footer.php';
?>