<?php
    //dashboard page
    include __DIR__.'/../theme/header.php';
    if(!isset($_SESSION['username'])) header('Location: login.php');
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
        flex-wrap: nowrap;
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
        margin: 5px auto;
        border: 1px solid lightgrey;
        border-radius: 5px;
        min-width: 73%;
        max-width: 77%;
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
    td, th{
        padding:10px;
        color: grey;
        border-bottom: 1px solid lightgrey;
        text-align: left;
    }
    th{
        color: black;
    }
    ul.info{
        list-style-type: disc;
        margin: 20px;
    }
    ul.info li{
        font-size: 14px;
        color: var(--dark);
    }
</style>
<script src="theme/script/dashboard.js"></script>
<div class="flexrow flexass">
    <div class="sidebar flexcol flexass">
        <div class="flexrow" style="margin: 40px 0;">
            <img data-id="profilepic" src="contents/img/profilepic/<?php echo $user['IMAGE'] ?>" alt="" style="box-shadow: 0 0 2px 2px lightgrey;" class="brad50" width="70" height="70">
            <div class="flexcol" style="row-gap: 0;">
                <h4 class="lg"><?php echo $user['NAME']?></h4>
                <h5 style="color:grey;" class="sm">@<?php echo $user['USERNAME']?></h5>
            </div>
        </div>
        <ul>
            <hr style="height: 1px; background-color:lightgrey;margin:0;">
            <li id="newpoll" onclick="showContent('newPoll')"><i class="fa fa-plus fa-sm"></i> Create New Poll</li>
            <li id="mypolls" onclick="showContent('showPolls')"><i class="fa fa-poll fa-sm"></i> My Polls</li>
            <li id="myvotes" onclick="showContent('showVotes')"><i class="fa fa-vote-yea fa-xs"></i> My Votes</li>
            <?php
                if(isset($_SESSION['role']) && $_SESSION['role'] == USERROLE::ADMIN){
                    echo '<li onclick="showContent(\'userManagement\')"><i class="fa fa-users-cog fa-xs"></i> User Management</li>
                        <li onclick="showContent(\'pollManagement\')"><i class="fa fa-sliders-h fa-xs"></i> Polls Management</li>';
                }elseif(isset($_SESSION['role']) && $_SESSION['role'] == USERROLE::MODERATOR){
                    echo '<li onclick="showContent(\'pollManagement\')"><i class="fa fa-sliders-h fa-xs"></i> Polls Management</li>';
                }
            ?>
            <li id="profile" onclick="showContent('profile')"><i class="fa fa-cog fa-xs"></i> Account Settings</li>
        </ul>
        <hr style="height:50px;">
    </div>
    <style>
        tr, td{
            border: none;
        }
    </style>
    <div class="content" id="contentArea">
        <h2 style="color:grey;">How to use this platform?</h2>
        <br>
        <table>
            <tr>
                <td>🗳</td>
                <td><p>Visit <a href="index.php" style="color:var(--blue);">homepage</a> to see the list of active polls and click on the vote button to caste your vote.</p></td>
            </tr>
            <tr>
                <td>📊</td>
                <td><p>To create your own poll click on<strong> Create New Poll </strong>option from left sidebar.</p></td>
            </tr>
            <tr>
                <td>⚙️</td>
                <td><p>To manage your existing polls click on<strong> My Polls </strong>option from left sidebar.</p></td>
            </tr>
            <tr>
                <td>👤</td>
                <td><p>To manage your profile click on<strong> Account Settings </strong>option from left sidebar.</p></td>
            </tr>
        </table>        
    </div>
</div>
<?php

include ROOTDIR .'theme/footer.php';

?>