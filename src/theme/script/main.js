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
/**
 * 
 * @param {any} element 
 * @returns boolean
 */
function isset(element){
    return (typeof(element) != 'undefined') ? true : false;
}

//intializing smart contract
var abi = [
    {
        "inputs": [
            {
                "internalType": "uint256",
                "name": "_pid",
                "type": "uint256"
            }
        ],
        "name": "endPoll",
        "outputs": [
            {
                "internalType": "bool",
                "name": "",
                "type": "bool"
            }
        ],
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "inputs": [
            {
                "internalType": "string",
                "name": "_title",
                "type": "string"
            },
            {
                "internalType": "string[]",
                "name": "_options",
                "type": "string[]"
            }
        ],
        "name": "newPoll",
        "outputs": [
            {
                "internalType": "uint256",
                "name": "pollId",
                "type": "uint256"
            }
        ],
        "stateMutability": "payable",
        "type": "function"
    },
    {
        "inputs": [
            {
                "internalType": "uint256",
                "name": "_pid",
                "type": "uint256"
            },
            {
                "internalType": "uint16",
                "name": "periodInDays",
                "type": "uint16"
            }
        ],
        "name": "startPoll",
        "outputs": [
            {
                "internalType": "bool",
                "name": "",
                "type": "bool"
            }
        ],
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "inputs": [
            {
                "internalType": "address",
                "name": "_newAddress",
                "type": "address"
            }
        ],
        "name": "stopContract",
        "outputs": [
            {
                "internalType": "bool",
                "name": "",
                "type": "bool"
            }
        ],
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "stateMutability": "payable",
        "type": "receive"
    },
    {
        "inputs": [
            {
                "internalType": "uint256",
                "name": "_pid",
                "type": "uint256"
            }
        ],
        "name": "getPollOptions",
        "outputs": [
            {
                "internalType": "string[]",
                "name": "options",
                "type": "string[]"
            },
            {
                "internalType": "uint64[]",
                "name": "voteCount",
                "type": "uint64[]"
            }
        ],
        "stateMutability": "view",
        "type": "function"
    },
    {
        "inputs": [],
        "name": "newContractAddress",
        "outputs": [
            {
                "internalType": "address",
                "name": "",
                "type": "address"
            }
        ],
        "stateMutability": "view",
        "type": "function"
    },
    {
        "inputs": [],
        "name": "owner",
        "outputs": [
            {
                "internalType": "address",
                "name": "",
                "type": "address"
            }
        ],
        "stateMutability": "view",
        "type": "function"
    },
    {
        "inputs": [
            {
                "internalType": "uint256",
                "name": "",
                "type": "uint256"
            }
        ],
        "name": "polls",
        "outputs": [
            {
                "internalType": "string",
                "name": "title",
                "type": "string"
            },
            {
                "internalType": "address",
                "name": "createdBy",
                "type": "address"
            },
            {
                "internalType": "enum BBVS.Status",
                "name": "status",
                "type": "uint8"
            },
            {
                "internalType": "uint64",
                "name": "time",
                "type": "uint64"
            },
            {
                "internalType": "uint16",
                "name": "periodInDays",
                "type": "uint16"
            }
        ],
        "stateMutability": "view",
        "type": "function"
    },
    {
        "inputs": [],
        "name": "pollsCount",
        "outputs": [
            {
                "internalType": "uint256",
                "name": "",
                "type": "uint256"
            }
        ],
        "stateMutability": "view",
        "type": "function"
    }
];
var contractAddress = "0x4E28EF77c0b049d56e66Aa8afAcf63d7Ed03E6f8";
$( () => {
    
    if(isset(Web3)){
        var web3 = new Web3("http://localhost:8545" || Web3.givenProvider);
        bbvs = new web3.eth.Contract(abi, contractAddress);
    }
})