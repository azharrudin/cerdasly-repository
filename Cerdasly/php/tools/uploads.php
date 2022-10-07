<?php
    require_once(__DIR__."/../../config/config.php");
    require_once(__DIR__."/../cerdasly.php");
    class UploadAttachmentTools {
        function upload_answer_attachment($id){
            if(!isset($_FILES["upload_answer_attachment"])){                    
                return 'none';
            }
            $upload_extfile = ""; 
            $imageFileType  = strtolower(pathinfo($_FILES["upload_answer_attachment"]["name"], PATHINFO_EXTENSION));
            if($imageFileType == "pdf")
                $upload_extfile = ".pdf";
            else
                $upload_extfile = ".png";
            $upload_filepath = CRDSLY_ATTACHMENT_ANSWERS_DIR."/".$id.$upload_extfile;
            if(isset($_FILES["upload_answer_attachment"]) && ($upload_extfile == ".png")) {
                $check = getimagesize($_FILES["upload_answer_attachment"]["tmp_name"]);

                if($check !== false) {
                    $uploadOk = 1;
                } 
                else {
                    return false;
                    $uploadOk = 0;
                }
            }
            if(($_FILES["upload_answer_attachment"]['type'] != 'image/jpeg' && $_FILES["upload_answer_attachment"]['type'] != 'image/png') && $upload_extfile == ".png")
                $uploadOk = 0;

            if ($_FILES["upload_answer_attachment"]["size"] > CRDSLY_ATTACHMENT_ANSWERS_MAX) {
                $uploadOk = 0;
                return array(
                    "error_message" => "batas maksimum file lampiran sebesar 5mb"
                );
            }

            if($uploadOk){

                if (move_uploaded_file($_FILES["upload_answer_attachment"]["tmp_name"], $upload_filepath)){
                    return $id.$upload_extfile;
                }
            } else {
                echo array(
                    "error_message" => "gagal mengupload file"
                );
            }
        }
        function deleteAnswerAttachment($name){
            unlink( CRDSLY_ATTACHMENT_ANSWERS_DIR."/".$name);
        }
        //------------------------------------------------------------
        function uploadQuestionAttachment($id){
            if(!is_uploaded_file($_FILES["question_attachment"]["tmp_name"])){                    
                return 'none';
            }
            $upload_extfile = ""; 
            $imageFileType  = strtolower(pathinfo($_FILES["question_attachment"]["name"], PATHINFO_EXTENSION));
            if($imageFileType == "pdf")
                $upload_extfile = ".pdf";
            else
                $upload_extfile = ".png";
            $upload_filepath = CRDSLY_ATTACHMENT_QUESTIONS_DIR."/".$id.$upload_extfile;
            if(isset($_FILES["question_attachment"]) && ($upload_extfile == ".png")) {
                $check = getimagesize($_FILES["question_attachment"]["tmp_name"]);
                if($check !== false) {
                    $uploadOk = 1;
                } 
                else {
                    return false;
                    $uploadOk = 0;
                }
            }
            if(($_FILES["question_attachment"]['type'] != 'image/jpeg' && $_FILES["question_attachment"]['type'] != 'image/png') && $upload_extfile == ".png")
                $uploadOk = 0;

            if ($_FILES["question_attachment"]["size"] > CRDSLY_ATTACHMENT_QUESTIONS_MAX) {
                $uploadOk = 0;
                return array(
                    "error_message" => "batas maksimum file lampiran sebesar 5mb"
                );
            }

            if($uploadOk){

                if (move_uploaded_file($_FILES["question_attachment"]["tmp_name"], $upload_filepath)){
                    return $id.$upload_extfile;
                }
            } else {
                echo array(
                    "error_message" => "gagal mengupload file"
                );
            }
        }
    }
?>