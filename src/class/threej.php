<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Web3\Web3;

require __DIR__.'/../config.php';
require ROOTDIR.'class/db.php';
require ROOTDIR.'vendor/autoload.php';


/**
 * @version 0.1
 * @copyright www.threej.in
 */
class threej extends threejdb{
    private 
        $captcha,
        $salt;

    public
        $web3,
        $eth,
        $personal;
    
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
            $this->captcha->setMaxFrontLines(0);
            $this->captcha->setMaxBehindLines(2);
            $this->captcha->setBackgroundColor(250,250,250);
            // $this->captcha->setsty;
            $this->captcha->build(150,40,ROOTDIR.'vendor\gregwar\captcha\src\Gregwar\Captcha\Font\captcha4.ttf');
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
     * - c for ascii characters
     * - d for digits
     * - w for words/characters, digits and underscore
     * - @ for special char
     * - !c to exclude ascii characters
     * - !d to exclude digits
     * - !w to exclude words/characters/digits
     * - !@ to exclude special characters
     * - email to validate email address
     * @param string $not_allowed_char list of character that are not allowed
     * @return boolean
     */
    function strValidate($str, $option=''){
        if(!is_string($str)){
            return false;
        }
        if($option != ''){
            switch($option){
                case 'email':
                    return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i', $str);
                break;
                case 'c':
                    return preg_match('/^[a-z|A-Z]+$/',$str);
                break;
                case 'd':
                    return preg_match('/^[0-9]+$/',$str);
                break;
                case 'w':
                case '!@':
                case '!w':
                    return preg_match('/^\w+$/',$str);
                break;
                case '@':
                    return preg_match('/^\W+$/',$str);
                break;
                
                case '!c':
                    return preg_match('/^[\W0-9_]+$/',$str);
                break;
                case '!d':
                    return preg_match('/^\D+$/',$str);                    
                break;
            }
        }
    }

    public function sendmail($to, $subject = "test", $body = "test"){
        $mail = new PHPMailer();
        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'threej.in';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'noreply@threej.in';                     //SMTP username
            $mail->Password   = 'palji10dra@123';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('noreply@threej.in', 'BBVS');
            $mail->addAddress($to);     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @param string $url - Rpc url
     */
    public function web3Connect($url = "http://localhost:8545")
    {
        $this->web3 =  new Web3($url);
        $this->eth = $this->web3->eth;
        $this->personal = $this->web3->personal;
        return true;
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