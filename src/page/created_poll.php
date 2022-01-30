<?php
    require __DIR__.'/../theme/header.php';

?>
<html>
 <head>
     <style>
        /* *{
             margin:0;
             padding:0;
         }
         body{
             display:flex;
             align-items: center;
             justify-content: center;
             background-color: #6665ee;
         }*/
         .poll{
             margin:5% 0 0 25%;
             position:absolute;
             width: 50%;
             max-width: 50%;
             background-color:#fff;
             border:1px solid rgba(0,0,0,0.2)k;
             border-radius: 3px;
             box-shadow: 5px 5px 30px rgba(0,0,0,0.2);
           }
           .poll .quetion{
               margin-bottom:4%;
               align-items: center;
               position:relative;
               padding:7% 3% 4% 30%;
               border-bottom:2px solid rgba(0,0,0,0.2);
           }
           .poll .answers{
               margin:10% 0 5% 5%;
           }
           .poll .answers label{
               display:block;
               margin:1%;
               padding:1%;
               width: 95%;
               border: 2px solid #e6e6e6;
               border-radius: 10px;
           }           
           .poll .answers label:hover{
            border-color: #6665ee;
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
               width:15px;
               height:15px;
               border-radius: 50%;
               border:2px solid #ccc;
               position:relative;
           }
           .poll .answers label .row .dot::after{
               content:'';
               position: absolute;
               width: 8px;
               height: 8px;
               top:14%;
               left:15%;
               background-color:#ccc;
               border-radius: 50%;
           }

           .poll .answers label.selected .row .dot::after{
               background-color:#6665ee;
           }
           .poll .answers label.selected .row{
               color:#6665ee;
           }
           input[type="radio"]{
               display:none;
           }
           #btn_submit{
               color:#6665ee;
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
  
 </head>
 <body>
     <form method="POST">
        <div class="poll">
            <p class="quetion"> Select your Option</p>
            <div class='answers'>
                <input type="radio" name="option" id="op1" value="op1">
                <label for="op1">
                    <div class="row">
                    <span class="dot"></span>
                    <span class="text">option1</span>
                    </div>  
                </label>
                <input type="radio" name="option" id="op2" value="op2">
                <label for="op2">
                    <div class="row">    
                    <span class="dot"></span>
                    <span class="text">option2</span>
                    </div>  
                </label>
                <input type="radio" name="option" id="op3" value="op3">
                <label for="op3">
                    <div class="row">      
                    <span class="dot"></span>
                    <span class="text">option3</span>
                    </div>  
                </label>
                <input type="radio" name="option" id="op4" value="op4">
                <label for="op4">
                    <div class="row">     
                    <span class="dot"></span>
                    <span class="text">options4</span>
                    </div>  
                </label>  
            </div>
            <button type="submit" id='btn_submit'>Submit</button>
        </div>
        
     </form> 
 </body>
</html>