<?php
    if(isset($_POST['req'])){
        require __DIR__.'/../class/threej.php';
        require __DIR__.'/../class/settings.php';
        switch($_POST['req']){
            case 'profile':
                $t->query('SELECT * FROM BBVSUSERTABLE WHERE USERNAME = ?',[[&$_SESSION['username'],'s']]);
                if(false !== $t->execute()){
                    $user = $t->fetch();
                ?>
                <form action="page/dashboard.php" method="POST" enctype="multipart/form-data">
                    <section>
                        <img data-id="profilepic" src="<?php echo empty($user['IMAGE']) ? 'theme/img/boy.jpg' : $user['IMAGE'] ?>" alt="" width="150px" height="150px">
                        <div class="flexrow">
                            <label for="profileImage" style="border:1px solid grey;padding:10px 20px;border-radius:5px;width:fit-content;">Change image</label>
                            <input type="file" onchange="validateImage(this.files)" accept=".jpg,.png,.jpeg" name="profileImage" id="profileImage" style="display:none;">
                            <button type="button" onclick="validateImage('',true)" style="width:fit-content;" class="blue">Upload</button>
                        </div>
                        <p class="message"></p>
                    </section>
                    <section>
                        <label for="fname">Name</label>
                        <input name="fname" type="text" value="<?php echo $user['NAME']?>" onkeydown="return validateString(event.key,'!@')">
                        <p class="message"></p>
                    </section>
                    <section>
                        <label for="">username</label>
                        <input type="text" value="<?php echo $user['USERNAME']?>" disabled>
                        <p class="message"></p>
                    </section>
                    <section>
                        <label for="">Email</label>
                        <input type="text" value="<?php echo $user['EMAIL']?>" disabled>
                        <p class="message"></p>
                    </section>
                    <section>
                        <label for="securityQuestion">Change security question</label>
                        <?php
                            echo '<select name="securityQuestion" id="">';
                            foreach(SETTINGS::securityQuestion as $k=> $q){
                                echo $user['SECURITYQUESTION'] == $k 
                                ?
                                '<option value="'.$k.'" selected>'.$q.'</option>'
                                :
                                '<option value="'.$k.'">'.$q.'</option>'
                                ;
                            }
                            echo '</select>';
                        ?>
                        <input type="text" name="securityAnswer" placeholder="Your answer" onkeydown="return validateString(event.key,'!@')">
                    </section>
                    <button class="blue" type="button" onclick="updateProfile()" name="updateprofile">Update profile</button>
                </form>
            <?php
                }else{
                    echo '<p class="red">Unable to fetch user details at the movement.</p>';
                }
                break;
            case 'securityQuestion':
                if(!isset($_POST['loginid'])){
                    echo false;
                    return;
                }
                $loginid = $_POST['loginid'];
                $t->query('SELECT SECURITYQUESTION FROM BBVSUSERTABLE WHERE USERNAME LIKE ? OR EMAIL LIKE ?',[
                    [&$loginid,'s'],
                    [&$loginid,'s']
                ]);
                if($t->execute() != false && $t->affected_rows == 1){
                    
                    print_r(
                        json_encode(
                            [
                                'question' => SETTINGS::securityQuestion[($t->fetch())['SECURITYQUESTION']]
                            ]
                        )
                    );
                    return;
                }
                echo 'false';
            break;
            case 'updateProfile':
                if(isset($_POST['updateprofile'])){
                    $answer = password_hash($_POST['securityAnswer'], PASSWORD_DEFAULT);
                    $param = [
                        [&$_POST['securityQuestion'] , 'i'],
                        [&$answer, 's'],
                        [&$_SESSION['username'],'s']
                    ];
                    $t->query('UPDATE BBVSUSERTABLE SET SECURITYQUESTION = ?, SECURITYANSWER = ? WHERE USERNAME = ?', $param);
                    $t->execute();
                }
            break;
            case 'uploadImage':
                print_r($_POST);
                print_r($_FILES);
            break;
        }
    }
?>