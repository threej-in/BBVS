var AJAXURL = 'ajax/user.php';
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
    callAjax(
        AJAXURL,
        { req : request },
        (result)=>{
            $('#contentArea').html(result);
        }
    )
}

function validateImage(fileInput, upload = false, id ='profilepic', size = 100){
    img = upload ? false : fileInput.files[0];
    if(typeof img === 'object'){
        if(img.size > size*1024){
            alert('Image size should be under 100kb');
            $(fileInput).val('')
            return false;
        }
        if(!['image/jpeg','image/jpg','image/png'].includes(img.type)){
            alert('Please choose a valid image file.');
            $(fileInput).val('')
            return false;
        }
        $(`img[data-id=${id}]`).attr('src',URL.createObjectURL(img));
        $(`img[data-id=${id}]`).show()
        fd.append('file', img);
        fd.set('req','uploadImage');
        return
    }

    if(upload && fd.get('req') == 'uploadImage'){
        callAjax(
            AJAXURL,
            fd,
            (result)=>{
                r = JSON.parse(result)
                alert(r['message']);
            },
            (error)=>{
                alert('Failed to uplaod image!')
                console.log(error);
            }
        );
        return;
    }else{
        alert('Please choose an image before uploading');
    }
    
}

function updateProfile(){
    fname = $('input[name=fname]')[0].value
    question = $('select[name=securityQuestion]')[0].value
    answer = $('input[name=securityAnswer]')[0].value
    
    callAjax(
        AJAXURL,
        {
            req : 'updateProfile',
            name : fname,
            securityQuestion : question,
            securityAnswer : answer
        },
        (result)=>{
            r = JSON.parse(result)
            if(r['result']){
                alert(r['message']);
            }else{
                alert(r['error'])
            }
        }
    )
}

function removeUser(e){
    uid = $(e).attr('data-uid')
    if(uid > 0){
        if(confirm('Are you sure you want to remove this user?')){
            callAjax(
                AJAXURL,
                {
                    req : 'removeUser',
                    uid : uid
                },
                (r)=>{
                    r = JSON.parse(r)
                    if(r['result']){
                        alert(r['message'])
                        $('button[data-uid='+uid+']').parent().parent().remove()
                    }else{
                        alert(e['error'])
                    }
                }
            )
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
        callAjax(
            AJAXURL,
            payload,
            (r)=>{
                console.log(r)
                r = JSON.parse(r)
                if(r['result']){
                    alert(r['message'])
                }else{
                    alert(r['message'])
                }
            }
        )
    }
}

function deleteAccount(e){
    if(confirm('Are you sure you want to delete your account permanently? All the polls created by you will be deactivated and you will not be able to manage those polls.')){
        fd.append('req','deleteAccount')
        callAjax(
            AJAXURL,
            fd,
            (r)=>{
                r = JSON.parse(r)
                if(r['result']){
                    alert('Account deleted');
                    location.href = 'index.php';
                }
            }
        )
    }
}

function submitNewPoll(){
    fd = new FormData($('#newPoll')[0]);
    fd.append('req','createNewPoll');
    callAjax(
        AJAXURL,
        fd,
        (result)=>{
            r = JSON.parse(result);
            alert(r['message'])
            if(r['result']){
                $('#mypolls').click()
            }
        }
    )
}

function modifyPoll(el, action,pid){
    payload = {
        req : 'modifyPoll',
        pid : pid,
        action : action
    };
    if(action =='publish'){
        period = $(el).prev().val();
        if(0 == period){
            alert('Invalid period selected');
            return
        }
        payload['period'] = period
    }
    callAjax(
        AJAXURL,
        payload,
        (result)=>{
            r = JSON.parse(result);
            alert(r['message']);
            if(r['result']){
                $('#mypolls').click()
            }
        }
    )
}
