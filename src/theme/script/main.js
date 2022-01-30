$_GET = {}
$(()=>{
    location.search != '' ? location.search.split('?')[1].split('&').forEach((e)=>{
        $_GET[e.split('=')[0]] = e.split('=')[1]
    }) : 0;
})

//not allow  user to enter the special charecters only '@' '.' are allow// 
$('#loginid').on('keypress', function (event) {
    var regex = new RegExp("^[a-zA-Z0-9@.]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
        event.preventDefault();
         $('#id_err').text('special char not allowed');
        return false;
    }
});
/**
 * 
 * @param {string} option Send options as specified below
 * - c for ascii characters
 * - d for digits
 * - w for words/characters, digits and underscore
 * - @ for special char
 * - !c to exclude ascii characters
 * - !d to exclude digits
 * - !w to exclude words/characters/digits
 * - !@ to exclude special characters
 * - email to validate email
 * - special to validate special charaters
 * - emt to check the there is data or not
*/
function validateString(str, option){
    switch(option){
        case 'c':
            r = new RegExp(/^[a-z|A-Z]+$/,'g');
            return r.test(str);
        break;
        case 'd':
            r = new RegExp(/^[0-9]+$/,'g');
            return r.test(str);
        break;
        case 'w':
        case '!@':
        case '!w':
            r = new RegExp(/^\w+$/,'g');
            return r.test(str);
        break;
        case '@':
            r = new RegExp(/^\W+$/,'g');
            return r.test(str);
        break;
        
        case '!c':
            r = new RegExp(/^[\W0-9_]+$/,'g');
            return r.test(str);
        break;
        case '!d':
            r = new RegExp(/^\D+$/,'g');
            return r.test(str);
        break;
        case 'email':
            r = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
            return r.test(str);
        break;  
    }
    return false;
}
function isEmpty(element){
    if($('#'+element).val().trim() == ''){
        $('#'+element+'_err').show()
        $('#'+element+'_err').text(element + ' is required')
        return true
    }
    $('#'+element+'_err').hide()
    return false
}
/**
 * 
 * @param {string} url 
 * @param {Object|FormData} data 
 * @param {callback} success 
 * @param {callback} error 
 * @returns 
 */
function callAjax(url, data, success, error = {}){
    if(!(data instanceof FormData)){
        if(!(data instanceof Object)) return false;

        fd = new FormData
        for (key in data) { fd.append(key, data[key]) }
        data = fd
    }
    $.ajax({
        url : url,
        type : 'post',
        data : data,
        contentType: false,
        processData: false,
        cache: false,
        timeout: 800000,
        success : success,
        error : error
    })
}