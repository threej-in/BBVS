    </main>
        <style>
            footer{
                background-color: var(--blue);
                color: var(--white);
                padding: 20px 30px;
            }
            footer div.widget{
                margin: 20px 0;
            }
            footer div.widget>div{
                width: 25%;
                margin-right: 20px;
            }
            footer ul li a,footer ul li a:hover{
                color: white;
            }
        </style>
        <footer>
            <div class="flexrow flexass widget">
                <div class="flexcol flexass">
                    <img class="logo brad50" style="box-shadow:0 0 2px 3px;" src="theme/img/logo.png" alt="Blockchain Based Voting system" height="50px" width="50px">
                    <h3><?= $website['title'] ?></h3>
                    <p class="md">Blockchain Based Voting System is future for digital voting.</p>
                    <div class="flexrow">
                        <a target="_blank" href="https://twitter.com/threej_in"><li class="fab fa-twitter" style="color: var(--white);"></li></a>
                        <a target="_blank" href="https://telegram.me/threej_in"><li class="fab fa-telegram" style="color: var(--white);"></li></a>
                        <a target="_blank" href="https://www.linkedin.com/company/threejin"><li class="fab fa-linkedin" style="color: var(--white);"></li></a>
                    </div>
                </div>
                <div class="flexcol flexass">
                    <h4>Navigation</h4>
                    <ul style="list-style: none;" class="md">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="page/dashboard.php">Dashboard</a></li>
                        <li><a href="page/result.php">Result</a></li>
                        <li><a href="page/dashboard.php?show=newpoll">Create Poll</a></li>
                        <li><a href="page/dashboard.php?show=profile">Profile Settings</a></li>
                    </ul>
                </div>
                <div class="flexcol flexass">
                    
                </div>
                <div class="flexrow flexass"></div>
            </div>
            <hr class="white">
            <div class="flexrow flexass sm">
                &copy; <?php echo date('Y', time())?> <a style="color:var(--white);" href="https://threej.in">threej.in</a>
            </div>
        </footer>
              <script src="theme/script/main.js"></script>
    </body>
</html>
