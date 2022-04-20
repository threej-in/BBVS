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

async function submitNewPoll(e){
    
    fd = new FormData($('#newPoll')[0]);
    
    if(!fd.get('pollTitle')){ alert('Poll title is required'); return}
    if(!fd.get('pollImage')){ alert('Poll image is required'); return}
    if(!fd.get('option1') || !fd.get('option2')){ alert('Atleast 2 options are required'); return}

    $(e).prop('disabled',true);
    $(e).text('Sign this transaction.');
    setTimeout(() => {
        $(el).after('<p class="red">Processing your request do not press back button.</p>');
    }, 1000);

    web3Connection().then(async ()=>{
        userAccounts = await ethereum.request({method:'eth_requestAccounts'});

        if(typeof bbvs != 'undefined'){
            var pollTitle = fd.get('pollTitle');
            var pollOptions = [];
            for (let index = 1; index <= 10; index++) {
                if(!fd.get('option'+index)) break;

                pollOptions.push(fd.get('option'+index));
            }
            //send data to blockchain
            data = bbvs.methods.newPoll(pollTitle,pollOptions)
            .send({ from: ethereum.selectedAddress })
            .then(async (receipt)=>{
                console.log(receipt);
                $(e).text('Processing your request');
                pid = await bbvs.methods.pollsCount().call();
                
                //Write poll data to server
                fd.append('req','createNewPoll');
                fd.append('txhash',receipt.transactionHash);
                fd.append('pid',pid);
                callAjax(
                    AJAXURL,
                    fd,
                    (result)=>{
                        r = JSON.parse(result);
                        if(r['result']){
                            alert(r['message'])
                            $('#mypolls').click()
                        }else{
                            console.log(r);
                        }
                    }
                )
            }).catch((err)=>{
                $(e).prop('disabled',false);
                $(e).text('Retry.');
                if(err.code == 4001){
                    alert('Transaction failed, user denied request.');
                }else{
                    console.log(err);
                    alert('Internal error occured!');
                }
            })
        }else{
            alert('Unable to establish connection with blockchain');
        }
        return;
    })
}

async function modifyPoll(el, action, pid){
    payload = {
        req : 'modifyPoll',
        pid : pid,
        action : action
    };
    const period = $(el).prev().val();
    if(action == 'publish'){
        if(0 == period){
            alert('Invalid period selected');
            return
        }
        payload['period'] = period
        web3Connection().then(()=>{
            $(el).text('Sign this transaction.');
            $(el).prop('disabled',true);
            setTimeout(() => {
                $(el).after('<p class="red">Processing your request do not press back button.</p>');
            }, 1000);
    
            bbvs.methods.startPoll($(el).attr('data-bpid'), period)
            .send({from: ethereum.selectedAddress})
            .then((receipt)=>{
                payload['txhash'] = receipt.transactionHash;
                
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
            })
            .catch(err=>{
                $(el).prop('disabled',false);
                $(el).text('Retry.');
                alert('Transaction Failed or reverted!');
                console.log(err);
            })
        })    
    }else{
        web3Connection().then(()=>{
            $(el).text('Sign this transaction.');
            $(el).prop('disabled',true);
            setTimeout(() => {
                $(el).after('<p class="red">Processing your request do not press back button.</p>');
            }, 1000);

            bbvs.methods.endPoll($(el).attr('data-bpid'))
            .send({from: ethereum.selectedAddress})
            .then((receipt)=>{
                payload['txhash'] = receipt.transactionHash;
                
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
            })
            .catch(err=>{
                $(el).prop('disabled',false);
                $(el).text('Retry.');
                alert('Transaction Failed or reverted!');
                console.log(err);
            })
        })
    }

    
}

function removePoll(e, userpoll = false){
    pid = $(e).attr('data-pid')
    if(pid > 0){
        if(confirm('Are you sure you want to remove this poll?')){
            callAjax(
                AJAXURL,
                {
                    req : 'removePoll',
                    pid : pid
                },
                (r)=>{
                    r = JSON.parse(r)
                    if(r['result']){
                        alert(r['message'])
                        if(userpoll)
                            $('button[data-pid='+pid+']').parent().remove()
                        else
                            $('button[data-pid='+pid+']').parent().parent().remove()
                    }else{
                        alert(e['error'])
                    }
                }
            )
        }
    }
}

function updatePoll(e){
    var pid = $(e).attr('data-pid');
    var status = $(e).parent().parent().find('td select[name=status]')[0].value;
    if(pid > 0){
        payload = {
            req : 'updatePoll',
            pid : pid,
            status: status
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