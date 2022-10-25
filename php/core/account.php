<?php  
    require_once(__DIR__."/../../config/config.php");
    trait accountAction  {
        /**
         * delete user account that existed in database. Return true if success and false if failed.
         * this function is part of the AccountAction subclass
         * @param string $username User Username (must be valid username, no whitespace in the end/start, fully plain)
         * @param string $email    User Email Address (must be valid email address), example: udinpetot@gmail.com
         * @param string $pass     User Password (must be php_hash password ), example: $2y$10$xMGiIIxxWIlTWnf4k3J9KeG18xFd5WCwTJiBQz2hPSl6llZ59T26
         * @return boolean|PDOException return true if success executed and false if failed to execute return PD0Exception
         */
        function deleteAccount($username, $email, $pass){
            if($this->login(trim($email), trim($pass))){
                try {
                    $this->m->exec("
                        DELETE FROM users WHERE username=".$this->m->quote($username).";
                    ");
                    $answers = $this->getAnswersByUsername($username);
                    unlink(dirname(__FILE__)."/".$this->getImgByUsername($username));
                    foreach($answers as $as){
                        $this->deleteAnswer($as["id"]);
                    }
                    $questions = $this->getQuestionsByUsername($username);
                    foreach($questions as $qs){
                        $this->deleteQuestion($qs["id"]);
                    }
                    $this->m->query("DELETE FROM notifications WHERE username LIKE ".$username);
                    $this->m->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    return true;
                } catch(PDOException $e) {
                    return $e;
                }
            }
        }
        /**
         * create new account if not existed in database
         * return true if success and false if failed
         * * this function is part of the AccountAction subclass
         * @param string $username Username of the user
         * @param string $realname Fullname of the user 
         * @param string $email    Email of the user 
         * @param string $password Password (phpencrypted) of the user
         * @return bool
         */
        function createAccount($username, $realname, $email, $password){
            global $userimgdir;
            try {
                if(!preg_match("/^[0-9a-z\_]+/", $username))
                    return;
                $user     = $this->m->quote(strtolower($username));
                $email    = $this->m->quote($email);
                $password = $this->m->quote(password_hash(trim($password), PASSWORD_DEFAULT));
                $realname = $this->m->quote($realname);
                $c = $username."-".generator(5, "abcdefghijklmnopqrstuvwxyz");
                $f = fopen($userimgdir.$c.".png", "w");
                fwrite($f, fread(
                    fopen(__DIR__."/../userimg/template/Anonym.png", "r"),
                    filesize(__DIR__."/../userimg/template/Anonym.png")
                ));
                fclose($f);
                $this->m->exec("
                    INSERT INTO users(
                        username,
                        realname,
                        img,
                        password,
                        email,
                        points,
                        about,
                        friends
                    ) VALUES($user, $realname, '$c', $password, $email, '0', '', '');
                ");
                $this->m->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return true;
            } catch(PDOException $e) {
                die("Connection failed: ".$e->getMessage());
            }
        }
        function getAboutByUsername($username){
            $r = $this->m->query("SELECT about FROM users WHERE username LIKE '$username';");
            $x = $r->fetch();
            if(gettype($x) != "boolean")
                if(strlen($x["about"]) < 1)
                    return "Hello";
                return $x["about"];
            return $x;
        }
        function getBestUser(){
            $r = $this->m->query("
                select answers.username, users.realname, count(answers.votes) as point from answers, users where (answers.username=users.username and answers.votes='1') group by username order by point desc limit 5 
            ");
            $x = $r->fetchAll();
            if(gettype($x) == "boolean")
                return array();
            return $x;
        }
        function changePassword($username, $new_password){
            $new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $r = $this->m->query("UPDATE `users` SET `password`=".$this->m->quote($new_password)." WHERE `email` LIKE ".$this->m->quote($username));
            $x = $r->fetch();
            return $x;
        }
    }
