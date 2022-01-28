<?php
    require __DIR__.'/../theme/header.php';
    if(isset($_SESSION['username'])){
        header('Location: dashboard.php');        
    }
    $error = '';
    if(isset($_POST['login']) || isset($_POST['resetPassword'])){
        isset($_POST['loginid']) && empty($_POST['loginid']) ? 
        $error = 'Username or Email is required!': $loginid = $_POST['loginid'];

        isset($_POST['password']) && empty($_POST['password']) ? 
        $error = 'Password is required!': $password = $_POST['password'];
        (empty($error) && strlen($password) < 8) ? $error = 'Password must contain atleast 8 characters.':'';
        //verify captcha
        isset($_POST['captcha']) && empty($_POST['captcha']) ? 
        $error = 'Invalid captcha!': ($t->validateCaptcha($_POST['captcha']) ? :$error = 'Invalid captcha!');
    }
    if(isset($_POST['login'])){
        if($error == ''){
            $values = [
                [&$loginid,'s']
            ];
            if($t->strValidate($loginid, 'email')){
                $result = $t->query('SELECT USERNAME,PASSWORD,ROLE from BBVSUSERTABLE WHERE EMAIL = ?', $values);
            }else{
                $result = $t->query('SELECT USERNAME,PASSWORD,ROLE from BBVSUSERTABLE WHERE USERNAME = ?', $values);
            }
            if(false !== $result){
                $t->execute();
                if($t->affected_rows == 1){
                    $result = $t->fetch();
                    if(password_verify($t->addSalt($password),$result['PASSWORD'])){
                        $_SESSION['username'] = $result['USERNAME'];
                        $_SESSION['role'] = $result['ROLE'];
                        header('Location: dashboard.php');
                    }
                }
            }
            $error ='Login failed! Invalid credentials';
        }
    }
    $passResetForm = 0;
    isset($_GET['req']) && $_GET['req'] == 'reset-password' ? $passResetForm = 1 : 0;

    if(isset($_POST['resetPassword'])){
        isset($_POST['answer']) && empty($_POST['answer']) ? 
        $error = 'Please provide answer to your security question.': $answer = $_POST['answer'];
        if($error == ''){
            $t->query('SELECT SECURITYANSWER FROM BBVSUSERTABLE WHERE USERNAME LIKE ? OR EMAIL LIKE ?',[
                [&$loginid,'s'],
                [&$loginid,'s']
            ]);
            if(false !== $t->execute() && $t->affected_rows == 1){
                if(password_verify($answer,$t->fetch()['SECURITYANSWER'])){
                    $hashedp = password_hash($t->addSalt($password),PASSWORD_DEFAULT);
                    $t->query('UPDATE BBVSUSERTABLE SET PASSWORD = ? WHERE USERNAME LIKE ? OR EMAIL LIKE ?',[
                        [&$hashedp,'s'],
                        [&$loginid,'s'],
                        [&$loginid,'s']
                    ]);
                    if(false != $t->execute() && $t->affected_rows == 1){
                        $success = 'Password reset was succesfull.<br> Now you can <a href="page/login.php">login</a> with your new password.';
                    }
                }else{
                    $error = 'Answer to the security question is not valid.';    
                }
            }
            $t->error($t->dberror, 'Password updation failed');
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
<script>
    function fetchdata(e){
        if(e.value == ''){
            $('#error').text('Login id cannot be empty');
            return;
        }
        $.post(
            'ajax/user.php',
            {
                req : 'securityQuestion',
                loginid : e.value
            },
            (result)=>{
                console.log(result);
                if(result != false){
                    $('#error').text('')
                    $('[class=hide]').removeClass('hide');
                    result = $.parseJSON(result)
                    $('label[for=question]').text(result.question)
                }else{
                    $('#error').text('Login id not found');
                }
            }
        )
    }
</script>
<div class="login-form">
    <?php if(isset($success)){
            echo '<h5 class="green">'.$success.'</h5>';
        }else{
    ?>
        <form action="page/login.php<?php echo $passResetForm ? '?req=reset-password' :'' ?>" method="POST" id="login">
            <div>
                <?php 
                    echo $passResetForm 
                    ?
                        '<h2>Reset Password</h2>' 
                    :
                        '<h2>Log In</h2>
                        <p class="sm">Don\'t have an account? Register <a href="page/register.php" class="bluetext">here</a></p>'
                    ;
                ?>
            </div>
            <section>
                <label for="loginid"><?php echo $passResetForm ? 'Enter your email or username':'Username or Email'?></label>
                <input type="text" <?php echo $passResetForm ? 'onblur="fetchdata(this)"':0;?> name="loginid" id="loginid" value="<?php echo $_POST['loginid'] ?? '' ?>" placeholder="Enter your login Id">
            </section>
        <?php 
            echo $passResetForm 
            ?
                '<section class="hide">
                    <label for="question"></label>
                    <input type="text" name="answer" id="answer" placeholder="Enter your answer" required>
                </section>
                <section class="hide">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter new password" required>
                </section>'
            :
                '<section>
                    <label for="password">Password <span style="float: right;color:grey;"><a href="page/login.php?req=reset-password">Forgot Password?</a></span></label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </section>'
            ;
        ?>
            
            <section>
                <label for="captcha">Captcha <span class="xs">[Not case-sensitive]</span></label>
                <div class="flexrow">
                    <input style="min-width:50%;max-width:60%;" type="text" name="captcha" id="captcha" placeholder="Enter text as shown in image" required>
                    <img style="border: 1px solid grey;border-radius:5px;" src="<?php echo $t->getCaptcha();?>">
                </div>
            </section>
            <p class="red sm" id="error"><?php echo $error ?></p>
            <button type="submit" name="<?php echo $passResetForm ?'resetPassword':'login' ?>" class="blue"><?php echo $passResetForm ?'Reset Password':'Login' ?></button>
        </form>
    <?php }?>
</div>
<?php
    require ROOTDIR.'theme/footer.php';
?>