<?php include "functions.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?=style_script()?>
    <script>
    $(document).ready(function() {
        $('#choose').DataTable();
    } );
    </script>

    <title>Dashboard My Exam</title>
</head>
<body>
    

<?php

$pdo = pdo_connect();
$stmt = $pdo->prepare('SELECT tb_answer.*, tb_user.nama, COUNT(tb_answer.id_question) AS answered FROM tb_answer
        LEFT JOIN tb_user
        ON tb_answer.id_chat=tb_user.id_chat GROUP BY id_chat');
$stmt->execute();
$algos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// cek total pertanyaan
$result = $pdo->prepare("SELECT count(*) FROM tb_question"); 
$result->execute(); 
$number_of_rows = $result->fetchColumn(); 


?>
<div class="container">
	<h2>Statistics</h2>
    <br>
    <div class="row">
        <div class="col">
            <table class="table table-striped" id="choose">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Mahasiswa</th>
                    <th>Nilai</th>
                    <th>Progress</th>
                    <th>Last Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach ($algos as $algo): 
                    $stmt_nilai = $pdo->prepare('SELECT * FROM tb_answer WHERE id_chat = ?');
                    $stmt_nilai->execute([$algo['id_chat']]);
                    $ns = $stmt_nilai->fetchAll(PDO::FETCH_ASSOC);
                    $count = 0;
                    
                    foreach ($ns as $n): 
                        if($n['answer'] == $n['correct_answer']) {                            
                            $count += 1;
                        }
                    
                    endforeach;


                    // cek last answer
                    $stmt_gl = $pdo->prepare('SELECT * FROM tb_answer WHERE id_chat = ? ORDER BY `date` DESC LIMIT 1');
                    $stmt_gl->execute([$algo['id_chat']]);
                    $get_last = $stmt_gl->fetch(PDO::FETCH_ASSOC);

                    $nilai = $count / $number_of_rows * 100;
                ?>
                
                <tr>
                    <td><?=$i?></td>
                    <td><?=$algo['nama']?></td>
                    <td><?=round($nilai)?></td>
                    <td><?=($algo['answered']==$number_of_rows)?"<span class='badge badge-success'>Selesai</span>":$algo['answered']." Soal"?></td>
                    <td><?=$get_last['date'] ?></td>
                </tr>
                <?php $i++;endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>#</th>
                    <th>Nama Mahasiswa</th>
                    <th>Nilai</th>
                    <th>Progress</th>
                    <th>Last Answer</th>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>    
	<!-- <a type="button" class="btn btn-danger btn-lg" href="change.php" class="change-choice">Ganti Pilihan</a> -->
	</body>
</html>