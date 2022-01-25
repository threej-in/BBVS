<?php

    if(isset($_POST['req'])){
        require __DIR__.'/../class/threej.php';
        require __DIR__.'/../class/settings.php';
        // var_dump($t->strValidate('ad','!@'));die;        
        switch($_POST['req']){
            case 'deleteAccount':
                $t->query('UPDATE `bbvsusertable` SET `NAME`=null,`USERNAME`=null,`EMAIL`=null,`PASSWORD`=null,`STATUS`=null,`ROLE`=null,`REGDATE`=null,`SECURITYQUESTION`=null,`SECURITYANSWER`=null,`IMAGE`=null WHERE username = ?',[
                    [&$_SESSION['username'],'s']
                ]);
                if(false !== $t->execute()){
                    session_unset();
                    echo json_encode([
                        'result' => true
                    ]);
                    return;
                }
                echo json_encode([
                    'result' => false
                ]);
                return;
            break;
            case 'profile':
                !isset($_SESSION['username']) ? die:'';
                $t->query('SELECT * FROM BBVSUSERTABLE WHERE USERNAME = ?',[[&$_SESSION['username'],'s']]);
                if(false !== $t->execute()){
                    $user = $t->fetch();
                ?>
                <h3 style="color: var(--grey);">Profile settings</h3><hr>
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
                    <button style="background-color: #d12525;color:white;" type="button" onclick="deleteAccount(this)" name="removeprofile">Delete account</button>
                </form>
            <?php
                }else{
                    echo '<p class="red">Unable to fetch user details at the movement.</p>';
                }
                return;
            break;
            case 'removeUser':
                if($_SESSION['role'] == USERROLE::ADMIN){
                    $t->query('DELETE FROM BBVSUSERTABLE WHERE UID = ? && (ROLE = ? || ROLE = ?)',
                    [
                        [&$_POST['uid'],'i'],
                        [USERROLE::MODERATOR,'i'],
                        [USERROLE::USER,'i']
                    ]);
                    if(false !== $t->execute() && $t->affected_rows == 1){
                        echo json_encode([
                            'result' => true,
                            'message' => 'User removed'
                        ]);
                        return;
                    }
                }
                echo json_encode([
                    'result' => false,
                    'message' => 'Failed to remove user'
                ]);
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
            case 'updateUser':
                if($_SESSION['role'] == USERROLE::ADMIN){
                    $t->query('UPDATE BBVSUSERTABLE SET STATUS = ?, ROLE = ? WHERE UID = ? AND ROLE <> ?',
                    [
                        [&$_POST['status'],'i'],
                        [&$_POST['role'],'i'],
                        [&$_POST['uid'],'i'],
                        [USERROLE::ADMIN,'i'],
                    ]);
                    if(false !== $t->execute() && $t->affected_rows == 1){
                        echo json_encode([
                            'result' => true,
                            'message' => 'User role, status updated successfuly'
                        ]);
                        return;
                    }
                }
                echo json_encode([
                    'result' => false,
                    'message' => 'Failed to update user'
                ]);
                return;
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
            case 'userManagement':
                if($_SESSION['role'] !== USERROLE::ADMIN) return;
                
                $t->query('SELECT * FROM BBVSUSERTABLE WHERE USERNAME NOT LIKE ? LIMIT 50',[[&$_SESSION['username'],'s']]);
                $t->execute();
                
                echo '
                    <h3 style="color: var(--grey);text-align:center;">User management</h3><hr style="height:25px;">
                    <ul class="info">
                        <li>You can promote users and moderators</li>
                        <li>You can remove users and moderators but not other admins</li>
                        <li>You can demote moderators but they cannot demote other admins</li>
                        <li>You can change status of users and moderators</li>
                    </ul>
                    <hr>
                    <table>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th>Reg Date</th>
                            <th>Action</th>
                        </tr>';
                $i=1;
                $userRole = [
                    2 => 'Admin',
                    3 => 'Moderator',
                    4 => 'User'
                ];
                $userStatus = [
                    2 => 'Active',
                    3 => 'On hold',
                    4 => 'Banned'
                ];
                while($r = $t->fetch()){
                ?>
                    <tr>
                        <td><?php echo $i++ ?></td>
                        <td><?php echo $r['NAME'] ?></td>
                        <td>@<?php echo $r['USERNAME'] ?></td>
                        <td>
                            <select name="status">
                                <?php
                                    foreach($userStatus as $k => $v){
                                        if($r['STATUS'] == $k)
                                            echo '<option value="'.$k.'" selected>'.$v.'</option>';
                                        else
                                            echo '<option value="'.$k.'">'.$v.'</option>';
                                    }
                                ?>

                            </select>
                        </td>
                        <td>
                            <select name="role">
                                <?php
                                    foreach($userRole as $k => $v){
                                        if($r['ROLE'] == $k)
                                            echo '<option value="'.$k.'" selected>'.$v.'</option>';
                                        else
                                            echo '<option value="'.$k.'">'.$v.'</option>';
                                    }
                                ?>

                            </select>
                        </td>
                        <td><?php echo date('d M Y',$r['REGDATE']) ?></td>
                        <td>
                            <button style="color: seagreen;" onclick="updateUser(this)" data-uid="<?php echo $r['UID']?>">Update</button>
                            <button onclick="removeUser(this)" data-uid="<?php echo $r['UID']?>">Remove</button>
                        </td>
                    </tr>
                    
                <?php
                }
                echo '</table>';
                return;
            break;
            
        }
    }
    echo 'false';
?>