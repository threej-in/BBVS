<?php
    //dashboard page
    include __DIR__.'/../theme/header.php';
    $t->query('SELECT * FROM BBVSUSERTABLE WHERE USERNAME = ?', [[&$_SESSION['username'],'s']]);
    if(false != $t->execute()){
        $user = $t->fetch();
    }
?>
<style>
    div.sidebar{
        width: 24%;
        border-right: 1px solid grey;
        background-color:#f1f1f1;
        margin: -30px;
        padding: 20px 0 20px 30px;
        margin-right: 0;
        height: 95vh;
        position: sticky;
        top: 65px;
    }
    div.sidebar ul{width: 100%;}
    div.sidebar li{
        width: 100%;
        padding: 12px 10px;
        list-style-type: none;
        border-bottom: 1px solid lightgrey;
        cursor: pointer;
        color: grey;
    }
    div.sidebar li:hover{
        background-color: var(--white);
        color: black;
    }
    div.sidebar li.active{
        background-color: var(--blue);
        color: white;
    }
    div.sidebar ul li i{
        width: 22px;
    }
    div.content{
        margin: 25px auto;
        border: 1px solid lightgrey;
        border-radius: 5px;
        width: 70%;
        padding: 20px;
    }
    form{
        width: 60%;
        display: flex;
        flex-flow: column;
        row-gap: 1em;
    }
    .error{
        color:red;
        font-size:14px;
    }
</style>
<script src="theme/script/dashboard.js"></script>
<div class="flexrow flexass">
    <div class="sidebar flexcol flexass">
        <div class="flexrow" style="margin: 40px 0;">
            <img data-id="profilepic" src="<?php echo 'theme/img/boy.jpg' ?>" alt="" style="box-shadow: 0 0 2px 2px lightgrey;" class="brad50" width="70" height="70">
            <div class="flexcol" style="row-gap: 0;">
                <h4 class="lg"><?php echo $user['NAME']?></h4>
                <h5 style="color:grey;" class="sm">@<?php echo $user['USERNAME']?></h5>
            </div>
        </div>
        <ul>
            <hr style="height: 1px; background-color:lightgrey;margin:0;">
            <li><i class="fa fa-poll fa-sm"></i> My Polls</li>
            <li><i class="fa fa-vote-yea fa-xs"></i> My Votes</li>
            <?php
                if(isset($_SESSION['role']) && $_SESSION['role'] == USERROLE::ADMIN){
                    echo '<li><i class="fa fa-users-cog fa-xs"></i> User Management</li>
                        <li><i class="fa fa-sliders-h fa-xs"></i> Polls Management</li>';
                }elseif(isset($_SESSION['role']) && $_SESSION['role'] == USERROLE::MODERATOR){
                    echo '<li><i class="fa fa-sliders-h fa-xs"></i> Polls Management</li>';
                }
            ?>
            <li onclick="showProfile()"><i class="fa fa-cog fa-xs"></i> Account Settings</li>
        </ul>
        <hr style="height:50px;">
    </div>
    <div class="content" id="contentArea">
        
    </div>
</div>
<?php

include ROOTDIR .'theme/footer.php';

?>