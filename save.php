<?php
    $fileName = pathinfo($_POST['file_name'], PATHINFO_FILENAME);
    if(file_exists($fileName.'.txt')){
        echo "file exists";
        $current_count = count(glob($fileName.'*.txt'))+1;
        $fullName = $fileName."_".$current_count.".txt";
    }else{
        echo "file doesnt exists";
        $fullName = $fileName.".txt";
    }
    echo $fullName;
    $fp = fopen($fullName, 'a');
    fwrite($fp, $_POST['ocr_text']);
    fclose($fp);
?>