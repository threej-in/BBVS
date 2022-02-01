<?php
    $result = false;
    $message = 'internal error occured';
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
            break;
            case 'createNewPoll':
                if(!isset($_SESSION['UID'])) return;
                $elements = ['Title','Description','Image'];
                $error = '';
                $result = false;
                // print_r($_FILES);
                foreach(array_reverse($elements) as $k => $el){
                    if(empty($_POST['poll'.$el]) && empty($_FILES['poll'.$el])){
                        $error = $el.' is required';
                    }
                }
                if($error == '' && $_FILES['pollImage']['size'] > 500000){
                    $error = 'Image size is too large.';
                }elseif($error == '' && !in_array($_FILES['pollImage']['type'],['image/jpeg','image/jpg','image/png'])){
                    $error = 'Selected file is not an image';
                }
                if($error == '' && (empty($_POST['option1']) || empty($_POST['option2']))){
                    $error = 'At least two options is required';
                }
                if($error == ''){
                    $time = time();
                    $ext = preg_replace('/.*\./','.',$_FILES['pollImage']['name']);
                    $image = $_SESSION['UID'].$time.$ext;
                    $options = [];
                    foreach($_POST as $k => $v){
                        if(preg_match('/option\d/',$k)){
                            $options[] = $v;
                        }
                    }
                    $options = json_encode($options);
                    $values =[
                        [&$_POST['pollTitle'],'s'],
                        [&$_POST['pollDescription'],'s'],
                        [&$options,'s'],
                        [&$image,'s'],
                        [&$time,'i'],
                        [&$_SESSION['UID'],'i'],
                    ];
                    $t->query('INSERT INTO BBVSPOLLS(POLLNAME, DESCRIPTION, OPTIONS, POLLIMAGE,  CREATEDON, CREATEDBY) VALUES(?,?,?,?,?,?)',$values);
                    if(false !== $t->execute()){
                        $result = true;
                        $error = 'Poll created successfully!';
                        move_uploaded_file($_FILES['pollImage']['tmp_name'],ROOTDIR.'contents/img/pollpic/'.$image);
                    }else{
                        $error = 'Failed to create poll';
                        $t->error($t->dberror,'');
                    }
                }
                echo json_encode(
                    [
                        'result' => $result,
                        'message' => $error
                    ]
                );
                return;
            break;
            case 'modifyPoll':
                if(!isset($_SESSION['UID'])) return;
                if($_POST['action'] == 'publish'){
                    if($_POST['period'] != 0){
                        $time = time();
                        $t->query('UPDATE BBVSPOLLS SET STATUS = 1,PERIOD=?,STARTDATE = ? WHERE PID = ?',[
                            [&$_POST['period'],'i'],
                            [&$time,'i'],
                            [&$_POST['pid'],'i']
                        ]);
                        if(false != $t->execute()){
                            $result = true;
                            $message = 'Poll published!';
                        }
                    }else{
                        $message = 'Invalid period selected.';
                    }
                }elseif($_POST['action'] == 'stop'){
                    $t->query('UPDATE BBVSPOLLS SET STATUS = 0 WHERE PID = ?',[
                        [&$_POST['pid'],'i']
                    ]);
                    if(false != $t->execute()){
                        $result = true;
                        $message = 'Poll stopped!';
                    }
                }
            break;
            case 'newPoll': ?>
                <div class="newPoll">
                    <form action="#" method="POST" id="newPoll">
                        <div>
                            <h2 style="color: var(--grey);">Submit a new poll</h2>
                            <p class="sm">All the fields are required</p>
                        </div>
                        <section>
                            <label for="pollTitle">Title</label>
                            <input type="text" name="pollTitle" placeholder="Poll title" required>
                            <p id="name_err" class="red sm" style="display: none;">Title is required</p>
                        </section>
                        <section>
                            <label for="pollDescription">Description</label>
                            <input type="text" name="pollDescription" placeholder="A short description for your poll" required>
                            <p id="description_err" class="red sm" style="display: none;">Description is required</p>
                        </section>
                        <section>
                            <img src="" alt="" data-id="pollpic" style="display: none;">
                            <label for="">Upload an image for your poll</label>
                            <input type="file" onchange="return validateImage(this,false,'pollpic', 500)" accept=".jpg,.png,.jpeg" name="pollImage" required>
                        </section>
                        <section>
                            <label for="option">Options</label>
                            <input type="text" name="option1" placeholder="Option 1" requried>
                            <input type="text" name="option2" placeholder="Option 2" requried>
                            <button data-no="2" onclick="n=$(this).attr('data-no');n++;$(this).before(`<input type='text' name='option${n}' placeholder='Option ${n}'>`);$(this).attr('data-no',n)" type="button" style="color: var(--dark);"><i class="fa fa-plus"></i> Add more option</button>
                            <p id="email_err" class="red sm"></p>
                        </section>
                        <p class="red md"><?php echo $error ?? '' ?></p>
                        <button name="register" type="button" class="blue" onclick="submitNewPoll()">Create Poll</button>
                    </form>
                </div>
                <?php
                return;
            break;
            case 'profile':
                !isset($_SESSION['username']) ? die:'';
                $t->query('SELECT * FROM BBVSUSERTABLE WHERE USERNAME = ?',[[&$_SESSION['username'],'s']]);
                if(false !== $t->execute()){
                    $user = $t->fetch();
                ?>
                <h2 style="color: var(--grey);">Profile settings</h2><hr>
                <form action="page/dashboard.php" method="POST" enctype="multipart/form-data">
                    <section>
                        <img data-id="profilepic" src="<?php echo empty($user['IMAGE']) ? 'theme/img/boy.jpg' : 'contents/img/profilepic/'.$user['IMAGE'] ?>" alt="" width="150px" height="150px">
                        <div class="flexrow">
                            <label for="profileImage" style="border:1px solid grey;padding:10px 20px;border-radius:5px;width:fit-content;">Change image</label>
                            <input type="file" onchange="validateImage(this)" accept=".jpg,.png,.jpeg" name="profileImage" id="profileImage" style="display:none;">
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
                        <label for="">Username</label>
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
                        <input type="text" name="securityAnswer" placeholder="Your answer" onkeydown="return validateString(event.key,'!@')" required>
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
                $message = 'Failed to remove user';
            break;
            case 'securityQuestion':
                if(!isset($_POST['loginid'])){
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
            case 'showPolls':
                !isset($_SESSION['username']) ? die:'';
                $t->query('SELECT * FROM BBVSPOLLS WHERE CREATEDBY = ?',[[&$_SESSION['UID'],'i']]);
                if(false == $t->execute()){
                    echo '<p class="red">Unable to fetch polls</p>';
                    return;
                }
                ?>
                <style>
                    .active-polls{
                        column-gap: 1em;
                    }
                    .individual-polls{
                        border-radius: 5px;
                        box-shadow: 0 0 1px var(--dark);
                        width: 60%;
                        color: var(--grey);
                        cursor: pointer;
                        background-color: #f1f1f1;
                    }
                    .individual-polls img{
                        width: 100%;
                        height: 200px;
                        object-fit: cover;
                    }
                    .individual-polls .details{
                        position: relative;
                        width: 100%;
                    }
                    .individual-polls .title{
                        position: absolute;
                        bottom: 0;
                        margin: 0 15px;
                        background-color: #ffffffd1;
                        padding: 10px 0;
                        width: 94%;
                        box-shadow: 0px -7px 21px 9px #ffffffd1;
                    }
                    .individual-polls .options{
                        width: 90%;
                        padding: 20px 0;
                    }
                    .individual-polls .status{
                        position: absolute;
                        right: 7px;
                        padding: 0px 10px;
                        color: white;
                        border: 1px solid black;
                        border-radius: 6px;
                        top: 7px;
                    }
                    input{
                        width: fit-content;
                    }
                    input[type=radio]{
                        transform: scale(1.5);
                    }
                </style>
                <div>
                    <div class="flexrow">
                        <h3 style="color: var(--grey);">List of polls created by you</h3>
                    </div>
                    <hr style="height: 20px;">
                    <div class="flexrow flexass active-polls">
                        <?php while($r = $t->fetch()){ ?>
                            <div class="flexcol individual-polls">
                                <div class="flexcol flexass details">
                                    <img src="contents/img/pollpic/<?php echo $r['POLLIMAGE'] ?>" alt="">
                                    <?php echo $r['STATUS'] ? 
                                    '<div class="status" style="background-color: seagreen;">Active</div>' : 
                                    '<div class="status" style="background-color: #d93939;">Not active</div>' ?>
                                    <div class="title">
                                        <h3><?php echo $r['POLLNAME'] ?></h3>
                                        <p class="sm"><?php echo $r['DESCRIPTION'] ?></p>
                                    </div>
                                </div>
                                <div class="flexcol flexass options">
                                    <p class="md" style="color:grey;">Choose your answer</p>
                                    <?php
                                        $options = json_decode($r['OPTIONS']);
                                        foreach($options as $k => $v){
                                            echo 
                                            '<div class="flexrow">
                                                <input type="radio" name="vote" id="">
                                                <label class="md">'.$v.'</label>
                                            </div>';
                                        }
                                    ?>
                                </div>
                                <?php
                                if($r['STATUS']){
                                    $title = urlencode($r['POLLNAME']);
                                    echo '
                                    <button class="blue" style="width: 92%;" onclick="location.href=\'page/created_poll.php?title='.$title.'\'">Cast your vote &nbsp;<i class="fa fa-external-link-alt"></i></button>
                                    <button onclick="modifyPoll(this,\'stop\', '.$r['PID'].')" style="color:white;background-color:#cd1f1f;margin: 0 0 20px 0;width: 92%;border:1px solid red;"><i class="fa fa-ban"></i> Stop Poll</button>';
                                }else{
                                    echo '<select name="votingTime" id="" style="background-color: f1f1f1;width: 92%;">
                                        <option value="0">Select Voting period</option>
                                        <option value="1">One Day</option>
                                        <option value="2">One Week</option>
                                        <option value="3">Two Weeks</option>
                                        <option value="4">Three Weeks</option>
                                        <option value="5">One Month</option>
                                    </select>
                                    <button onclick="modifyPoll(this,\'publish\', '.$r['PID'].')" class="blue" style="margin: 0 0 20px 0;width: 92%;">Publish</button>';
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <hr style="height: 20px;">
                <?php
                return;
            break;
            case 'updateProfile':
                $answer = password_hash($_POST['securityAnswer'], PASSWORD_DEFAULT);
                if(isset($_POST['name'])){
                    if(!$t->strValidate($_POST['name'],'!@') || !$t->strValidate($_POST['securityAnswer'],'!@')){
                        $message = empty($_POST['securityAnswer']) ? 'Security answer could not be empty':'Special characters not allowed!';
                    }else{
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
                        $message = 'Profile updatation failed';
                    }
                }elseif(isset($_POST['updateprofile'])){
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
                $message = 'Failed to update user';
            break;
            case 'uploadImage':
                !isset($_SESSION['username']) ? die:'';

                if(isset($_FILES['file']['name'])){
                    $img = $_FILES['file'];
                    if($img['size'] > 100000){
                        $message = 'Image size should be less then 100kb';
                    }elseif(!in_array($img['type'],['image/jpeg','image/jpg','image/png'])){
                        $message = 'Please select valid image file.';
                    }else{
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
                }
            break;
            case 'userManagement':
                if($_SESSION['role'] !== USERROLE::ADMIN) return;
                
                $t->query('SELECT * FROM BBVSUSERTABLE WHERE USERNAME NOT LIKE ? LIMIT 50',[[&$_SESSION['username'],'s']]);
                $t->execute();
                
                echo '
                    <h2 style="color: var(--grey);text-align:center;">User management</h2>
                    <hr style="height:25px;">
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
    echo json_encode([
        'result' => $result,
        'message' => $message
    ]);
?>