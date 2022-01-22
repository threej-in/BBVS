<?php

    if(isset($_POST['req'])){
        require __DIR__.'/../class/threej.php';
        require __DIR__.'/../class/settings.php';
        // var_dump($t->strValidate('ad','!@'));die;        
        switch($_POST['req']){
            case 'profile':
                !isset($_SESSION['username']) ? die:'';
                $t->query('SELECT * FROM BBVSUSERTABLE WHERE USERNAME = ?',[[&$_SESSION['username'],'s']]);
                if(false !== $t->execute()){
                    $user = $t->fetch();
                ?>
                <form action="page/dashboard.php" method="POST" enctype="multipart/form-data">
                    <section>
                        <img data-id="profilepic" src="<?php echo empty($user['IMAGE']) ? 'theme/img/boy.jpg' : 'contents/img/profilepic/'.$user['IMAGE'] ?>" alt="" width="150px" height="150px">
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
                return;
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
                $answer = password_hash($_POST['securityAnswer'], PASSWORD_DEFAULT);
                if(isset($_POST['name'])){
                    if(!$t->strValidate($_POST['name'],'!@') || !$t->strValidate($_POST['securityAnswer'],'!@')){
                        echo json_encode(
                            [
                                'result' => false,
                                'error' => 'Special characters not allowed!'
                            ]
                        );
                        return;
                    }
                    $question = intval($_POST['securityQuestion']);
                    $t->query(
                        'UPDATE BBVSUSERTABLE SET NAME = ?, SECURITYQUESTION = ?, SECURITYANSWER = ? WHERE USERNAME = ?',
                        [
                            [&$_POST['name'],'s'],
                            [&$question,'i'],
                            [&$answer,'s'],
                            [&$_SESSION['username'],'s']
                        ]
                    );
                    if(false != $t->execute()){
                        echo json_encode(
                            [
                                'result' => true,
                                'message' => 'Profile updated'
                            ]
                        );
                        return;
                    }
                    echo json_encode(
                        [
                            'result' => true,
                            'message' => 'Profile updatation failed'
                        ]
                    );
                    return;
                }
                if(isset($_POST['updateprofile'])){
                    $param = [
                        [&$_POST['securityQuestion'] , 'i'],
                        [&$answer, 's'],
                        [&$_SESSION['username'],'s']
                    ];
                    $t->query('UPDATE BBVSUSERTABLE SET SECURITYQUESTION = ?, SECURITYANSWER = ? WHERE USERNAME = ?', $param);
                    $t->execute();
                    return;
                }
            break;
            case 'uploadImage':
                !isset($_SESSION['username']) ? die:'';

                if(isset($_FILES['file']['name'])){
                    $img = $_FILES['file'];
                    if($img['size'] > 100000){
                        echo json_encode(
                            [
                                'result'=>false,
                                'error'=>'Image size should be less then 100kb'
                            ]
                        );
                        return;
                    }
                    if(!in_array($img['type'],['image/jpeg','image/jpg','image/png'])){
                        echo json_encode(
                            [
                                'result'=>false,
                                'error'=>'Please select valid image file.'
                            ]
                        );
                        return;
                    }
                    $ext = preg_replace('/.*\./','',$img['name']);
                    $imgname = $_SESSION['username'].".$ext";
                    if(move_uploaded_file(
                        $img['tmp_name'],
                        ROOTDIR.'/contents/img/profilepic/'.$imgname
                    )){
                        $t->query('UPDATE BBVSUSERTABLE SET IMAGE = ? WHERE USERNAME = ?',[
                            [&$imgname,'s'],
                            [&$_SESSION['username'],'s']
                        ]);
                        if(false != $t->execute()){
                            echo json_encode(
                                [
                                    'result'=>true,
                                    'message'=>'Image uploded successfully'
                                ]
                            );  
                            return;
                        }
                        
                    }   
                }
                echo json_encode(['result'=>false, 'error'=>'internal error occured']);
            break;
        }
    }
    echo 'false';
?>