<?php
    require __DIR__.'/../theme/header.php';
?>
<style>
    .register-form{
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
<div class="register-form">
    <form action="page/register.php" method="POST" id="register">
        <div>
            <h2>Register a new account</h2>
            <p class="sm">Alreadt have an account? Login <a href="page/login.php" class="bluetext">here</a></p>
        </div>
        <section>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Choose an username">
        </section>
        <section>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email address...">
        </section>
        <section>
            <label for="username">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
        </section>
        <section>
            <label for="username">Repeat Password</label>
            <input type="password" name="password" id="password" placeholder="Repeat your password" required>
        </section>
        <button type="submit" class="blue">Register</button>
    </form>
</div>
<?php
    require ROOTDIR.'theme/footer.php';
?>