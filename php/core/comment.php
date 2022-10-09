<?php
    trait commentAction {
        /**
         * Post comment to answer, return posted comment ID
         * (part of Core class)
         * @param string $content  Content should be plaintext
         * @param string $answerid ID of answer for this comment
         * @param string $username Username of user who post this comment
         * @return string
         */
        function addComment($content, $answerid, $username){
            global $date;
            $content  = strip_tags($content);
            $id       = generator(12);
            $test     = $this->m->query("SELECT * FROM comments WHERE id LIKE ".$this->m->quote($id))->fetchAll();
            if(count($test) > 0)
                $this->addComment($content, $answerid, $username);
            if(strlen($content) < 1000){
                $this->m->exec("
                    INSERT INTO comments(
                        id,
                        answerid,
                        username,
                        content,
                        postdate
                    ) VALUES('$id', '$answerid', '$username', '$content', '$date');
                ");
            }
            return $id;
        }
        /**
         * Delete comment from answer, return posted comment ID
         * @param  string $commentid ID of this comment 
         * @return string
         */
        function deleteComment($commentid){
            $commentid = $this->m->quote($commentid);
            $r = $this->m->query("DELETE FROM comments WHERE id LIKE $commentid;");
            return $commentid;
        }
        /**
         * Fetch comments from answer, return array of fetched comments
         * @param  string $commentid ID of this comment 
         * @return array
         */
        function getComments($answerid, $limit_a = 0, $limit_b = 10){
            $answerid = $this->m->quote($answerid);
            $r = $this->m->query("SELECT * FROM comments WHERE answerid LIKE $answerid ORDER BY   STR_TO_DATE(`postdate`,\"%d-%m-%y %H:%i:%s\") DESC LIMIT $limit_a, $limit_b;");
            $g = $r->fetchAll();
            if(gettype($g) == "boolean")
                return array();
            return $g;
        }
         /**
         * Get all comment properties from comment id in database
         * @param  string $id comment id (required)
         * @return array
         */
        function getCommentById($id){
            $r = $this->m->query("SELECT * FROM comments WHERE id LIKE ".$this->m->quote($id));
            $g = $r->fetch();
            if(gettype($g) == "boolean")
                return array();
            return $g;
        }
    }