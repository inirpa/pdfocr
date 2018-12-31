<?php
    $fp = fopen('op.txt', 'a');
    fwrite($fp, $_POST['ocr_text']);
    fclose($fp);
?>