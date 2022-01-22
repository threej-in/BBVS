var fd = new FormData();

$(
    //add event to change the color of sidebar list on click
    ()=>{
        $('div.sidebar ul li').on('click',(e)=>{
            $('div.sidebar ul li').removeClass('active');
            $(e.target).addClass('active');
            loading();
        })
    }
)
//"loading" spinner
function loading(){
    $('#contentArea').html('<img style="margin-left: calc(100% - 22em);" width="100px" src="theme/img/loading.gif">');
}

function showProfile(){
    $.post(
        'ajax/user.php',{ req : 'profile' },
        (result)=>{
            $('#contentArea').html(result);
        }
    )
}

function validateImage(img, upload = false){
    img = img[0]
    if(typeof img === 'object'){
        if(img.size > 100000){
            alert('Image size should be under 100kb');
            return
        }
        if(!['image/jpeg','image/jpg','image/png'].includes(img.type)){
            alert('Please choose a valid image file.');
            return
        }
        $('img[data-id=profilepic]').attr('src',URL.createObjectURL(img));
        
        fd.append('file', img);
        fd.append('req','uploadImage');
        return
    }

    if(upload && fd.get('req') == 'uploadImage'){
        $.ajax({
            url : 'ajax/user.php',
            type: 'post',
            contentType: false,
            processData:false,
            data : fd,
            success: function(result){
                r = JSON.parse(result)
                if(r['result']){
                    alert(r['message']);
                }else{
                    alert(r['error'])
                }
            },
            error: function(error) {
                alert('Failed to uplaod image!')
                console.log(error);
            }
        });
        return;
    }else{
        alert('Please choose an image before uploading');
    }
    
}

function updateProfile(){
    fname = $('input[name=fname]')[0].value
    question = $('select[name=securityQuestion]')[0].value
    answer = $('input[name=securityAnswer]')[0].value
    payload = {
        req : 'updateProfile',
        name : fname,
        securityQuestion : question,
        securityAnswer : answer
    }
    $.ajax({
        url : 'ajax/user.php',
        type : 'post',
        data : payload,
        success : (result)=>{
            r = JSON.parse(result)
            if(r['result']){
                alert(r['message']);
            }else{
                alert(r['error'])
            }
        },
        error : (error)=>{
            console.log(error)
        }
    })
}