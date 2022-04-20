var $_GET = {};
$(()=>{
    location.search != '' ? location.search.split('?')[1].split('&').forEach((e)=>{
        $_GET[e.split('=')[0]] = e.split('=')[1]
    }) : 0;
})

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
 * @returns boolean
 */
function emptyOrNull(el){
	return el.trim() == '' || el == NULL ? true : false;
}
/**
 * 
 * @param {any} element 
 * @returns boolean
 */
function isset(element){
    return (typeof(element) != 'undefined') ? true : false;
}
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

//Functions related to current website

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

function isEmpty(element){
    if($('#'+element).val().trim() == ''){
        $('#'+element+'_err').show()
        $('#'+element+'_err').text(element + ' is required')
        return true
    }
    $('#'+element+'_err').hide()
    return false
}

web3 = {}
$( async () => {
    
    if(isset(Web3)){
		try {
			//intializing smart contract
			const abi = [
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
					"inputs": [
						{
							"internalType": "uint256",
							"name": "_pid",
							"type": "uint256"
						},
						{
							"internalType": "string",
							"name": "_email",
							"type": "string"
						},
						{
							"internalType": "uint256",
							"name": "_option",
							"type": "uint256"
						},
						{
							"internalType": "string",
							"name": "_secretPhrase",
							"type": "string"
						}
					],
					"name": "vote",
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
			]
			const contractAddress = "0x6A249b353F1b7b9728a5Ac5324465273479b57B7";
			const from = "0xFC43025223C4305db566f11c0b661AA4bAe1A668";
			
			web3 = new Web3(Web3.givenProvider || "http://localhost:8545");
			web3.eth.handleRevert = true
			bbvs = new web3.eth.Contract(abi, contractAddress);
		} catch (error) {
			console.log(error);
		}
    }
})

/**
 * Checks if metamask is installed and connected to local ethereum test network
 * 
 * @returns boolean
 */
async function web3Connection(){
	//kovan test network
	const targetNetworkId = '0x2a';
	if(typeof window.ethereum != 'undefined' && ethereum.isMetaMask){
		if (ethereum.chainId != targetNetworkId) {
			await ethereum.request({
				method: 'wallet_switchEthereumChain',
    			params: [{ chainId: targetNetworkId }],
			}).catch(()=>{
				alert('Unknown network is selected. Please allow to switch network & retry your last action after refreshing the page.')
			})
		}
		return true;
	}else{
		alert('Install metamask extension to continue');
		return false;
	}

}