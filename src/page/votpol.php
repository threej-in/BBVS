<html>
  <?php require("bar.php")?>
        <section class="sbox">  
         <p>Creat Poll</p>    
     <div id="f1">
         <input type="text" placeholder="Enter Title" id="ntit"/><br>
         <input id='nop' type="number" placeholder="number of option you need"/><br>
         <button onclick="hide(),input()">next</button>
     </div>   
     <div id="f2">
        <h4>enter options</h4>
         <span><br></span>
         <!--<button onclick="sop()">Next</button>-->
     </div>
     
     <div id='f3'>
         <h3 id='tit'></h3>
         <span></span>
     </div>  
     
        
    </section>  
     <script>
         let f1 = document.getElementById("f1");
         let f2 = document.getElementById("f2");
         let f3 = document.getElementById("f3");
         let hide = () =>{
            f1.style.display="none";
         };
        
        let input = () =>{
            i=0;
            f2.style.display='block';
            let nop = document.getElementById('nop').value;
            for(i=0;i<nop;i++){
              console.log(i);
              //create the elemet in javascript dynamic;
              let a = document.createElement("input");
              //give id to create elemte in html dynamic;
              a.id =`op${i}`; 
              a.placeholder = `Option ${i+1}`;
              //give style to all create elemte at once in javascript;
              a.setAttribute("style","width:200px;border:none;outline:none;border-bottom:1px solid black;");
              //create elemet in html dynamic; 
              f2.appendChild(a);
            }
            let btn = document.createElement("button");
            btn.innerHTML="Next";
            f2.appendChild(btn);
            btn.onclick=sop;
        };

        let sop = () =>{
            i=0;
            f2.style.display="none";
            f3.style.display="inline-block";
            f3.style.float="none";
            let nop = document.getElementById('nop').value;
            console.log('hello');
            for(i=0;i<nop;i++){
             //debugger
             op = document.getElementById(`op${i}`).value;   
             console.log(i);
            console.log(op);
             let a = document.createElement("input");
             let br = document.createElement("br");
             let lab = document.createElement("label");
             a.type="radio";
             a.name="nm";
             a.value= document.getElementById(`op${i}`).value;
             f3.appendChild(a);
             f3.appendChild(lab);
             f3.appendChild(br);
             lab.innerHTML=`${a.value}`;
             document.getElementById("tit").innerHTML=document.getElementById('ntit').value;
            } 
            let btn = document.createElement("button");
            btn.innerHTML="publish";
            f3.appendChild(btn);
            
          }
     </script>
     </body>
</html>