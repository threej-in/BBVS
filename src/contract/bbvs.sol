// SPDX-License-Identifier: GPL-3.0
pragma solidity >=0.8.0;

//["BJP","AAP","NOTA"]
// import "hardhat/console.sol";
import "./bbvsbase.sol";

contract BBVS is inheritingBase{
    enum Status {
        Inactive,
        Active
    }
    
    struct poll {
        string title;
        address createdBy;
        string[] options;
        uint64[] voteCount;
        Status status;
        uint64 time;
        uint16 periodInDays;
    }

    struct voting{
        address voter;
        uint pollId;
        uint8 option;
        uint userUniqueId;
    }

    mapping(uint => poll) public polls;
    uint public pollsCount = 0;
    
    /*
    * @param _title string - Poll title
    * @param _options string - Specify upto 10 options for poll 
    */
    function newPoll(string memory _title, string[] memory _options) public payable returns(
        uint pollId
    ){
        //creating poll requires fees of 0.0001 ether
        // require(msg.value > 0.0001 ether,"Fees paid is insufficient to create a new poll");
        //You can specify up to 10 options
        require(_options.length < 10, "Only 10 options are allowed!");

        uint count = pollsCount; //read from state
        count++;
        poll storage newpoll = polls[count]; // reference
        newpoll.title = _title; //write

        for(uint8 _i = 0; _i < _options.length; _i++){
            newpoll.options.push(_options[_i]); //write
            newpoll.voteCount.push(uint64(0)); //write
        }
        
        newpoll.createdBy = msg.sender; //write
        newpoll.status = Status.Inactive; //write
        newpoll.time = uint64(block.timestamp); //write
        newpoll.periodInDays = uint16(0); //write
        pollsCount = count; //write
        return (count);
    }

    /*
    *
    */
    function getPollOptions(uint _pid) public view validatePollId(_pid) returns(
        string[] memory options,
        uint64[] memory voteCount
    ){
        return (
            polls[_pid].options,
            polls[_pid].voteCount
        );
    }

    function startPoll(uint _pid, uint16 periodInDays) public validatePollId(_pid) validateAuthor(_pid) returns (bool) {
        
        require(periodInDays > 0 && periodInDays < 366,"Period not in range, Please enter value greater then 0 and less than 366");
        
        polls[_pid].status = Status.Active;
        polls[_pid].time = uint64(block.timestamp);
        polls[_pid].periodInDays = periodInDays;
        return true;
    }

    function endPoll(uint _pid) public validatePollId(_pid) validateAuthor(_pid) returns(bool){
        polls[_pid].status = Status.Inactive;
        return true;
    }

    modifier validatePollId(uint _pid) {
        require(_pid > 0 && _pid <= pollsCount, "Id is invalid, poll not found");
        _;
    }
    modifier validateAuthor(uint _pid){
        require(msg.sender == polls[_pid].createdBy,"You are not authorized");
        _;
    }
}