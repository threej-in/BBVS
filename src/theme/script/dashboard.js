var fd = new FormData();
$(
    //add event to change the color of sidebar list on click
    ()=>{
        $('div.sidebar ul li').on('click',(e)=>{
            $('div.sidebar ul li').removeClass('active');
            $(e.target).addClass('active');
            loading();
        })
        if($_GET ['show']){
            $('#'+$_GET['show']).click()
        }
    }
)
//"loading" spinner
function loading(){
    $('#contentArea').html('<img style="margin-left: calc(100% - 22em);" width="100px" src="theme/img/loading.gif">');
}

function showContent(request){
    $.post(
        'ajax/user.php',
        { req : request },
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

function removeUser(e){
    uid = $(e).attr('data-uid')
    if(uid > 0){
        if(confirm('Are you sure you want to remove this user?')){
            payload = {
                req : 'removeUser',
                uid : uid
            }
            $.ajax({
                url: 'ajax/user.php',
                type: 'post',
                data: payload,
                success: (r)=>{
                    r = JSON.parse(r)
                    if(r['result']){
                        alert(r['message'])
                        $('button[data-uid='+uid+']').parent().parent().remove()
                    }else{
                        alert(e['error'])
                    }
                    
                }
            })
        }
    }
}

function updateUser(e){
    var uid = $(e).attr('data-uid');
    var status = $(e).parent().parent().find('td select[name=status]')[0].value;
    var role = $(e).parent().parent().find('td select[name=role]')[0].value;

    if(uid > 0){
        payload = {
            req : 'updateUser',
            uid : uid,
            status: status,
            role: role
        }
        $.ajax({
            url: 'ajax/user.php',
            type: 'post',
            data: payload,
            success: (r)=>{
                console.log(r)
                r = JSON.parse(r)
                if(r['result']){
                    alert(r['message'])
                }else{
                    alert(e['error'])
                }
            }
        })
    }
}

function deleteAccount(e){
    if(confirm('Are you sure you want to delete your account permanently? All the polls created by you will be deactivated and you will not be able to manage those polls.')){
        $.ajax({
            url : 'ajax/user.php',
            type : 'post',
            data : {
                req : 'deleteAccount'
            },
            success : (r)=>{
                r = JSON.parse(r)
                if(r['result']){
                    alert('Account deleted');
                    location.href = 'index.php';
                }
            }

        })
    }
}
