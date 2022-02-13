<?php

    require __DIR__.'/../theme/header.php';
    $t->query('SELECT PID,OPTIONS FROM BBVSPOLLS ');

    if(!isset($_SESSION['username'])) header('Location: login.php');
    $error = '';
    if(isset($_POST['pollid']) && isset($_SESSION['UID'])){
        if(isset($_POST['vote']) && !empty($_POST['vote'])){
            if(isset($_POST['securityAnswer']) && !empty($_POST['securityAnswer'])){
                $t->query('SELECT SECURITYANSWER FROM BBVSUSERTABLE WHERE UID = ?',[[&$_SESSION["UID"],'i']]);
                if(false != $t->execute()){
                    if(password_verify($_POST['securityAnswer'],$t->fetch()['SECURITYANSWER'])){
                        if(password_verify($t->addSalt($_POST['pollid']), $_POST['signature'])){
                            $votehash = hash('sha256',$_POST['securityAnswer'] . $_POST['vote']);
                            $t->query('INSERT INTO BBVSVOTES SET UID = ?, PID = ?, VOTEHASH = ?',[
                                [&$_SESSION['UID'],'i'],
                                [&$_POST['pollid'],'i'],
                                [&$votehash,'s']
                            ]);
                            if(false!=$t->execute()){
                                $t->query('SELECT PID, VOTECOUNT FROM BBVSPOLLS WHERE PID = ?',[[&$_POST['pollid'],'i']]);
                                if(false != $t->execute()){
                                    $data = $t->fetch();
                                    $arr = json_decode($data['VOTECOUNT']);
                                    $arr[$_POST['vote']-1] += 1;
                                    $json = json_encode($arr);
                                    $t->query('UPDATE BBVSPOLLS SET VOTECOUNT = ? WHERE PID = ?',[
                                        [&$json,'s'],
                                        [&$data['PID'],'i']
                                    ]);
                                    if(false!= $t->execute() && $t->affected_rows == 1){
                                        header('Location: result.php?vote=success&title='.$_POST['pollname']);
                                        return;
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
    if(!isset($_GET['title'])) header('Location: ../index.php');

    $title = urldecode($_GET['title']);
    $t->query('SELECT a.*, b.SECURITYQUESTION FROM BBVSPOLLS AS a, BBVSUSERTABLE AS b WHERE a.POLLNAME LIKE ? AND b.UID = ? LIMIT 1', [[&$title,'s'],[&$_SESSION['UID'],'i']]);
    if(false != $t->execute() && $t->affected_rows == 1){
        $poll = $t->fetch();
    }else{
        header('Location: ../index.php');
    }
    if($poll['STATUS'] === 0) header('Location: result.php?title='.$_GET['title']);
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
                        <hr><span class="md">Q. '.SETTINGS::securityQuestion[$poll['SECURITYQUESTION']].'</span>
                        <input type="text" name="securityAnswer" placeholder="Enter answer to your security question" required></input>
                        <span class="red md">'.$error.'</span>
                        <span class="sm"><i class="fa fa-clock"></i> Poll end date '.date('d M \a\t h:i a',$poll['STARTDATE'] + ($poll['PERIOD'])*24*3600).'</span>
                    </div>';
                ?>
            
            <button type="submit" id='btn_submit' style="background-color:var(--blue);color:#fff;font-weight:bold">Submit</button>
        </div>  
    </form>
    <div class="sidebar flexrow" style="width: 33%;height:1000px;overflow-y:scroll;">
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
</div>
<?php

include ROOTDIR .'theme/footer.php';

?>