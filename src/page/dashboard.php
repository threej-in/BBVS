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
        width: 25%;
        border-right: 1px solid grey;
    }
</style>
<div class="flexrow">
    <div class="sidebar">
        <div class="flexrow">
            <img src="<?php echo HOMEURI.'theme/img/logo.png' ?>" alt="" class="rad50" width="65" height="65">
            <div class="flexcol" style="row-gap: 0;">
                <h4 class="lg"><?php echo $user['NAME']?></h4>
                <h5 style="color:grey;" class="sm">@<?php echo $user['USERNAME']?></h5>
            </div>
            
        </div>
    </div>
</div>
<?php

include ROOTDIR .'theme/footer.php';

?>