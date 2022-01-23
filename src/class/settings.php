<?php
//enum for user role
abstract class USERROLE{
    const ADMIN = 2;
    const MODERATOR = 3;
    const USER = 4;
}

abstract class USERSTATUS{
    const ACTIVE = 2;
    const ONHOLD = 3;
    const BANNED = 4;
}

abstract class SETTINGS{
    const securityQuestion = [
        "Choose question",
        "In what city were you born?",
        "what is the name of your favourite animal?",
        "What is your mother's maiden name?",
        "What high school did you attend?",
        "What is the name of your first school?",
        "What was the make of your first car?",
        "Where did you meet your spouse?",
        "What was the first movie you show in theater?",
        "Who is your favourite actor / actress?",
        "What is your favourite Olympic sport?"
    ];
}
?>