<?php
    $result = false;
    $message = 'internal error occured';

    if(isset($_POST['req'])){
        require __DIR__.'/../class/threej.php';
        require __DIR__.'/../class/settings.php';
        // var_dump($t->strValidate('ad','!@'));die;
        $isUserActive = (isset($_SESSION['status']) && $_SESSION['status'] == USERSTATUS::ACTIVE);
        if(!$isUserActive) return print("Please verify your email address to continue. <br>Didn't got your verification email? <a style=\"color:var(--pl);\" href=\"page/register.php?req=new-verification-email\">Click here to request a new email.</a>");

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
                $elements = ['Title','Image'];
                $error = '';
                $result = false;
                // print_r($_FILES);
                if(empty($_POST['pollTitle'])){
                    $error = 'Poll title is required!';
                }
                if($error == '' && empty($_POST['pollDescription'])) $_POST['pollDescription'] = $_POST['pollTitle'];

                if($error == '' && empty($_FILES['pollImage'])){
                    $error = 'Poll Image is required';
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
                    $voteCount = [];
                    foreach($_POST as $k => $v){
                        if(preg_match('/option\d/',$k)){
                            $options[] = $v;
                            $voteCount[] = 0;
                        }
                    }
                    $options = json_encode($options);
                    $voteCount = json_encode($voteCount);
                    $_POST['pid'] = $_POST['pid'] ?? 0;
                    $_POST['txhash'] = $_POST['txhash'] ?? '';
                    $values =[
                        [&$_POST['pollTitle'],'s'],
                        [&$_POST['pollDescription'],'s'],
                        [&$options,'s'],
                        [&$image,'s'],
                        [&$time,'i'],
                        [&$_SESSION['UID'],'i'],
                        [&$voteCount,'s'],
                        [&$_POST['pid'],'i'],
                        [&$_POST['txhash'],'s']
                    ];
                    $t->query('INSERT INTO BBVSPOLLS(POLLNAME, DESCRIPTION, OPTIONS, POLLIMAGE,  CREATEDON, CREATEDBY, VOTECOUNT, BPID, TXHASH) VALUES(?,?,?,?,?,?,?,?,?)',$values);
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
                $_POST['txhash'] = $_POST['txhash'] ?? '';

                if($_POST['action'] == 'publish'){
                    if($_POST['period'] != 0){
                        $time = time();
                        $t->query("UPDATE BBVSPOLLS SET STATUS = 1,PERIOD=?,STARTDATE=?,TXHASH=(CASE WHEN ? = '' THEN TXHASH ELSE ? END) WHERE PID = ?",[
                            [&$_POST['period'],'i'],
                            [&$time,'i'],
                            [&$_POST['txhash'],'s'],
                            [&$_POST['txhash'],'s'],
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
                    $t->query("UPDATE BBVSPOLLS SET STATUS = 0, TXHASH=(CASE WHEN ? = '' THEN TXHASH ELSE ? END) WHERE PID = ?",[
                        [&$_POST['txhash'],'s'],
                        [&$_POST['txhash'],'s'],
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
                            <img src="" alt="" data-id="pollpic" style="display: none;" width="500px">
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
                        <p class="red md"><?= $error ?? '' ?></p>
                        <button name="register" type="button" class="blue" onclick="submitNewPoll(this)">Create Poll</button>
                    </form>
                </div>
                <?php
                return;
            break;
            case 'newComment':
                !isset($_SESSION['username']) ? die('LogIn to post comment'):'';
                if(isset($_POST['comment'])){
                    $t->query('INSERT INTO COMMENTS(COMMENT, PID, CDATE, UID) VALUES(?,?,?,?)',[
                        [&$_POST['comment'],'s'],
                        [&$_POST['pid'],'i'],
                        [time(),'i'],
                        [&$_SESSION['UID'],'i']
                    ]);
                    if(false!== $t->execute()){
                        $result = true;
                        $message = 'Comment added';
                    }
                }
            break;
            case 'pollManagement':
                if($_SESSION['role'] !== USERROLE::ADMIN && $_SESSION['role'] !== USERROLE::MODERATOR) return;
                
                $t->query('SELECT a.*,b.USERNAME FROM `bbvspolls` as a, bbvsusertable as b WHERE a.CREATEDBY = b.UID AND a.STATUS = 1 LIMIT 50');
                $t->execute();
                
                echo '
                    <h2 style="color: var(--grey);text-align:center;">Polls management</h2>
                    <hr style="height:25px;">
                    <ul class="info">
                        <li>You can change status of public polls</li>
                        <li>You can remove duplicate or invalid polls</li>
                    </ul>
                    <hr>
                    <table>
                        <tr>
                            <th>#</th>
                            <th>Poll Name & Description</th>
                            <th>CreatedBy</th>
                            <th>Status</th>
                            <th>Period</th>
                            <th>Start Date</th>
                            <th>Action</th>
                        </tr>';
                $i=1;
                $pollStatus = [
                    'Inactive','Active'
                ];
                $period = [
                    1 => 'One Day',
                    7 => 'One Week',
                    14 => 'Two Week',
                    21 => 'Three Week',
                    30 => 'One Month'
                ];
                while($r = $t->fetch()){
                ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td>
                            <?= '<h4>' .$r['POLLNAME'] .'</h4>
                            <h6>' .$r['DESCRIPTION'] .'</h6>' ?>
                        </td>
                        <td>@<?= $r['USERNAME'] ?></td>
                        <td>
                            <select name="status">
                                <?php
                                    foreach($pollStatus as $k => $v){
                                        if($r['STATUS'] == $k)
                                            echo '<option value="'.$k.'" selected>'.$v.'</option>';
                                        else
                                            echo '<option value="'.$k.'">'.$v.'</option>';
                                    }
                                ?>

                            </select>
                        </td>
                        <td><?= $period[$r['PERIOD']] ?></td>
                        <td><?= date('d M Y',$r['STARTDATE']) ?></td>
                        <td>
                            <button style="color: seagreen;" onclick="updatePoll(this)" data-pid="<?= $r['PID']?>">Update</button>
                            <button onclick="removePoll(this)" data-pid="<?= $r['PID']?>">Remove</button>
                        </td>
                    </tr>
                    
                <?php
                }
                echo '</table>';
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
                        <img data-id="profilepic" src="<?= empty($user['IMAGE']) ? 'theme/img/boy.jpg' : 'contents/img/profilepic/'.$user['IMAGE'] ?>" alt="" width="150px" height="150px">
                        <div class="flexrow">
                            <label for="profileImage" style="border:1px solid grey;padding:10px 20px;border-radius:5px;width:fit-content;">Change image</label>
                            <input type="file" onchange="validateImage(this)" accept=".jpg,.png,.jpeg" name="profileImage" id="profileImage" style="display:none;">
                            <button type="button" onclick="validateImage('',true)" style="width:fit-content;" class="blue">Upload</button>
                        </div>
                        <p class="message"></p>
                    </section>
                    <section>
                        <label for="fname">Name</label>
                        <input name="fname" type="text" value="<?= $user['NAME']?>" onkeydown="return validateString(event.key,'!@')">
                        <p class="message"></p>
                    </section>
                    <section>
                        <label for="">Username</label>
                        <input type="text" value="<?= $user['USERNAME']?>" disabled>
                        <p class="message"></p>
                    </section>
                    <section>
                        <label for="">Email</label>
                        <input type="text" value="<?= $user['EMAIL']?>" disabled>
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
                    $t->query('SELECT IMAGE FROM BBVSUSERTABLE WHERE UID = ?',[[&$_POST['uid'],'i']]);
                    if(false != $t->execute()){
                        $userdata = $t->fetch();
                        $t->query('DELETE FROM BBVSUSERTABLE WHERE UID = ? && (ROLE = ? || ROLE = ?)',
                        [
                            [&$_POST['uid'],'i'],
                            [USERROLE::MODERATOR,'i'],
                            [USERROLE::USER,'i']
                        ]);
                        if(false !== $t->execute() && $t->affected_rows == 1){
                            if($userdata['IMAGE'] != 'boy.jpg' && $userdata['IMAGE'] != 'girl.jpg')
                                unlink(ROOTDIR.'contents/img/profilepic/'.$userdata['IMAGE']);
                            echo json_encode([
                                'result' => true,
                                'message' => 'User removed'
                            ]);
                            return;
                        }
                    }
                }
                $message = 'Failed to remove user';
            break;
            case 'removePoll':
                $t->query('SELECT POLLIMAGE FROM BBVSPOLLS WHERE PID = ?',[[&$_POST['pid'],'i']]);
                if(false != $t->execute()){
                    $polldata = $t->fetch();
                    if($_SESSION['role'] == USERROLE::ADMIN || $_SESSION['role'] == USERROLE::MODERATOR){
                        $t->query('DELETE FROM BBVSVOTES WHERE PID = ?',[[&$_POST['pid'],'i']]);
                        $t->execute();
                        $t->query('DELETE FROM BBVSPOLLS WHERE PID = ?',[[&$_POST['pid'],'i']]);
                    }else{
                        $t->query('DELETE FROM BBVSVOTES WHERE PID = SELECT PID FROM BBVSPOLLS WHERE PID = ? AND UID = ?',[[&$_POST['pid'],'i'],[&$_SESSION['UID']]]);
                        $t->execute();
                        $t->query('DELETE FROM BBVSPOLLS WHERE PID = ? AND CREATEDBY = ?',[[&$_POST['pid'],'i'],[&$_SESSION['UID'],'i']]);
                    }
                    if(false !== $t->execute() && $t->affected_rows == 1){
                        unlink(ROOTDIR.'contents/img/pollpic/'.$polldata['POLLIMAGE']);
                        echo json_encode([
                            'result' => true,
                            'message' => 'Poll removed'
                        ]);
                        return;
                    }
                }
                $message = 'Failed to remove poll';
            break;
            case 'removeVote':
                if(isset($_POST['uid']) && isset($_POST['pid']) && $_POST['uid'] == $_SESSION['UID']){
                    
                    $t->query('DELETE FROM BBVSVOTES WHERE PID = ? and UID = ?',[[&$_POST['pid'],'i'],[&$_POST['uid'],'i']]);
                    if(false !== $t->execute() && $t->affected_rows == 1){
                        echo json_encode([
                            'result' => true,
                            'message' => 'vote removed'
                        ]);
                        return;
                    }    
                }
                $message = 'Failed to remove poll';
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
                $t->query('SELECT * FROM BBVSPOLLS WHERE CREATEDBY = ? ORDER BY CREATEDON DESC',[[&$_SESSION['UID'],'i']]);
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
                        background-color: #ffffffd1;
                        padding: 10px;
                        width: 100%;
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
                    table.txdetails{
                        border-collapse: collapse;
                    }
                    table.txdetails tr td{
                        font-size: 13px;
                        padding: 5px;
                        border: 1px dotted grey;
                    }
                </style>
                <div>
                    <div class="flexrow">
                        <h3 style="color: var(--grey);">Polls created by you</h3>
                    </div>
                    <hr style="height: 20px;">
                    <div class="flexrow flexass active-polls">
                        <?php
                        if($t->affected_rows < 1){
                            echo "<p class=\"md\">You haven't created any poll yet.</p>";
                        }else{
                            while($r = $t->fetch()){ ?>
                                <div class="flexcol individual-polls">
                                    <div class="flexcol flexass details">
                                        <img src="contents/img/pollpic/<?= $r['POLLIMAGE'] ?>" alt="">
                                        <?= $r['STATUS'] ? 
                                        '<div class="status" style="background-color: seagreen;">Active</div>' : 
                                        '<div class="status" style="background-color: #d93939;">Not active</div>' ?>
                                        <div class="title">
                                            <h3><?= $r['POLLNAME'] ?></h3>
                                            <p class="sm"><?= $r['DESCRIPTION'] ?></p>
                                        </div>
                                    </div>
                                    <div class="flexcol flexass options">
                                        <table class="txdetails">
                                            <tr>
                                                <td>BPID</td>
                                                <td><?= $r['BPID'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Last Tx Hash</td>
                                                <td style="overflow-wrap: anywhere;"><?= $r['TXHASH'] ?></td>
                                            </tr>
                                        </table>
                                        <hr>
                                        <p class="md" style="color:grey;">Choices or Options</p>
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
                                        <button class="blue" style="width: 92%;" onclick="location.href=\'page/poll.php?pid='.$r['PID'].'&title='.$title.'\'">Cast your vote &nbsp;<i class="fa fa-external-link-alt"></i></button>
                                        <button onclick="modifyPoll(this,\'stop\', '.$r['PID'].')" data-bpid="'.$r['BPID'].'" style="color:white;background-color:#cd1f1f;margin: 0 0 20px 0;width: 92%;border:1px solid red;"><i class="fa fa-ban"></i> Stop Poll</button>';
                                    }else{
                                        echo '<select name="votingTime" id="" style="background-color: f1f1f1;width: 92%;">
                                            <option value="0">Select Voting period</option>
                                            <option value="1">One Day</option>
                                            <option value="7">One Week</option>
                                            <option value="14">Two Weeks</option>
                                            <option value="21">Three Weeks</option>
                                            <option value="30">One Month</option>
                                        </select>
                                        <button onclick="modifyPoll(this,\'publish\', '.$r['PID'].')" data-bpid="'.$r['BPID'].'" class="blue" style="margin: 0 0 20px 0;width: 92%;">Publish</button>';
                                    }
                                    ?>
                                    <button style="color:#cd1f1f;background-color:lightgrey;margin: 0 0 20px 0;width: 92%;border:1px solid red;" onclick="removePoll(this, true)" data-pid="<?= $r['PID']?>"><i class="fa fa-trash"></i> Delete Poll</button>
                                </div>
                        <?php }} ?>
                    </div>
                </div>
                <hr style="height: 20px;">
                <?php
                return;
            break;
            case 'showVotes':
                !isset($_SESSION['username']) ? die:'';
                $t->query('SELECT B.*,V.TXHASH AS VTHASH FROM BBVSPOLLS AS B, BBVSVOTES AS V WHERE V.PID = B.PID AND V.UID = ?;',[[&$_SESSION['UID'],'i']]);
                if(false == $t->execute()){
                    echo '<p class="red">Unable to fetch polls</p>';
                    return;
                }
                ?>
                <style>
                    .active-polls{
                        align-items: flex-start;
                        column-gap: 3em;
                        row-gap: 2em;
                    }
                    .individual-polls{
                        border-radius: 5px;
                        box-shadow: 0 0 7px grey;
                        width: 42%;
                        color: var(--grey);
                        cursor: pointer;
                        background-color: var(--white);
                    }
                    .individual-polls img{
                        width: 100%;
                        height: 200px;
                        object-fit: cover;
                        border-radius: 5px;
                    }
                    .individual-polls .details{
                        position: relative;
                        width: 100%;
                    }
                    .individual-polls .title{
                        margin-top: -10px;
                        padding: 10px 20px;
                        width: 100%;
                    }
                    .individual-polls .options{
                        width: 90%;
                        padding: 10px 0;
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
                    div.option{
                        justify-content: space-between;
                        width: 100%;
                        overflow: hidden;
                    }
                    div.option span{
                        display: flex;
                        align-items: center;
                        height: 100%;
                        position: absolute;
                        right: 0;
                        font-size: 18px;
                        background-color: var(--white);
                        padding: 0 10px;
                    }
                    label.md{
                        color: var(--grey);
                        width: 35%;
                        border-radius: 5px;
                        white-space: nowrap;
                        margin: 3px 0;
                        padding: 3px 10px;
                    }
                    .gn-option{
                        background-color: #bcbcbd47;
                    }
                    .wn-option{
                        background-color: #68b4ffb8;
                    }
                </style>
                <div>
                    <div class="flexrow">
                        <h3 style="color: var(--grey);">List of polls in which you have participated.</h3>
                    </div>
                    <hr style="height: 20px;">
                    <div class="flexrow active-polls">
                    <?php
                    if($t->affected_rows < 1){
                        echo "<p class=\"md\">You haven't participated in any poll yet.</p>";
                    }else{
                        while($r = $t->fetch()){ ?>
                            <div class="flexcol individual-polls" onclick="location.href='page/result.php?pid=<?= $r['PID'] ?>&title=<?= urlencode($r['POLLNAME'])?>'">
                                <div class="flexcol flexass details">
                                    <img src="contents/img/pollpic/<?= $r['POLLIMAGE'] ?>" alt="">
                                    <div class="title">
                                        <h3><?= $r['POLLNAME'] ?></h3>
                                        <p class="sm"><?= $r['DESCRIPTION'] ?></p>
                                    </div>
                                </div>
                                <div class="flexcol flexass options" style="row-gap: 0.1em;">
                                    <!-- <p class="md" style="color:grey;">Options</p> -->
                                    <?php
                                        $options = json_decode($r['OPTIONS']);
                                        $votecount = json_decode($r['VOTECOUNT']);
                                        $total=0;
                                        $winner=-1;
                                        foreach($votecount as $k => $v){
                                            $total += $v;
                                            $v > $winner ? $winner = $v :0;
                                        }
                                        foreach($options as $k => $v){
                                            $percentage = $total > 0 ? ( $votecount[$k] / $total ) * 100 : 0;
                                            echo 
                                            '<div class="flexrow option">
                                                <label class="'.($votecount[$k] == $winner ? 'wn-option': 'gn-option').' md" style="width:'.$percentage.'%;">'.$v.'</label>
                                                <span>'.$percentage.'%</span>
                                            </div>';
                                        }
                                        echo '<hr><span style="word-break:break-word;" class="sm">txhash: '.$r['VTHASH'].'</span>
                                        <hr><span class="sm"><i class="fa fa-clock"></i> Poll end date '.date('d M \a\t h:i a',$r['STARTDATE']).'</span>';
                                    ?>
                                </div>
                            </div>
                    <?php }} ?>
                    </div>
                </div>
                <hr style="height: 20px;">
                <?php
                return;
            break;
            case 'updatePoll':
                if($_SESSION['role'] == USERROLE::ADMIN || $_SESSION['role'] == USERROLE::MODERATOR){
                    $t->query('UPDATE BBVSPOLLS SET STATUS = ? WHERE PID = ?',
                    [
                        [&$_POST['status'],'i'],
                        [&$_POST['pid'],'i']
                    ]);
                    if(false !== $t->execute() && $t->affected_rows == 1){
                        echo json_encode([
                            'result' => true,
                            'message' => 'Poll updated successfuly'
                        ]);
                        return;
                    }
                }
                $message = 'Failed to update poll';
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
                        <td><?= $i++ ?></td>
                        <td><?= $r['NAME'] ?></td>
                        <td>@<?= $r['USERNAME'] ?></td>
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
                        <td><?= date('d M Y',$r['REGDATE']) ?></td>
                        <td>
                            <button style="color: seagreen;" onclick="updateUser(this)" data-uid="<?= $r['UID']?>">Update</button>
                            <button onclick="removeUser(this)" data-uid="<?= $r['UID']?>">Remove</button>
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