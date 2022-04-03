<?php

    require __DIR__.'/../theme/header.php';

    if(!isset($_SESSION['username'])){header('Location: login.php');return;};
    $error = '';
    if(isset($_POST['pollid']) && isset($_SESSION['UID'])){
        if(isset($_POST['vote']) && !empty($_POST['vote'])){
            if(isset($_POST['securityAnswer']) && !empty($_POST['securityAnswer'])){
                $t->query('SELECT SECURITYANSWER, EMAIL FROM BBVSUSERTABLE WHERE UID = ?',[[&$_SESSION["UID"],'i']]);
                if(false != $t->execute()){
                    $user = $t->fetch();
                    if(password_verify($_POST['securityAnswer'],$user['SECURITYANSWER'])){
                        if(password_verify($t->addSalt($_POST['pollid']), $_POST['signature'])){
                            $votehash = hash('sha256',$_POST['securityAnswer'] . $_POST['vote']);
                            $t->query('INSERT INTO BBVSVOTES SET UID = ?, PID = ?, VOTEHASH = ?',[
                                [&$_SESSION['UID'],'i'],
                                [&$_POST['pollid'],'i'],
                                [&$votehash,'s']
                            ]);
                            if(false!=$t->execute()){
                                $t->query('SELECT PID, VOTECOUNT,BPID FROM BBVSPOLLS WHERE PID = ?',[[&$_POST['pollid'],'i']]);
                                if(false != $t->execute()){
                                    $data = $t->fetch();
                                    $arr = json_decode($data['VOTECOUNT']);
                                    $arr[$_POST['vote']-1] += 1;
                                    $json = json_encode($arr);
                                    $t->query('UPDATE BBVSPOLLS SET VOTECOUNT = ? WHERE PID = ?',[
                                        [&$json,'s'],
                                        [&$data['PID'],'i']
                                    ]);

                                    if(false!= $t->execute() && $t->affected_rows == 1){ ?>
                                        <script>
                                            $(()=>{
                                                
                                                web3Connection().then(()=>{
                                                    bbvs.methods.vote(
                                                        <?= $data['BPID'] ?>,
                                                        '<?= $user['EMAIL'] ?>',
                                                        <?= $_POST['vote'] -1 ?>,
                                                        '<?= $_POST['securityAnswer'] ?>'
                                                    )
                                                    .send({from: ethereum.selectedAddress})
                                                    .then((receipt)=>{
                                                        $('#popup_img').attr('src','theme/img/greencheck.jpg')
                                                        $('#popup_message').text("You have successfully casted your vote. Your tx hash is: "+receipt.transactionHash);
                                                        $('#popup_btn').attr('href',"page/result.php?title=<?= $_POST['pollname'] ?>&txhash="+receipt.transactionHash);
                                                        $('#popup_btn').show();
                                                    })
                                                    .catch(err=>{
                                                        alert('Transaction Failed or reverted!');
                                                        console.log(err);
                                                    })
                                                })
                                            })
                                        </script>
                                        <?php
                                        echo "<style>
                                            .popup-parent{
                                                position: fixed;
                                                background-color: #2a2a2abd;
                                                top: 0;
                                                left: 0;
                                                width: 100vw;
                                                height: 100vw;
                                                z-index: 1000;
                                                padding-top: 150px;
                                                border-radius: 5px;
                                            }
                                            .popup{
                                                position: absolute;
                                                background-color: white;
                                                width: 50%;
                                                padding: 50px;
                                                border-radius: 10px;
                                                box-shadow: 0 0 80px 10px grey;
                                                left: 25%;
                                            }
                                        </style>
                                        <div class=\"popup-parent\">
                                            <div class=\"flexcol popup\">
                                                <img id=\"popup_img\" src=\"theme/img/loading.gif\" width=\"250px\"><br>
                                                <h3 id=\"popup_message\" style=\"word-break: break-word;\">Sign the transaction to confirm your vote.</h3>
                                                <a href id=\"popup_btn\" style=\"display:none;\"><button>Confirm</button></a>
                                            </div>
                                        </div>";
                                        
                                    }else{
                                        $error = 'Internal error occured';
                                    }
                                }else{
                                    $error = 'Internal error occured';
                                }
                            }else{
                                $error = 'Unable to caste your vote. Duplicate votes are not allowed!';
                            }
                        }else{
                            $error = 'Signature verification failed.';
                        }
                    }else{
                        $error = 'Security answer is incorrect.';
                    }
                }
            }else{
                $error = 'Security Answer is required.';
            }
        }else{
            $error = 'Please choose an option to vote.';
        }
        
    }
    if(!isset($_GET['title'])){header('Location: ../index.php');return;}

    $title = urldecode($_GET['title']);
    $t->query('SELECT a.*, b.SECURITYQUESTION FROM BBVSPOLLS AS a, BBVSUSERTABLE AS b WHERE a.POLLNAME LIKE ? AND b.UID = ? LIMIT 1', [[&$title,'s'],[&$_SESSION['UID'],'i']]);
    if(false != $t->execute() && $t->affected_rows == 1){
        $poll = $t->fetch();
    }else{
        header('Location: ../index.php');
    }
    if($poll['STATUS'] === 0) header('Location: result.php?title='.$_GET['title']);
    $pid = $poll['PID'];
