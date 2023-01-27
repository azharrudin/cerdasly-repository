<?php
require_once(__DIR__."/htmlpurifier.php");
require_once(__DIR__."/tools/libtools.php");
require_once(__DIR__."/tools/uplib/uplib.php");
require_once(__DIR__."/core/comment.php");
require_once(__DIR__."/core/account.php");
require_once(__DIR__."/functions.php");
require_once(__DIR__."/../config/config.php");
$date        = gmdate("d-m-y H:i:s");
$userimgdir  = __DIR__."/../data/userimg/";
function generator( $length, $chars = "0123456789"){
    $size = strlen($chars);
    $str  = "";
    for( $i = 0; $i < $length; $i++ ){
        $str .= $chars[ rand(0, $size-1) ];
    }
    return $str;

}
function buildregex($query){
    $m     =  explode(' ', trim($query));
    $regex = ""; 
    foreach($m as $v){
        $regex .= "(?=.*".preg_quote($v).")";
    }
    return $regex;
}
class Core {
    public $m;
    public $attachment;
    public $uplib;
    public $libtools;

    /**
     * construct() - untuk membangun konstruksi kelas Core
     */
    function __construct(){
        $username = PDO_USERNAME;
        $password = PDO_PASSWORD;
        try {
            $this->m = new PDO("mysql:host=localhost;dbname=".PDO_DBNAME , $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->m->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: ".$e->getMessage());
        }
        $this->libtools = new Libtools();
        $this->uplib = new UPLib();
        $this->attachment = $this->libtools->UploadAttachmentTools();
        
    }
    use commentAction;
    use accountAction;
    /**
     * addAnswer() - untuk menambah jawaban
     * $answer     -> jawaban pengguna (3000 karakter) [STRING]
     * $username   -> username pengguna (15 huruf) [STRING]
     * $questionid -> id pertanyaan [STRING]
     */
    function addAnswer($answer, $username, $questionid){
        global $date;
        $id         = generator(12);
        $test       = $this->m->query("SELECT * FROM answers WHERE id LIKE ".$this->m->quote($id))->fetchAll();
        if(count($test) > 0)
            $this->addAnswer($answer, $username, $questionid);
        else if(count($this->getAnswers($questionid)) <= 4 && strlen($answer) < 2000000){
            $answer = $this->m->quote(htmlpurify($answer));
            $attachment_try     = $this->attachment->upload_answer_attachment($id);
            $attachment_success = !is_array($attachment_try) ? $attachment_try : "none";
            $this->m->query("
                INSERT INTO answers(
                    id,
                    votes,
                    questionid,
                    postdate,
                    username,
                    answer,
                    attachment
                ) VALUES('$id', '0', '$questionid', '$date', '$username', $answer, '$attachment_success');
            ");
            return $attachment_success;
        }
        return false;
    }
    /**
     * addQuestion() - untuk menambah pertanyaan
     * $text       -> teks pertanyaan  (1000 karakter) [STRING]
     * $username   -> username pengguna (15 huruf) [STRING]
     * $category   -> kategori pertanyaan [STRING]
     */
    function addQuestion($text, $username, $category){
        global $date;
        $category = strtolower($category);
        $title    = $this->m->quote(htmlpurify($text));
        $id       = generator(12);
        $test   = $this->m->query("SELECT * FROM questions WHERE id LIKE ".$this->m->quote($id))->fetchAll();
        if(count($test) > 0)
            $this->addQuestion($text, $username, $category);
        if(strlen($text) < 2000000){
            $attachment_try     = $this->attachment->uploadQuestionAttachment($id);
            $attachment_success = !is_array($attachment_try) ? $attachment_try : "none";
            $this->m->exec("
                INSERT INTO questions(
                    id,
                    title,
                    category,
                    postdate,
                    views,
                    username,
                    attachment
                ) VALUES('$id', $title, '$category', '$date', 0, '$username', '$attachment_success');
            ");
            return $attachment_success;
        }
       
    }
    function addNotification($content, $username, $code, $actor){
        if(trim($username) == trim($actor))
            return;
        global $date;
        $id      = generator(12);
        $content = $this->m->quote($content);
        $code    = $this->m->quote($code);
        $actor   = $this->m->quote($actor);
        $testcode    = $this->m->query("SELECT COUNT(*) AS count FROM notifications WHERE code LIKE ".$code)->fetchAll();
        $testid      = $this->m->query("SELECT COUNT(*) AS count FROM notifications WHERE id LIKE ".$id)->fetchAll();
        if(intval($testcode[0]["count"]) == 0 && intval($testid[0]["count"]) == 0){
            $this->m->exec("
                INSERT INTO notifications(
                    id,
                    username,
                    postdate,
                    content,
                    code,
                    readed,
                    actor
                ) VALUES('$id', '$username', '$date', $content, $code, 0, $actor);
            ");
        }
        return $id;
    }
  
    function voteAnswer($answerid, $votes){ 
        $this->m->exec("UPDATE answers SET votes='$votes' WHERE id=".$this->m->quote($answerid));
    }
    function deleteQuestion($questionid){
        $m = $this->getAnswers($questionid);
        if(file_exists(CRDSLY_ATTACHMENT_QUESTIONS_DIR.$questionid.".png")){
            @unlink(CRDSLY_ATTACHMENT_QUESTIONS_DIR.$questionid.".png");
        }
        $questionid = $this->m->quote($questionid);
        $this->m->query("DELETE FROM questions WHERE id LIKE $questionid;");
        foreach($m as $c){
            $this->deleteAnswer($c["id"]);
            $comments = $this->getComments($c['id']);
            foreach($comments as $comment){
                $this->deleteComment($comment["id"]);
            }
        }
    }
    function updateAnswer($answerid, $text, $attachment = "none"){
        $attachment = $this->m->quote($attachment);
        $text = htmlpurify($text);
        $this->m->query("UPDATE answers SET answer=".$this->m->quote($text).", attachment=$attachment WHERE id LIKE '$answerid';");
        return $text;
    }
    function deleteAnswer($answerid){
        $answer_attachment =  $this->getAnswerByID($answerid)["attachment"];
        $comments = $this->getComments($answerid);
        $answerid = $this->m->quote($answerid);
        $this->m->query("DELETE FROM answers WHERE id LIKE $answerid;");
        if(file_exists(CRDSLY_ATTACHMENT_ANSWERS_DIR.$answer_attachment.".png")){
            @unlink(CRDSLY_ATTACHMENT_ANSWERS_DIR.$answer_attachment.".png");
        }
        foreach($comments as $comment){
            $this->deleteComment($comment["id"]);
        }
    }
    function getAnswerByID($id){
        $id = $this->m->quote($id);
        $r = $this->m->query("SELECT * FROM answers WHERE id LIKE $id;");
        $x = $r->fetch();
        if(gettype($x) == "boolean")
            return array();
        return $x;
    }
   
    function updateAboutByUsername($username, $about){ 
        $this->m->query("UPDATE users SET about=".$this->m->quote(strip_tags($about))." WHERE username LIKE ".$this->m->quote($username));
    }
    function notificationReadAll($username){ 
        $this->m->query("UPDATE notifications SET readed=1 WHERE username LIKE ".$this->m->quote($username));
    }
    function getAllNotifications($username, $limit_from = 0, $limit_to = 15){
        $r = $this->m->query("SELECT * FROM notifications WHERE username LIKE '$username' ORDER BY STR_TO_DATE(`postdate`,\"%d-%m-%y %H:%i:%s\") DESC LIMIT $limit_from, $limit_to;");
        $g = $r->fetchAll();
        if(gettype($g) == "boolean")
            return array();
        return $g;
    }
    function readNotification($id){
        $this->m->query("UPDATE notifications SET readed=1 WHERE id LIKE ".$this->m->quote($id));
    }
    function getUnreadNotifications($username){
        $r = $this->m->query("SELECT * FROM notifications WHERE (username LIKE '$username' AND readed=0) ORDER BY postdate ASC;");
        $g = $r->fetchAll();
        if(gettype($g) == "boolean")
            return array();
        return $g;
    }
    function getAnswers($questionid){
        $r = $this->m->query("SELECT * FROM answers WHERE questionid LIKE '$questionid' ORDER BY votes DESC, postdate ASC;");
        $g = $r->fetchAll();
        if(gettype($g) == "boolean")
            return array();
        return $g;
    }
    function getVoted($id){
        $r = $this->m->query("SELECT votes FROM answers WHERE id LIKE '$id';");
        $g = $r->fetch();
        if(gettype($g) == "boolean")
            return 0;
        return $g["votes"];
    }
    function getAnswersByUsername($username, $limit_from = 0 , $limit_to = 999){
        $r = $this->m->query("SELECT * FROM answers WHERE username LIKE '$username' ORDER BY `postdate` ASC LIMIT $limit_from, $limit_to ");
        $g = $r->fetchAll();
        if(gettype($g) == "boolean")
            return array();
        return $g;
    }
    function getAnswerCountByUsername($username){
        $r = $this->m->query(" SELECT count(*) As 'total' FROM answers WHERE username LIKE '$username';");
        $g = $r->fetchAll();
        if(gettype($g) == "boolean")
            return array();
        return $g;
    }
    function getBestAnswer($qid){
        $r = $this->m->query("SELECT * FROM answers WHERE questionid LIKE '$qid' ORDER BY votes DESC, postdate ASC LIMIT 1;");
        $g = $r->fetchAll();
        if(gettype($g) == "boolean")
            return array();
        if(isset($g[0]))
            return $g[0];
        else return $g;
    }
    function getQuestionsByUsername($username){
        $username=$this->m->quote($username);
        $r = $this->m->query("SELECT * FROM questions WHERE username LIKE $username;");
        $g = $r->fetchAll();
        if(gettype($g) == "boolean")
            return array();
        return $g;
    }
    function getUsername($email){
        $r = $this->m->query("SELECT username FROM users WHERE email LIKE '$email';");
        $x = $r->fetch();
        if(gettype($x) != "boolean")
            return $x["username"];
        return $x;
    }
    function getRealnameByEmail($email){
        $r = $this->m->query("SELECT realname FROM users WHERE email LIKE '$email';");
        $x = $r->fetch();
        if(gettype($x) != "boolean")
            return $x["realname"];
        return $x;
    }
    function getRealnameByUsername($username){
        $r = $this->m->query("SELECT realname FROM users WHERE username LIKE '$username';");
        $x = $r->fetch();
        if(gettype($x) != "boolean")
            return $x["realname"];
        return $x;
    }
    function getImg($email){
        $r = $this->m->query("SELECT img FROM users WHERE email LIKE '$email';");
        $x = $r->fetch();
        if(gettype($x) != "boolean")
            return $this->uplib->geturl($x["img"].".png");
        else return "";
    }
    function getImgCode($email){
        $r = $this->m->query("SELECT img FROM users WHERE email LIKE '$email';");
        $x = $r->fetch();
        if(gettype($x) != "boolean")
            return $x["img"].".png";
        else return "";
    }
    function getImgByUsername($username){
        $r = $this->m->query("SELECT img FROM users WHERE username LIKE '$username';");
        $x = $r->fetch();
        if(gettype($x) != "boolean")
        return $this->uplib->geturl("profile_images/".$x["img"].".png");

        else 
            return "";
    }
    function searchUser($fullname){
        $fullname = "%".$fullname."%";
        $r = $this->m->query("SELECT * FROM users WHERE realname LIKE ".$this->m->quote($fullname)." LIMIT 0, 10");
        return $r->fetchAll();
    }
    function getRecentQuestion($a, $b){
        $r = $this->m->query("SELECT * FROM questions ORDER BY  STR_TO_DATE(`postdate`,'%d-%m-%y %H:%i:%s') DESC LIMIT $a, $b;");
        return $r->fetchAll();
    }
    function getRecentComments($id, $a){
        $r = $this->m->query("SELECT * FROM comments WHERE answerid LIKE ".$this->m->quote($id)." ORDER BY postdate DESC LIMIT $a;");
        return $r->fetchAll();
    }
    function searchQuestion($query, $a, $b){
        global $date;
        $r = $this->m->query("SELECT * FROM questions WHERE title REGEXP ".$this->m->quote($query)." LIMIT $a, $b;");
        return $r->fetchAll();
    }
    function getQuestion($id){
        $r = $this->m->query("SELECT * FROM questions WHERE id LIKE ".$this->m->quote($id));
        $x = $r->fetch();
        return $x;
    }
    function getEmailByUsername($username){
        $r = $this->m->query("SELECT email FROM users WHERE username LIKE '$username';");
        $x = $r->fetch();
        if(gettype($x) != "boolean")
            return $x["email"];
        else return "";
    }
    function changeRealname($old, $new){
        $r = $this->m->query("UPDATE users SET realname=".$this->m->quote($new)." WHERE realname LIKE ".$this->m->quote($old));
        $x = $r->fetch();
        return $x;
    }
    function emailExist($email){
        $r = $this->m->query("SELECT username FROM `users` WHERE `email` LIKE '$email';");
        return ($r->fetch() != false) ? true : false;
    }
    function usernameExist($username){
        $r = $this->m->query("SELECT * FROM users WHERE username = '$username';");
        return ($r->fetch() != false) ? true : false;   
    }
    function login($email, $password){
        $r = $this->m->query("SELECT password FROM users WHERE email = '$email'");
        if($this->emailExist($email)){
            $ox = password_verify($password,$r->fetch()["password"]);
            var_dump($ox);
            $n = @password_verify($password,$r->fetch()["password"]) == 1;
            if($n)
                return true;
            else 
                return false; 
        }
        return false;        
    }
}
