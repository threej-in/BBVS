<?php
    require __DIR__.'/../theme/header.php';
    if(isset($_SESSION['username'])){
        header('Location: dashboard.php');        
    }
    $error = '';
    if(isset($_POST['register'])){
        $name = $_POST['name'] ?? '';
        isset($_POST['username']) && empty($_POST['username']) ? 
        $error = 'Username is required!': $username = $_POST['username'];

        isset($_POST['email']) && empty($_POST['email']) ? 
        $error = 'Email is required!': $email = $_POST['email'];

        isset($_POST['password']) && empty($_POST['password']) ? 
        $error = 'Password is required!': $password = $_POST['password'];
        //verify captcha
        isset($_POST['captcha']) && empty($_POST['captcha']) ? 
        $error = 'Invalid captcha!': ($t->validateCaptcha($_POST['captcha']) ? :$error = 'Invalid captcha!');

        if($error == ''){
            $t->strValidate($username,'','@') ? : $error = 'Special characters are not allowed in username';
            $t->strValidate($email,'email') ? : $error = 'Please enter a valid email address.';            
            strlen($password) < 8 ? $error = 'Please choose a strong password.<br>Required password length is 8':'';
            if($error == ''){
                $hashed_password = password_hash($t->addSalt($password), PASSWORD_DEFAULT);
                unset($password);
                $time = time();
                $values = [
                    [&$name,'s'],
                    [&$username,'s'],
                    [&$email,'s'],
                    [&$hashed_password,'s'],
                    [&$time,'i'],
                ];
                $result = $t->query('INSERT INTO BBVSUSERTABLE(NAME, USERNAME, EMAIL, PASSWORD, REGDATE) VALUES(?,?,?,?,?)', $values);
                if(false !== $result){
                    $result = $t->execute();
                    if($t->affected_rows == 1){
                        $_SESSION['username'] = $username;
                        header('Location: dashboard.php');
                    }
                    $t->error($t->dberror, 'Registeration failed!');
                }
                
            }
        }
    }
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
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo $_POST['name'] ?? '' ?>" placeholder="Enter your name" >
        </section>
        <section>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo $_POST['username'] ?? '' ?>" placeholder="Choose an username" >
        </section>
        <section>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $_POST['email'] ?? '' ?>" placeholder="Enter your email address..." >
        </section>
        <section>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" >
        </section>
        <section>
            <label for="captcha">Captcha</label>
            <div class="flexrow">
                <input style="min-width:50%;max-width:60%;" type="text" name="captcha" id="captcha" placeholder="Enter text as shown" >
                <img style="border: 1px solid grey;border-radius:5px;" src="<?php echo $t->getCaptcha();?>">
            </div>
        </section>
        <p class="red sm"><?php echo $error ?></p>
        <button name="register" type="submit" class="blue">Register</button>
        
    </form>
</div>
<?php
    require ROOTDIR.'theme/footer.php';
?>