<?php
    require __DIR__.'/../theme/header.php';
    if(isset($_SESSION['username'])){
        header('Location: dashboard.php');        
    }
    $error = '';
    if($error == '' && isset($_POST['passwordResetLink'])){
        //email validation
        $t->strValidate($_POST['loginid'],'email') ? 
            $loginid = $_POST['loginid'] :
            $error = 'Username or Email is required!';

        //captcha validation
        isset($_POST['captcha']) && empty($_POST['captcha']) ? 
        $error = 'Invalid captcha!': ($t->validateCaptcha($_POST['captcha']) ? :$error = 'Invalid captcha!');
        if($error == ''){
            
            $t->query('SELECT PASSWORD, SECURITYANSWER FROM bbvsusertable WHERE EMAIL LIKE ?;',[[&$loginid,'s']]);
            if(false !== $result = $t->execute() && $t->affected_rows == 1){
                $res = $t->fetch();
                $token = hash("SHA256", $t->addSalt($res['PASSWORD'] . $res['SECURITYANSWER']));
                $link = HOMEURI . "page/login.php?req=reset-password&email=$loginid&token=". $token;
                $body = "
                <h2>Reset your password</h2><br>
                    Hello, we have received a request to reset you password if this was you then please click on the below button or provided link to reset your password.
                    <br><br>
                    If you have not made this request then simply ignore this email.
                    <br><br>
                    <a href=\"$link\">
                        <button style=\"cursor: pointer;background-color: royalblue;color: white;border: none;padding: 10px 75px;border-radius: 4px;\"> Reset password </button>
                    </a>
                    or copy paste the below link to web browser
                    <br>
                    $link
                    <br><br>
                    <hr>
                    &copy; ".date('Y', time())." BBVS
                ";
                if($t->sendMail($loginid, "BBVS - Reset your password", $body)){
                    $success = "We have sent an email with instructions on how to reset your password.";
                }else{
                    $success = "mail sending failed";
                }
            }else{
                $error = "Provided email is not registered with us.";
            }
        }
    }

    if($error == '' && isset($_POST['resetPassword'])){

        //email validation
        $t->strValidate($_POST['loginid'],'email') ? 
            $loginid = $_POST['loginid'] :
            $error = 'Email is required!';

        //password validation
        isset($_POST['password']) && empty($_POST['password']) ? 
            $error = 'Password is required!':
            $password = $_POST['password'];
        (empty($error) && strlen($password) < 8) ?
            $error = 'Password must contain atleast 8 characters.':'';

        isset($_POST['answer']) && empty($_POST['answer']) ? 
        $error = 'Please provide answer to your security question.': $answer = $_POST['answer'];

        if($error == ''){
            $t->query('SELECT PASSWORD, SECURITYANSWER FROM BBVSUSERTABLE WHERE EMAIL LIKE ?',[[&$loginid,'s']]);
            if(false !== $t->execute() && $t->affected_rows == 1){
                $res = $t->fetch();
                //validate security answer
                if(password_verify($answer,$res['SECURITYANSWER'])){
                    //validate token
                    if($_POST['token'] == hash('SHA256', $t->addSalt($res['PASSWORD'] . $res['SECURITYANSWER']))){
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
                        $error = 'Invalid token';
                    }
                }else{
                    $error = 'Answer to the security question is not valid.';
                }
            }
            // $error = 'Password updation failed';
        }
    }

    if($error == '' && isset($_POST['login'])){
        $loginid = $_POST['loginid'] ?? '';

        //password validation
        isset($_POST['password']) && empty($_POST['password']) ? 
            $error = 'Password is required!':
            $password = $_POST['password'];
        (empty($error) && strlen($password) < 8) ?
            $error = 'Password must contain atleast 8 characters.':'';
        
        //captcha validation
        isset($_POST['captcha']) && empty($_POST['captcha']) ? 
            $error = 'Invalid captcha!':
            ($t->validateCaptcha($_POST['captcha']) ? :$error = 'Invalid captcha!');

        if($error == ''){
            $values = [
                [&$loginid,'s']
            ];
            if($t->strValidate($loginid, 'email')){
                $result = $t->query('SELECT UID,USERNAME,PASSWORD,STATUS,ROLE from BBVSUSERTABLE WHERE EMAIL = ?', $values);
            }else{
                $result = $t->query('SELECT UID,USERNAME,PASSWORD,STATUS,ROLE from BBVSUSERTABLE WHERE USERNAME = ?', $values);
            }
            if(false !== $result){
                $t->execute();
                if($t->affected_rows == 1){
                    $result = $t->fetch();
                    if(password_verify($t->addSalt($password),$result['PASSWORD'])){
                        $_SESSION['UID'] = $result['UID'];
                        $_SESSION['username'] = $result['USERNAME'];
                        $_SESSION['role'] = $result['ROLE'];
                        $_SESSION['status'] = $result['STATUS'];
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
<script>
   $(()=>{
       error = {
         captcha: false,
         password: false,
         answer:false   
       };

        $('#captcha').on('blur',function(){
          if(this.value.length != 5){
              $('#captcha_err').text('Invalid captcha');
              $('#captcha_err').show();
              console.log('must 5');
              return error['captcha'] = false;
          }
          error['captcha'] = true;
          $('#captcha_err').hide();
        });

        $('#password').on('blur',function(){
            if(isEmpty('password'))return false;
            error['password'] = true;
            $('#password_err').hide();
        });

        $('#answer').on('blur',function(){
            if(isEmpty('answer'))return false;
            error['answer'] = true;
            $('#answer_err').hide();
        });
        $('#btnsubmit').on('click',()=>{
            $('input').blur();

            if(error['password'] && error['captcha']){
                if(document.getElementById('btnsubmit').name == "password-reset-link"){
                    if(!error['captcha']) return;
                }else if(document.getElementById('btnsubmit').name == "reset-password"){
                    
                    if(error['answer']){
                        $('#error').text('');
                        $('form').submit();
                    }else{
                        $('#error').text('please enter all data ');
                        return;
                    }
                }
                $('#error').text('');
                // $('form').submit();
            }else{
                $('#error').text('please enter all data ');
            }
        });
    });
</script>

<!-- PASSWORD RESET LINK GENERATION FORM -->
<?php if(isset($_GET['req']) && $_GET['req'] == 'password-reset-link'){ ?>
        <div class="login-form" id="passwordResetLink">
            <?php if(isset($success)){
                    echo '<h5 class="green">'.$success.'</h5>';
                }else{
            ?>
                <form action="page/login.php?req=password-reset-link" method="POST" id="login">
                    <div>
                        <h2>Reset Your Password</h2>
                    </div>
                    <section>
                        <label for="loginid">We will send you a password reset link to your email.</label>
                        <input type="email" autocomplete="off" name="loginid" id="loginid" value="" placeholder="Enter your email">
                        <p id='id_err' class="red sm"></p>
                    </section>
                    <section>
                        <label for="captcha">Captcha</label>
                        <div class="flexrow">
                            <input style="min-width:50%;max-width:60%;" type="text" name="captcha" id="captcha" placeholder="Enter text as shown in image" required>
                            <img style="border: 1px solid grey;border-radius:5px;" src="<?php echo $t->getCaptcha();?>">
                            <p id="captcha_err" class='red sm' style="display: none;"></p>
                        </div>
                    </section>
                    <p class="red sm" id="error"><?php echo $error ?></p>
                    <section class="flexrow flexass">
                        <button style="width:60%;display:inline;" id="btnsubmit" type="submit" name="passwordResetLink" class="blue">Send link</button>
                        <a href="page/login.php" class="md" style="padding:15px;color:var(--blue);text-decoration:underline;text-align:center;" id="btnsubmit" type="submit">Return to login</a>
                    </section>
                </form>
            <?php }?>
        </div>
<?php }elseif(isset($_GET['req']) && $_GET['req'] == 'reset-password'){ 
    $email = $_GET['email'] ?? $_POST['loginid'] ?? '';
    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    $sQuestion = -1;
    if($email == '' && !isset($_POST['resetPassword'])){
        echo 'Password reset link expired!';
    }else{
    ?>
        <!-- PASSWORD RESET FORM -->
        <div class="login-form" id="resetPassword">
            <?php if(isset($success)){
                    echo '<h5 class="green">'.$success.'</h5>';
                }else{
                    $t->query('SELECT SECURITYQUESTION FROM BBVSUSERTABLE WHERE EMAIL LIKE ?',[[$email,'s']]);
                    if(false != $t->execute()){
                        $sQuestion = $t->fetch()['SECURITYQUESTION'];
                    }
            ?>
                <form action="page/login.php?req=reset-password" method="POST" id="login">
                    <div>
                        <h2>Reset Your Password</h2>
                    </div>
                    <section>
                        <label for="loginid">Email</label>
                        <input type="text" autocomplete="off" name="loginid" id="loginid" value="<?= $email ?>">
                        <p id='id_err' class="red sm"></p>
                    </section>
                    <section>
                        <input type="hidden" name="token" value="<?= $token ?>">
                        <label for="question"><?= SETTINGS::securityQuestion[$sQuestion] ?></label>
                        <input type="text" name="answer" id="answer" placeholder="Enter your answer" required>
                        <p id="answer_err" class="red sm"></p>
                    </section>
                    <section>
                        <label for="password">New Password</label>
                        <input type="password" name="password" id="password" placehold  er="Enter new password" required>
                        <p id="password_err" class="red sm"></p>
                    </section>
                    <p class="red sm" id="error"><?php echo $error ?></p>
                    <section class="flexrow flexass">
                        <button type="submit" name="resetPassword" class="blue">Reset Password</button>
                    </section>
                </form>
            <?php }?>
        </div>
<?php
    }
    }else{ ?>
<!-- LOGIN FORM -->
        <div class="login-form" id="#loginForm">
            <?php if(isset($success)){
                    echo '<h5 class="green">'.$success.'</h5>';
                }else{
            ?>
                <form action="page/login.php" method="POST" id="login">
                    <div>
                        <h2>Log In</h2>
                        <p class="md">or <a href="page/register.php" class="bluetext" style="margin:0px;">Sign Up</a> for a new account</p>
                    </div>
                    <section>
                        <label for="loginid">Enter username or email.</label>
                        <input type="text" autocomplete="off" name="loginid" id="loginid" value="" placeholder="Enter your login Id">
                        <p id='id_err' class="red sm"></p>
                    </section>
                    <section>
                        <label for="password">Password <span style="float: right;color:grey;"><a href="page/login.php?req=password-reset-link">Forgot Password?</a></span></label>
                        <input type="password" name="password" id="password" placeholder="Enter your password" required>
                        <p id="password_err" class="red sm"></p>
                    </section>
                    <section>
                        <label for="captcha">Captcha</label>
                        <div class="flexrow">
                            <input style="min-width:50%;max-width:60%;" type="text" name="captcha" id="captcha" placeholder="Enter text as shown in image" required>
                            <img style="border: 1px solid grey;border-radius:5px;" src="<?php echo $t->getCaptcha();?>">
                            <p id="captcha_err" class='red sm' style="display: none;"></p>
                        </div>
                    </section>
                    <p class="red sm" id="error"><?php echo $error ?></p>
                    <section class="flexrow flexass">
                        <button type="submit" name="login" class="blue">Login</button>
                    </section>
                </form>
            <?php }?>
        </div>
<?php } ?>

<?php
    require ROOTDIR.'theme/footer.php';
?>