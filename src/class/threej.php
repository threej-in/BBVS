<?php
require __DIR__.'/../config.php';
require ROOTDIR.'class/db.php';
require ROOTDIR.'class/Gregwar/Captcha/CaptchaBuilder.php';
/**
 * @version 0.1
 * @copyright www.threej.in
 */
class threej extends threejdb{
    private 
        $captcha,
        $salt;

    public 
    
    function __construct() {

        $this->salt = $GLOBALS['SALT'];
        if(session_status() < 2) session_start();
        //error reporting
        DEBUGGING ? error_reporting(E_ALL) : error_reporting(0);
        //intiate captcha class
        if(extension_loaded('gd')) $this->captcha = new \Gregwar\Captcha\CaptchaBuilder();
        //initiate new db class
        if(false === $this->newDatabaseConnection()){
            $this->error($this->dberror,'Database connection failed');
            die;
        }
    }
    /**
     * @param string $str String for adding salt to it
     * @param integer $start_or_end specify the location where you want to add the salt
     * - 1 for start [default]
     * - 2 for end 
     * - 3 for adding salt on both end
     * @return string|false Salted string or false if wrong positon choosed.
     */
    function addSalt($str, $start_or_end=1){
        switch($start_or_end){
            case 1:
                return $this->salt . $str;
            break;
            case 2:
                return $str . $this->salt;
            break;
            case 3:
                return $this->salt . $str . $this->salt;
            break;
            default:
                return false;
        }
    }
    /**
     * error handler
     * 
     */
    function error(string $err, string $err_for_user, mixed $return_value=false){
        if(DEBUGGING){
            $this->print($err);
        }else{
            ob_start();
            debug_print_backtrace(2);
            $backtrace = ob_get_clean();
            echo "<p class=\"user_error\">$err_for_user</p>";
            error_log(PHP_EOL.date('[H:i:s - d/M/Y] ' ,time()).strval($err).PHP_EOL.$backtrace,3,ROOTDIR.'errorlog.txt');
        }
        return $return_value;
    }
    /**
     * Captcha
     * @return string|false base64 string of image or false on failure
     */
    function getCaptcha(){
        try{
            $this->captcha->build();
            $_SESSION['captcha'] = $this->captcha->getPhrase();
            return $this->captcha->inline();
        }catch(Exception $e){
            $this->print('failed');
            return false;
        }
        
    }
    /**
     * Captcha validation function
     * @param string $str - Phrase entered by user.
     */
    function validateCaptcha($str){
        return preg_match('/'.$_SESSION['captcha'].'/i', $str);
    }
    
    //see output
    function print($data){
        ob_start();
        debug_print_backtrace(2);
        $backtrace = ob_get_clean();

        if(!DEBUGGING) return;
        echo '<pre style="position: fixed;height: 35%;width:50%;left:0;bottom:0;overflow: scroll;background-color: white;width: 100%;z-index: 100000;border: 2px solid;padding: 20px;font-size:16px;color:black;resize:both;">';
        var_dump($data);
        print_r($backtrace);
        echo '</pre>';
    }
    /**
     * string validation
     * @param string $str string to validate
     * @param string|array $allowed_char Array of named constants for allowed characters
     * - alphanum for a-zA-Z0-9
     * - alpha for a-z
     * - ALPHA for A-Z
     * - num for 0-9
     * - @ for special characters
     * - email to validate email address
     * @param string $not_allowed_char list of character that are not allowed
     * @return boolean
     */
    function strValidate($str, $allowed_char='', $not_allowed_char=''){
        $result = false;
        if(!is_string($str)){
            return false;
        }
        if($allowed_char != ''){
            switch($allowed_char){
                case 'email':
                    return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i', $str);
                break;
            }
        }
        if($not_allowed_char != ''){
            switch($not_allowed_char){
                case '@':
                    return !preg_match('/[\W]+/', $str);
                break;
            }
        }
        
    }
}

//initialization of threej class
$t = new threej();

unset($SERVER);
unset($DBUSER);
unset($DBPASS);
unset($DATABASE);
unset($SALT);

?>