<?php
    require __DIR__.'/../theme/header.php';
    if(!isset($_SESSION['username'])) header('Location: login.php');
    if(!isset($_GET['title'])) header('Location: ../index.php');
    $title = urldecode($_GET['title']);
    $t->query('SELECT * FROM BBVSPOLLS WHERE POLLNAME LIKE ? LIMIT 1', [[&$title,'s']]);
    if(false != $t->execute()){
        $poll = $t->fetch();
        if(isset($_GET['txhash'])){
            $t->query('UPDATE BBVSVOTES SET TXHASH = ? WHERE UID = ? AND PID = ?',[
                [&$_GET['txhash'],'s'],
                [&$_SESSION['UID'],'i'],
                [&$poll['PID'],'i']
            ]);
            $t->execute();
        }
    }
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
        white-space: nowrap;
        margin: 5px 0;
        padding: 3px 10px;
    }
    .gn-option{
        background-color: #bcbcbd47;
    }
    .wn-option{
        background-color: #68b4ffb8;
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
    <p style="font-weight:700;color: #eba11b;width:65%;margin:20px 0;font-size:22px;padding-left:40px;"><i class="fa fa-trophy"></i> Poll Result</p>
    <p style="font-weight:700;color: grey;margin:20px 0 20px 0;font-size:22px;">Recently ended polls...</p>
</div>
<div class="flexrow flexass">
    <form method="POST">
        <div class="poll">
            <div class="banner">
                <img class="opimg" src="contents/img/pollpic/<?php echo $poll['POLLIMAGE'] ?>" alt="img">
                <p class="title"><?php echo $poll['POLLNAME'] ?></p>
            </div>
            <p class="quetion"><?php echo $poll['DESCRIPTION'] ?></p>
            <div class="flexcol flexass options">
                <div class="flexrow option" style="color: grey;">
                    <p>Options/Candidates</p>
                    <span>No of votes</span>
                </div>
                <?php
                    $options = json_decode($poll['OPTIONS']);
                    $votecount = json_decode($poll['VOTECOUNT']);
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
                            <span>'.$votecount[$k].'</span>
                        </div>';
                    }
                    echo '<hr><span class="sm"><i class="fa fa-clock"></i> Poll end date '.date('d M \a\t h:i a',$poll['STARTDATE'] + ($poll['PERIOD'])*24*3600).'</span>';
                ?>
            </div>
        </div>  
    </form>
    <div class="flexrow sidebar" style="width: 33%;height:1000px;overflow-y:scroll;">
        <?php
        $t->query('SELECT * FROM BBVSPOLLS WHERE STATUS = 0 AND PERIOD > 0 AND PID <> ? ORDER BY STARTDATE DESC LIMIT 5',[[&$poll['PID'],'i']]);
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