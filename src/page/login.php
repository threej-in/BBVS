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
    .error{
        color:red;
        font-size:14px;
    }
</style>
<div class="login-form">
    <form action="page/login.php" method="POST" id="login">
        <div>
            <h2>Log In</h2>
            <p class="sm">Don't have an account? Register <a href="page/register.php" class="bluetext">here</a></p>
        </div>
        <section>
            <label for="loginid">Username or Email</label>
            <input type="text" name="loginid" id="loginid" placeholder="Enter your username or email address...">
        </section>
        <section>
            <label for="loginid">Password <span style="float: right;color:grey;">Forgot Password?</span></label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
        </section>
        <button type="submit" class="blue">Login</button>
    </form>
</div>
<?php
    require ROOTDIR.'theme/footer.php';
?>