?>
<style>
    input{cursor: pointer;}
    .poll{
        margin: 0 auto;
        width: 90%;
        background-color:#fff;
        border:1px solid rgba(0,0,0,0.2)k;
        border-radius: 3px;
        box-shadow: 0px 0px 10px rgb(0 0 0 / 20%);
    }
    .quetion{
        color:var(--dark);
        font-weight: bold;
        align-items: center;
        position:relative;
        padding:3% 3% 4% 5%;
    }
    .poll .quetion{
        font-weight: bold;
        position:relative;
        padding:30px;
        border-bottom:2px solid rgba(0,0,0,0.2);
    }
    .poll .answers{
        margin:5% 0 5% 5%;
    }
    .poll .answers label{
        display:block;
        margin:1%;
        padding:1%;
        width: 95%;
    }           
    .poll .answers label.row .dot:hover{
        background-color:#6665ee;
    }
    .poll .answers label:hover
    {
        border-bottom: 2px solid #6665ee;
        color: #6665ee;
    }
    .poll .answers label.selected{
        border-color: #6665ee;
    }
    .poll .answers label .row{
        display:flex;
    }
    .poll .answers label .row .dot{
        margin-right: 5%;
        display:block;
        width:20px;
        height:20px;
        border-radius: 50%;
        border:2px solid #ccc;
        position:relative;
    }
    .poll .answers label .row .dot::after{
        content:'';
        position: absolute;
        width:8px;
        height:8px;
        top:24%;
        left:22%;
        background-color:#ccc;
        border-radius: 50%;
    }

    .poll .answers label.selected .row .dot::after{
        background-color:#6665ee;
    }
    .poll .answers label.selected .row{
        color:#6665ee;
    }
    .options{
        padding: 30px;
        border-bottom: 2px solid rgba(0,0,0,0.2);
    }
    input[type="radio"]{
        width: fit-content;
    }
    img.opimg{
        object-fit: fill;
        position: relative;
        width:100%;
        height: 13em;
        border-bottom:4px solid rgba(0,0,0,0.2);
        border-radius: 4px;
    }
    form{
        position:relative;
        width: 65%;
    }
    .box{
        width: 100%;
        cursor:pointer;
        margin:5px 10px 0px 5px;
        position:relative;
        display:block;
        border-radius:4px;
        background-color:rgba(0,0,0,0.2);
        box-shadow: 0px 0px 10px rgb(0 0 0 / 20%);
    }
    .title{
        padding: 10px 30px;
        position: absolute;
        width: 100%;
        background-color: rgb(1 1 1 / 42%);
        color: white;
        bottom: 0;
    }
</style>
<script>
    $(document).ready(()=>{
        $('div.sidebar').css('height',$('div.poll').height())
        const option = $("label");
        for(let i=0;i<option.length;i++){
            $(option[i]).on('click',()=>{ 
                for(let j=0;j<option.length;j++){
                if($(option[j]).hasClass('selected')){
                    $(option[j]).removeClass('selected');
                }   
            }
            $(option[i]).addClass('selected');
            });
        }
    });
    function postComment(el){
        fd = new FormData($('#newCommentForm')[0]);
        fd.append('req','newComment');
        fd.append('pid',<?= $pid ?>);
        callAjax(
            'ajax/user.php',
            fd,
            (data)=>{
                data = JSON.parse(data);
                if(data['result']){
                    location.reload();
                }
            },
            (err)=>{
                console.log(err);
                alert('Unknown error occured');
            }
        );
    }
</script>
<div class="flexrow">
    <p style="font-weight:700;color: grey;width:65%;margin:20px 0;font-size:22px;padding-left:40px;">Caste your vote for <?php echo $poll['POLLNAME'] ?></p>
    <p style="font-weight:700;color: grey;margin:20px 0 20px 0;font-size:22px;">More active polls...</p>
