<?php
require __DIR__.'/theme/header.php';
$t->query('SELECT * FROM BBVSPOLLS WHERE STATUS =1 ORDER BY STARTDATE DESC');
if(false == $t->execute()){
    echo '<p class="red">Unable to fetch polls</p>';
    return;
}
                   
?>
<style>
    div.banner{
        background-image: url(theme/img/blockchain.webp);
        background-size: contain;
        margin: -30px;
        padding: 50px 50px 50px 30px;
        background-position-x: -80px;
    }
    div.intro{
        width: 45%;
        background-color: #fffffff0;
        box-shadow: 0 0 40px 90px #fffffff0;
    }
    div.intro h4.description{
        color: var(--dark);
        font-weight:500;
        margin: 20px 0;
    }
    .active-polls{
        column-gap: 1em;
        padding: 10px;
    }
    .individual-polls{
        border-radius: 5px;
        box-shadow: 0 0 7px grey;
        width: 32%;
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
<div class="banner">
    <div class="intro">
        <h1>Anonymous, Cheap, Simple & Secure Voting</h1>
        <h4 class="description"><span class="blue">Blockchain Based Voting System</span> is designed to overcome all the limitations of traditional voting system.</h4>
    </div>
</div>
<span class="vspace" style="height: 80px;"></span>
<h2 class="flexrow flexass" style="color: #5a5a5a;border-left: 3px solid var(--blue);padding: 5px 0 5px 10px;background-color: #0c67c10d;">Currently active Polls</h2>
<hr style="height: 20px;">
<div class="flexrow flexass active-polls">
    <?php while($r = $t->fetch()){ ?>
        <div class="flexcol individual-polls" onclick="location.href='page/poll.php?title=<?php echo urlencode($r['POLLNAME'])?>'">
            <div class="flexcol flexass details">
                <img src="contents/img/pollpic/<?php echo $r['POLLIMAGE'] ?>" alt="">
                <div class="title">
                    <h3><?php echo $r['POLLNAME'] ?></h3>
                    <p class="sm"><?php echo $r['DESCRIPTION'] ?></p>
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
                    echo '<hr><span class="sm"><i class="fa fa-clock"></i> Ending on '.date('d M \a\t h:i a',$r['STARTDATE']).'</span>
                    <button class="blue"><i class="fa fa-poll"></i> Vote</button>
                    <button class="blue" onclick="event.stopPropagation();location.href=\'page/result.php?title='.urlencode($r['POLLNAME']).'\';"><i class="fa fa-eye"></i> Result</button>';
                    
                    
                ?>
            </div>
        </div>
    <?php } ?>
</div>

<span class="vspace" style="height: 150px;"></span>
<h2 class="flexrow flexass" style="color: #5a5a5a;border-left: 3px solid var(--blue);padding: 5px 0 5px 10px;background-color: #0c67c10d;">Recently ended polls</h2>
<br>
<br>
<div class="flexrow active-polls">
    <?php
    $t->query('SELECT * FROM BBVSPOLLS WHERE STATUS = 0 AND PERIOD > 0 AND STARTDATE > 0 ORDER BY STARTDATE DESC');
    if(false != $t->execute()){
        while($r = $t->fetch()){ ?>
            <div class="flexcol individual-polls" onclick="location.href='page/result.php?title=<?php echo urlencode($r['POLLNAME'])?>'">
                <div class="flexcol flexass details">
                    <img src="contents/img/pollpic/<?php echo $r['POLLIMAGE'] ?>" alt="">
                    <div class="title">
                        <h3><?php echo $r['POLLNAME'] ?></h3>
                        <p class="sm"><?php echo $r['DESCRIPTION'] ?></p>
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
                        echo '<hr><span class="sm"><i class="fa fa-clock"></i> Ended on '.date('d M \a\t h:i a',$r['STARTDATE']).'</span>';
                    ?>
                </div>
            </div>
        <?php
        }
    }
    ?>
</div>
<br>
<br>
<br>
<?php
require __DIR__.'/theme/footer.php';
?>
