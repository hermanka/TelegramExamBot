<?php

include 'functions.php';
date_default_timezone_set("Asia/Jakarta");
$now = strtotime(date("Y-m-d H:i:s"));
$deadline = strtotime('2021-02-16 15:40:00');

$pdo = pdo_connect();
$path = bot_token_path();

$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$stmt3 = $pdo->prepare("INSERT INTO tb_log(id_chat,command_log) VALUES(?,?)");
$stmt3->execute([$chatId,$message]);


// cek status
$stmt_status = $pdo->prepare('SELECT * FROM tb_status WHERE id_chat = ?');
$stmt_status->execute([$chatId]);
$arr_status = $stmt_status->fetch(PDO::FETCH_ASSOC);

$my_status = $arr_status['status'];
$my_status_opt = $arr_status['status_option'];
$my_status_opt2 = $arr_status['status_option2'];
if($now>$deadline){

    file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Waktu habis.&reply_markup=".$reply);

} else {
    if ($message == "/start") {

        // cek apakah sudah pernah start dan mengisi nama

        $stmt_cek = $pdo->prepare('SELECT * FROM tb_user WHERE id_chat = ?');
        $stmt_cek->execute([$chatId]);
        $cek_user = $stmt_cek->fetchColumn(); 

        if($cek_user==0){
            $welcome_message1 = "Selamat Datang, ID Anda : ". $chatId;
            $welcome_message2 = "Tuliskan nama Anda ";
        
            $welcome_message = urlencode("$welcome_message1 \n$welcome_message2");
            // save status waiting insert name
        
            // save status
            $stmt_wait_name = $pdo->prepare("INSERT INTO tb_status(id_chat,status) VALUES(?,?)");
            $stmt_wait_name->execute([$chatId,'waiting_name']);
        
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$welcome_message);
        } else {        
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Perintah telah dijalankan");
        }
    

    }

    if($my_status == "waiting_name"){
        // hapus status
        $stmt_del_stat = $pdo->prepare("DELETE FROM tb_status WHERE id_chat = ?");
        $stmt_del_stat->execute([$chatId]);

        // save name
        $stmt_ins_name = $pdo->prepare("INSERT INTO tb_user VALUES(?,?)");
        $stmt_ins_name->execute([$chatId, $message]);

        $keyboard = array(array("Pertanyaan"));
        $resp = array("keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true);
        $reply = json_encode($resp);

        // cek total pertanyaan
        $result = $pdo->prepare("SELECT count(*) FROM tb_question"); 
        $result->execute(); 
        $number_of_rows = $result->fetchColumn(); 

        $welcome_message = "Tap tombol 'Pertanyaan' pada keyboard setiap kali Anda ingin menampilkan dan menjawab pertanyaan.\n\nSetiap pertanyaan hanya dapat dijawab satu kali dan jawaban tidak dapat diubah, sehingga pastikan Anda telah yakin sebelum menjawab (atau tekan 'Skip' untuk melewati pertanyaan).\n\nTotal $number_of_rows pertanyaan.\n\nDeadline : ". date('d-m-Y H:i', $deadline);

        $success_message = urlencode($welcome_message);
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Selamat datang ".$message."&reply_markup=".$reply);
        
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$success_message."&reply_markup=".$reply);

    }


    if(strtolower($message) == "pertanyaan" || strtolower($message) == "skip" ){

        // cek apakah soal sudah dijawab?

        $stmt_cek = $pdo->prepare('SELECT * FROM tb_answer WHERE id_chat = ?');
        $stmt_cek->execute([$chatId]);
        $ids = $stmt_cek->fetchAll(PDO::FETCH_ASSOC);
        $all_ids = [];
        foreach ($ids as $idq): 
            $all_ids[] = $idq['id_question'];
        endforeach;

        // cek total pertanyaan
        $result = $pdo->prepare("SELECT count(*) FROM tb_question"); 
        $result->execute(); 
        $number_of_rows = $result->fetchColumn(); 

        if(count($all_ids)<$number_of_rows) {  

            while( in_array( ($n = mt_rand(1, $number_of_rows)), $all_ids ) );

            $stmt = $pdo->prepare('SELECT * FROM tb_question WHERE id_question = ?');
            $stmt->execute([$n]);
            $choose = $stmt->fetch(PDO::FETCH_ASSOC);

            // hapus status
            $stmt_del_stat = $pdo->prepare("DELETE FROM tb_status WHERE id_chat = ?");
            $stmt_del_stat->execute([$chatId]);

            // save status
            $stmt3 = $pdo->prepare("INSERT INTO tb_status(id_chat,status,status_option,status_option2) VALUES(?,?,?,?)");
            $stmt3->execute([$chatId,'waiting_answer',$choose['id_question'],strtoupper($choose['correct_answer'])]);

            $question = $choose['question'];
            $answer_a = $choose['answer_a'];
            $answer_b = $choose['answer_b'];
            $answer_c = $choose['answer_c'];
            $answer_d = $choose['answer_d'];

            // cek total pertanyaan dijawab
        
            $tots = $pdo->prepare("SELECT count(*) FROM tb_answer WHERE id_chat = ?"); 
            $tots->execute([$chatId]); 
            $last_number = $tots->fetchColumn();
            $next_number = $last_number+1;

            $exam_message = urlencode("$next_number. $question \nA. $answer_a \nB. $answer_b \nC. $answer_c \nD. $answer_d");

            $keyboard = [["A","B","C","D"],["Skip"]];
            $resp = array("keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true);
            $reply = json_encode($resp);
            
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$exam_message."&reply_markup=".$reply);

        } else {
            $resp = array(
                'remove_keyboard' => true
            );
            $reply = json_encode($resp);
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Ujian selesai, semua pertanyaan telah Anda jawab.&reply_markup=".$reply);
        }

    } else {



        if($my_status == "waiting_answer"){


            $message = strtoupper($message);
            $correct_answer = array("A","B","C","D");
            if(in_array($message, $correct_answer)){
            
                $stmt = $pdo->prepare('SELECT * FROM tb_question WHERE id_question = ?');
                $stmt->execute([$my_status_opt]);
                $choose = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $exam_message = $choose['question'];
                $keyboard = array(array("Pertanyaan"));
                $resp = array("keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true);
                $reply = json_encode($resp);
                
                // hapus status
                $stmt_del_stat = $pdo->prepare("DELETE FROM tb_status WHERE id_chat = ?");
                $stmt_del_stat->execute([$chatId]);

                // cek total pertanyaan dijawab
        
                $tots = $pdo->prepare("SELECT count(*) FROM tb_answer WHERE id_chat = ?"); 
                $tots->execute([$chatId]); 
                $last_number = $tots->fetchColumn();
                $next_number = $last_number+1;
                
                // simpan jawaban
                $stmt_save_ans = $pdo->prepare("INSERT INTO tb_answer(id_chat, no_question, id_question, answer, correct_answer) VALUES(?,?,?,?,?)");
                $stmt_save_ans->execute([$chatId, $next_number, $my_status_opt , $message,$my_status_opt2 ]);

                file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Jawaban Anda : ".$message.", untuk pertanyaan : ".urlencode($exam_message)."&reply_markup=".$reply);
            } else {
                $keyboard = array(array("Pertanyaan"));
                $resp = array("keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true);
                $reply = json_encode($resp);
                
                // hapus status
                $stmt_del_stat = $pdo->prepare("DELETE FROM tb_status WHERE id_chat = ?");
                $stmt_del_stat->execute([$chatId]);

                file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Jawaban tidak dikenali&reply_markup=".$reply);
            }
        }

    }

}

?>