</div>
<div class="flexrow flexass">
    
    <form method="POST" action="page/poll.php?title=<?php echo urlencode($poll['POLLNAME']) ?>">
        <div class="poll">
            <div class="banner">
                <img class="opimg" src="contents/img/pollpic/<?php echo $poll['POLLIMAGE'] ?>" alt="img">
                <p class="title"><?php echo $poll['POLLNAME'] ?></p>
            </div>
            <p class="quetion"><?php echo $poll['DESCRIPTION'] ?></p>
            <div class="flexcol flexass options">
                <p class="md" style="color:grey;">Choose your answer</p>
                <?php
                    $signature = password_hash($t->addSalt($poll['PID']),PASSWORD_DEFAULT);
                    echo '
                    <input type="hidden" name="pollname" value="'. urlencode($poll['POLLNAME']).'">
                    <input type="hidden" name="pollid" value="'.$poll['PID'].'">
                    <input type="hidden" name="signature" value="'.$signature.'">';
                    $options = json_decode($poll['OPTIONS']);
                    foreach($options as $k => $v){
                        echo 
                        '<div class="flexrow">
                            <input type="radio" name="vote" value="'.($k+1).'" id="'.$k.'">
                            <label class="md" for="'.$k.'">'.$v.'</label>
                        </div>';
                    }
                    echo '</div>
                    <div style="padding:0 30px;" class="flexcol flexass">
                        <hr><p class="md" style="color:grey;">Answer your security question</p>
                        <span class="md">Q. '.SETTINGS::securityQuestion[$poll['SECURITYQUESTION']].'</span>
                        <input type="text" name="securityAnswer" placeholder="Enter answer to your security question" required></input>
                        <span class="red md">'.$error.'</span>
                        <span class="sm"><i class="fa fa-clock"></i> Poll end date '.date('d M \a\t h:i a',$poll['STARTDATE'] + ($poll['PERIOD'])*24*3600).'</span>
                    </div>';
                ?>
            
            <button type="submit" id='btn_submit' style="background-color:var(--blue);color:#fff;font-weight:bold">Submit</button>
        </div>  
    </form>

    <!-- Active polls in sidebar -->
    <div class="sidebar flexrow flexass" style="width: 33%;height:1000px;overflow-y:scroll;">
        <?php
        $t->query('SELECT * FROM BBVSPOLLS WHERE STATUS = 1 AND PID <> ? ORDER BY STARTDATE DESC LIMIT 5',[[&$poll['PID'],'i']]);
        if(false != $t->execute()){
            while($poll = $t->fetch()){ 
                echo '<div class="box" onclick="location.href=\'page/poll.php?title='.urlencode($poll['POLLNAME']).'\'">
                    <img class="opimg" src="contents/img/pollpic/'.$poll['POLLIMAGE'].'" alt="img">
                    <p class="quetion">'.$poll['POLLNAME'].'</p>
                </div>';
            }
        }
        ?>
    </div>
    <div style="padding: 35px;background-color: #ecebeb;width: 59%;margin: 35px;">
        <p class="md">comment as <?= $_SESSION['username'] ?></p>
        <form action="#" method="post" id="newCommentForm">
            <textarea style="padding:5px;" name="comment" id="" cols="60" rows="2"></textarea>
            <button style="width:fit-content;" class="blue" type="button" onclick="postComment(this)">comment</button>
        </form>
        <hr>
        <div class="flexcol flexass">
        <?php
            if(false != $t->query('SELECT c.*, b.USERNAME, b.IMAGE FROM COMMENTS as c, BBVSUSERTABLE as b WHERE b.UID = c.UID and PID = ? ORDER BY CDATE DESC LIMIT 50',[[&$pid,'i']])){
                $t->execute();
                while($comment = $t->fetch()){
                    echo '
                    <div class="flexcol flexass">
                        <p class="quote" style="margin-bottom:0px;color:var(--black);">'.$comment['COMMENT'].'</p>
                        <p class="md flexrow flexasc" style="color:var(--grey);margin-left:10px;"><img class="brad50" width="30px" height="30px" src="contents/img/profilepic/'.$comment['IMAGE'].'">'.$comment['USERNAME'].' . '.date('d M Y',$comment['CDATE']).'</p>
                    </div>
                    <hr>';
                }
            }
            
        ?>
        </div>
    </div>
</div>

<?php

include ROOTDIR .'theme/footer.php';

?>