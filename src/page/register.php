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

        isset($_POST['securityQuestion']) && (empty($_POST['securityQuestion']) || $_POST['securityQuestion'] == 0) ? 
        $error  = 'Please select a security Question.': $question = $_POST['securityQuestion'];

        isset($_POST['securityAnswer']) && empty($_POST['securityAnswer']) ? 
        $error ='Please provide answer to your security question.': $answer = $_POST['securityAnswer'];
        //verify captcha
        isset($_POST['captcha']) && empty($_POST['captcha']) ? 
        $error = 'Invalid captcha!': ($t->validateCaptcha($_POST['captcha']) ? :$error = 'Invalid captcha!');

        if($error == ''){
            $t->strValidate($username,'!@') ? : $error = 'Special characters are not allowed in username';
            $t->strValidate($email,'email') ? : $error = 'Please enter a valid email address.';
            strlen($password) < 8 ? $error = 'Please choose a strong password.<br>Required password length is 8':'';

            $t->query('SELECT USERNAME FROM BBVSUSERTABLE WHERE USERNAME LIKE ? OR EMAIL LIKE ?',[
                [&$username,'s'],
                [&$email,'s']
            ]);
            $t->execute();
            if($t->affected_rows == 1){
                $r = $t->fetch();
                $r['USERNAME'] == $username ? $error = 'Please choose a different username' : $error = "An account already exists with the above email address.";
            } 
            if($error == ''){

                $hashed_password = password_hash($t->addSalt($password), PASSWORD_DEFAULT);
                $hashed_answer = password_hash($answer, PASSWORD_DEFAULT);
                unset($password);
                unset($answer);

                $time = time();
                $values = [
                    [&$name,'s'],
                    [&$username,'s'],
                    [&$email,'s'],
                    [&$hashed_password,'s'],
                    [&$time,'i'],
                    [&$question,'i'],
                    [&$hashed_answer,'s']
                ];
                $result = $t->query('INSERT INTO BBVSUSERTABLE(NAME, USERNAME, EMAIL, PASSWORD, REGDATE, SECURITYQUESTION, SECURITYANSWER) VALUES(?,?,?,?,?,?,?)', $values);
                if(false !== $result){
                    $result = $t->execute();
                    if($t->affected_rows == 1){
                        $_SESSION['username'] = $username;
                        header('Location: dashboard.php');
                    }
                    $t->error($t->dberror, 'Registeration failed!');
                }
                $t->error($t->dberror, 'Registeration failed!');
            }
        }
    }
?>     
<script>
    function isEmpty(element){
        if($('#'+element).val().trim() == ''){
            $('#'+element+'_err').show()
            $('#'+element+'_err').text(element + ' is required')
            return true
        }
        $('#'+element+'_err').hide()
        return false
    }
    $(document).ready(function(){
        error = {
            'username' : false,
            'email' : false,
            'password' : false,
            'Sans' : false,
            'captcha' : false
        }

        $('#username').on('blur',function(){ 
            if(!isEmpty('username')){
                $('p#username_err').show()
                if(!validateString(this.value,'!@')){
                    $('p#username_err').text('spcial charatars are not allowed');
                    return;
                }
                error['username'] = true
                $('p#username_err').hide()
            }
        });

        $('#email').on('blur',function(){
            if(!isEmpty('email')){
                $('p#email_err').show();
                if(!validateString(this.value,'email')){
                    $('p#email_err').text('Please enter a valid email address');
                    return
                }
                error['email'] =true
                $('p#email_err').hide()
            }
        });

        $('#password').on('blur',function(){
            if(!isEmpty('password')){
                $('p#password_err').show();
                if(this.value.length < 8){
                    $('p#password_err').text('Plese enter more then 8 char');
                    return
                }
                error['password'] = true;
                $('p#password_err').hide();  
            }
        });

        $('#sa').on('blur',function(){
            if(!isEmpty('sa')){
                $('p#sa_err').show();
                if(this.value.length < 3){
                    $('p#sa_err').text('Plese enter more then 3 char');
                    return
                }
                error['Sans'] = true;
                $('p#sa_err').hide();  
            }
        });
        
        $('#captcha').on('blur',function(){
            if(!isEmpty('captcha')){
                $('p#captcha_err').show();
                if(this.value.length != 5){
                    $('p#captcha_err').text('Plese enter 5 char');
                    return
                }
                error['captcha'] = true;
                $('p#captcha_err').hide();  
            }
        });

        $('#submit_btn').click(function(){
            $('input').blur();
            if(!(error['username'] && error['email'] && error['password'] && error['Sans'] && error['captcha']))
            {
                $('form').submit((e)=>{e.preventDefault()});
                return false
            }
            return true
        });
  });
  </script>
<style>
    .register-form{
        margin: 30px auto;
        border: 1px solid lightgrey;
        border-radius: 10px;
        width: 50%;
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
    <form
     action="page/register.php" method="POST" id="register">
        <div>
            <h2>Register a new account</h2>
            <p class="sm">Alreadt have an account? Login <a href="page/login.php" class="bluetext">here</a></p>
        </div>
        <section>
            <label for="name" >Name</label>
            <input type="text" name="name" id="name" value="<?php echo $_POST['name'] ?? '' ?>" placeholder="Enter your name" >
            <p id="name_err" class="red sm"></p>
        </section>
        <section>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo $_POST['username'] ?? '' ?>" placeholder="Choose an username" >
            <p id="username_err" class="red sm"></p>
        </section>
        <section>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $_POST['email'] ?? '' ?>" placeholder="Enter your email address..." >
            <p id="email_err" class="red sm"></p>
        </section>
        <section>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" >
            <p id="password_err" class="red sm"></p>
        </section>
        <section>
            <label for="securityQuestion">Choose security question</label>
            <?php
                echo '<select name="securityQuestion" id="">';
                foreach(SETTINGS::securityQuestion as $k=> $q){
                    echo '<option value="'.$k.'">'.$q.'</option>';
                }
                echo '</select>';
            ?>              
            <input type="text" name="securityAnswer" placeholder="Your answer" id="sa">
            <p id="sa_err" class="red"></p>
        </section>
        <section>
            <label for="captcha">Captcha <span class="xs">[Not case-sensitive]</span></label>
            <div class="flexrow">
                <input style="min-width:50%;max-width:60%;" type="text" name="captcha" id="captcha" placeholder="Enter text as shown" >
                <img style="border: 1px solid grey;border-radius:5px;" src="<?php echo $t->getCaptcha();?>">
                <p id="cp_err" class="red sm"></p>
            </div>
        </section>
        <p class="red md"><?php echo $error ?></p>
        <button name="register" type="submit" class="blue" id="submit_btn">Register</button>
    </form>
</div>
<?php
    require ROOTDIR.'theme/footer.php';
?>
