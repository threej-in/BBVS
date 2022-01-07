
/*var menu = document.querySelector(".mnu");
menu.addEventListener("click",function(){
    document.querySelector("body").classList.toggle("active");  
})*/
jQuery("#login").validate({
    rules:{
        loginid:{
            required:true
                    
        },
        password:{
            minlength:8
        }
    },
    messages:{
        loginid:{
            required:"Enter name"
        },
        password:{
            minlength:"more then 8"

        }
    },
    submitHandler:function(form){
        form.submit();
    }
});
//not allow  use to enter the special charecters// 
$('#loginid').on('keypress', function (event) {
var regex = new RegExp("^[a-zA-Z0-9]+$");
var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
if (!regex.test(key)) {
    event.preventDefault();
    return false;
}
});
