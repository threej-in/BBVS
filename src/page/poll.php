<?php
    require __DIR__.'/../theme/header.php';
    if(!isset($_SESSION['username'])) header('Location: login.php');
    if(!isset($_GET['title'])) header('Location: index.php');
    $title = urldecode($_GET['title']);
    $t->query('SELECT * FROM BBVSPOLLS WHERE POLLNAME LIKE ? LIMIT 1', [[&$title,'s']]);
    if(false != $t->execute()){
        $poll = $t->fetch();
    }
?>
<style>
    input{cursor: pointer;}
    .poll{
        margin:0 0 0 50px;
        width: 80%;
        background-color:#fff;
        border:1px solid rgba(0,0,0,0.2)k;
        border-radius: 3px;
        box-shadow: 5px 5px 30px rgba(0,0,0,0.2);
    }
    .quetion{
        font-weight: bold;
        margin-bottom:4%;
        align-items: center;
        position:relative;
        padding:3% 3% 4% 5%;
    }
    .poll .quetion{
        font-weight: bold;
        margin-bottom:4%;
        position:relative;
        padding:7% 3% 4% 5%;
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
        width: 90%;
        padding: 30px;
    }
    input[type="radio"]{
        width: fit-content;
    }
    img.opimg{
        object-fit:cover;
        position: relative;
        margin-top:0%;
        margin-left:0%;
        width:100%;
        height: 13em;
        border-bottom:4px solid rgba(0,0,0,0.2);
        border-radius: 4px;

    }
    form{
        position:relative;
        width: 65%;
    }
    .suggestion-sidebar{
        width: 30%;
    }
    .box{
        cursor:pointer;
        margin-bottom:15%;
        position:relative;
        display:block;
        border-radius:4px;
        background-color:rgba(0,0,0,0.2);
    }
    .title{
        margin-top:-10%;
        margin-left:5%;
        padding:10px;
        position:relative;
        width:90%;
        background-color:rgb(1,1,1,0.6);
        color:white;
        border:2px solid white;
    }

</style>
<script>
    $(document).ready(()=>{
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
<div class="flexrow flexass">
    <form method="POST">
        <div class="poll">
            <img class="opimg" src="contents/img/pollpic/<?php echo $poll['POLLIMAGE'] ?>" alt="img">
            <div class="title">
            <p><?php echo $poll['POLLNAME'] ?></p>
            </div>
            
            <p class="quetion"><?php echo $poll['DESCRIPTION'] ?></p>
            <div class="flexcol flexass options">
                <p class="md" style="color:grey;">Choose your answer</p>
                <?php
                    $options = json_decode($poll['OPTIONS']);
                    foreach($options as $k => $v){
                        echo 
                        '<div class="flexrow">
                            <input type="radio" name="vote" id="">
                            <label class="md">'.$v.'</label>
                        </div>';
                    }
                ?>
            </div>
            <button type="submit" id='btn_submit' style="background-color:var(--blue);color:#fff;font-weight:bold">Submit</button>
        </div>  
    </form> 
    <div class="suggestion-sidebar">
        <h4 style="color: grey;margin:10px 0;">More active polls...</h4>
        <?php
        $t->query('SELECT * FROM BBVSPOLLS WHERE STATUS = 1 ORDER BY STARTDATE DESC LIMIT 5');
        if(false != $t->execute()){
            
            while($poll = $t->fetch()){ 
                echo '<div class="box" onclick="location.href=\'page/poll.php?'.urlencode($poll['POLLNAME']).'\'">
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