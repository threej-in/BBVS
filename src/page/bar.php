
<head>
    <link rel="stylesheet" href="vot.css">
    <style>
    input:focus{
        background:rgba(255,255,255,0.2);
      }
      input{
          color:white;
          margin-top:10px;
          margin-bottom: 10px;
          border:none;
          outline:none;
          backdrop-filter:  blur(40px);
          background-color:rgba(255,255,255,0); ;
          border-bottom: 1px solid black;
      }
      input::placeholder{
        color:white;
      }
      button{
          position:relative;
          border:none;
          margin-top:5px;
          width:50px;
          height:25px;
          color:#ff3561;
          background:rgba(255,255,255,0.9);
          border-radius: 5px;
          box-shadow:5px 5px rgba(0,0,0,0.1)

      }
      #f1{
          margin:0 0 0 500px ;
          padding:0px;
          width:auto;
          height:auto;
          padding: 50px 10px 10px 10px;
          display:inline-block;
          text-align:center;
          border:2px solid rgba(255,255,255,0.2);;
          border-radius:10px;
          background:rgba(255,255,255,0.3);
          box-shadow:10px 10px 5px rgba(0,0,0,0.1);
      }
      #f2{
          margin:0 0 0 450 ;
          padding:5px;
          text-align:center;
          display:none;
          width:200px;
          height:auto;
          border:2px solid rgba(255,255,255,0.2);
          border-radius: 10px;
          background:rgba(255,255,255,0.3);
          box-shadow:10px 10px 5px rgba(0,0,0,0.1);

      }
      #f3{
          margin:0 0 0 500px ;
          display:none;
          width:auto;
          height:auto;
          border:2px solid rgba(255,255,255,0.2);
          border-radius: 10px;
          padding:40px;
          text-align:center;
          background:rgba(255,255,255,0.3);
          box-shadow:10px 10px 5px rgba(0,0,0,0.1);

      }
    *{
      margin:0;
     padding:0;
     }
 section .wave{
   position:absolute;
   bottom:0;
   left:0;
   width:100%;
   height:90px;
   background:url("wave.png");
   background-size:1000px 100px; 
 }
 section .wave.w1{
   animation: animate 20s linear infinite;
   z-index: 1000;
   opacity: 0.8;
   animation-delay: -0.1s;
   bottom:10;
 }
 @keyframes animate{
   0%{
     background-position-x: 0;
   }
   100%{
     background-position-x: 1000px;
   }
 }
 section .wave.w2{
   animation: animate2 15s linear infinite;
   z-index: 999;
   opacity: 0.5;
   animation-delay: -5s;
   bottom:0;
 }
 @keyframes animate2{
   0%{
     background-position-x: 0;
   }
   100%{
     background-position-x: 1000px;
   }
 }
 section .wave.w3{
   animation: animate3 5s linear infinite;
   z-index: 998;
   opacity: 0.2;
   animation-delay: -3s;
   bottom:10px;
 }
 @keyframes animate3{
   0%{
     background-position-x: 0;
   }
   100%{
     background-position-x: 1000px;
   }
 }
 section .wave.w4{
   animation: animate2 15s linear infinite;
   z-index: 997;
   opacity: 0.7;
   animation-delay: -5s;
   bottom:20px;
 }
 @keyframes animate4{
   0%{
     background-position-x: 0;
   }
   100%{
     background-position-x: 1000px;
   }
 }
 p{
   width:200px;
   margin:5px 0 50px 525px;
   color:rgba(255,255,255,0.5);
   text-shadow:10px 10px 10px rgba(0,0,0,0.6);
   font-size: 34px;

 }
 .sbox{
   position: relative;
   margin-left:200px;
   width:84.4%;
   height:92%;
   overflow: hidden;
   background:#ff3561;
  }
body.active .sbox{
    margin-left:0%;
    width:100%;
   
  }
  body.active p{
    margin:50px 0 0 570px;
  }
  body.active .sbox #f1{
    margin:25px 0 0 540px ;
  }
  body.active .sbox #f2{
    margin:25px 0 0 550px ;
  }
  body.active .sbox #f3{
    margin:25px 0 0 540px ;
  }
  </style>
   
</head>
<body>
    <body>
    <div class="bar">  
        <div class="head">
           <div class="headnev">    
             <ul>
                 <li class="log"><a href='#'>VOTTING SYSTEM</a></li>
                 <div class="mnu">
                   <li><a href="#">Menu</a></li>
                 </div>
                 <li><a href="#"><b>Home</b></a></li>
                 <li><a href="#"><b>page 1</b></a></li>
                 <li><a href="#"><b>page 2</b></a></li>
               </ul>
           </div>
        </div>
        <div class="sidnev">
            <div class="prof">
                <img src="wallpapersden.com_madara-uchiha-anime_2384x1342.jpg" alt="">
            </div>
            <h3>USER Name   </h3>
            <ul>
              <li><a href="votpoll.html">Creat Poll</a></li>
              <li><a href="">Edit Poll</a></li>
              <li><a href="">DeLete Poll</a></li>
              <li><a href="">setting</a></li>
            </ul>
        </div>
        </div>
        <script>
                var menu = document.querySelector(".mnu");
                menu.addEventListener("click",function(){
                    document.querySelector("body").classList.toggle("active");  
                })
            </script>
