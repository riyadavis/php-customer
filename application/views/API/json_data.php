<?php

    header('Access-Control-Allow-Origin:*');
    header('Content-type:application/json;charset=utf-8');
    echo json_encode($items)
?>