<?php
require __DIR__.'/theme/header.php';
?>
<style>
    div.banner{
        background-image: url(theme/img/blockchain.webp);
        background-size: contain;
        margin: -30px;
        padding: 50px;
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
    }
    .individual-polls{
        border-radius: 20px;
        box-shadow: 0 0 1px var(--dark);
        width: 32%;
        color: var(--grey);
        cursor: pointer;
    }
    .individual-polls:hover{
        box-shadow: 0 0 6px 0px var(--grey);
    }
    .individual-polls img{
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 20px;
    }
    .individual-polls .content{
        padding: 20px;
    }
</style>
<div class="banner">
    <div class="intro">
        <h1>Anonymous, Cheap, Simple & Secure Voting</h1>
        <h4 class="description"><span class="blue">Blockchain Based Voting System</span> is designed to overcome all the limitations of traditional voting system.</h4>
    </div>
</div>
<span class="vspace" style="height: 150px;"></span>
<h3 class="flexrow flexacc">Participate in active Polls</h3>
<br>
<br>
<div class="flexrow active-polls">
    <div class="flexcol individual-polls">
        <img src="theme/img/logo.png" alt="">
        <div class="flexcol content">
            <h4>What is your favourite messaging application</h4>
            <ul>
                <li>Facebook messenger</li>
                <li>Signal</li>
                <li>Telegram</li>
                <li>Whatsapp</li>
            </ul>
        </div>
    </div>
    <div class="flexcol individual-polls">
        <img src="theme/img/logo.png" alt="">
        <div class="flexcol content">
            <h4>What is your favourite messaging application</h4>
            <ul>
                <li>Facebook messenger</li>
                <li>Signal</li>
                <li>Telegram</li>
                <li>Whatsapp</li>
            </ul>
        </div>
    </div>
    <div class="flexcol individual-polls">
        <img src="theme/img/logo.png" alt="">
        <div class="flexcol content">
            <h4>What is your favourite messaging application</h4>
            <ul>
                <li>Facebook messenger</li>
                <li>Signal</li>
                <li>Telegram</li>
                <li>Whatsapp</li>
            </ul>
        </div>
    </div>
</div>

<span class="vspace" style="height: 150px;"></span>
<h3 class="flexrow flexacc">Result of recently ended polls</h3>
<br>
<br>
<div class="flexrow active-polls">
    <div class="flexcol individual-polls">
        <img src="theme/img/logo.png" alt="">
        <div class="flexcol content">
            <h4>What is your favourite messaging application</h4>
            <ul>
                <li>Facebook messenger</li>
                <li>Signal</li>
                <li>Telegram</li>
                <li>Whatsapp</li>
            </ul>
        </div>
    </div>
    <div class="flexcol individual-polls">
        <img src="theme/img/logo.png" alt="">
        <div class="flexcol content">
            <h4>What is your favourite messaging application</h4>
            <ul>
                <li>Facebook messenger</li>
                <li>Signal</li>
                <li>Telegram</li>
                <li>Whatsapp</li>
            </ul>
        </div>
    </div>
    <div class="flexcol individual-polls">
        <img src="theme/img/logo.png" alt="">
        <div class="flexcol content">
            <h4>What is your favourite messaging application</h4>
            <ul>
                <li>Facebook messenger</li>
                <li>Signal</li>
                <li>Telegram</li>
                <li>Whatsapp</li>
            </ul>
        </div>
    </div>
</div>

<br>
<br>
<br>
<?php
require __DIR__.'/theme/footer.php';
?>
