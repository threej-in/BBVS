<?php
    require __DIR__.'/../theme/header.php';
    if(isset($_SESSION['username'])){
        header('Location: dashboard.php');        
    }
    $error = '';
    if(isset($_POST['login'])){
        isset($_POST['loginid']) && empty($_POST['loginid']) ? 
        $error = 'Username or Email is required!': $loginid = $_POST['loginid'];

        isset($_POST['password']) && empty($_POST['password']) ? 
        $error = 'Password is required!': $password = $_POST['password'];
        //verify captcha
        isset($_POST['captcha']) && empty($_POST['captcha']) ? 
        $error = 'Invalid captcha!': ($t->validateCaptcha($_POST['captcha']) ? :$error = 'Invalid captcha!');

        strlen($password) < 8 ? $error = 'Password must contain atleast 8 characters.':'';
        if($error == ''){
            
            $values = [
                [&$loginid,'s']
            ];
            if($t->strValidate($loginid, 'email')){
                $result = $t->query('SELECT USERNAME from BBVSUSERTABLE WHERE EMAIL = ? AND PASSWORD = ?', $values);
            }else{
                $result = $t->query('SELECT USERNAME,PASSWORD from BBVSUSERTABLE WHERE USERNAME = ?', $values);
            }
            if(false !== $result){
                $t->execute();
                if($t->affected_rows == 1){
                    $result = $t->fetch();
                    if(password_verify($password,$result['PASSWORD'])){
                        $_SESSION['username'] = $result['USERNAME'];
                        header('Location: dashboard.php');
                    }
                }
            }
            $error ='Login failed! Invalid credentials';
        }
    }
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
            <label for="password">Password <span style="float: right;color:grey;">Forgot Password?</span></label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
        </section>
        <section>
            <label for="captcha">Captcha</label>
            <div class="flexrow">
                <input style="min-width:50%;max-width:60%;" type="text" name="captcha" id="captcha" placeholder="Enter text as shown in image" required>
                <img style="border: 1px solid grey;border-radius:5px;" src="<?php echo $t->getCaptcha();?>">
            </div>
        </section>
        <p class="red sm"><?php echo $error ?></p>
        <button type="submit" name="login" class="blue">Login</button>
    </form>
</div>
<?php
    require ROOTDIR.'theme/footer.php';
?>