<?php
    if(isset($_POST['req'])){
        require __DIR__.'/../class/threej.php';
        require __DIR__.'/../class/settings.php';
        switch($_POST['req']){
            case 'securityQuestion':
                if(!isset($_POST['loginid'])){
                    echo false;
                    return;
                }
                $loginid = $_POST['loginid'];
                $t->query('SELECT SECURITYQUESTION FROM BBVSUSERTABLE WHERE USERNAME LIKE ? OR EMAIL LIKE ?',[
                    [&$loginid,'s'],
                    [&$loginid,'s']
                ]);
                if($t->execute() != false && $t->affected_rows == 1){
                    
                    print_r(
                        json_encode(
                            [
                                'question' => SETTINGS::securityQuestion[($t->fetch())['SECURITYQUESTION']]
                            ]
                        )
                    );
                    return;
                }
                echo false;
            break;
        }
    }
?>