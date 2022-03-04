// SPDX-License-Identifier: GPL-3.0
pragma solidity >= 0.8.0;

contract inheritingBase {
    address public owner;
    bool internal contractStatus;
    //You can get the new contract address from the below variable 
    //default is self address
    address public newContractAddress;

    constructor () {
        owner = msg.sender;
        contractStatus = true;
        newContractAddress = address(this);
    }

    modifier isActive() {
        require(contractStatus, "Smart contract outdated!");
        _;
    }

    modifier onlyOwner() {
        require(msg.sender == owner);
        _;
    }

    function stopContract(address _newAddress) public onlyOwner isActive returns(bool){
        contractStatus = false;

        if(newContractAddress == _newAddress) return false;

        newContractAddress = _newAddress;

        payable(owner).transfer(address(this).balance);
        return true;
    }

    receive() external payable {}
}