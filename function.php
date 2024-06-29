<?php include('config.php');
include('process.php');

// Set headers to allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Function to register a new user

$obj=new KhoyaPaya($conn);

if($_SERVER["REQUEST_METHOD"] == "POST" && $_GET['action']=='register'){
   echo  $obj->signup($_REQUEST);
}else

if($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['action']=='get_category'){
    echo  $obj->get_cat($_REQUEST);
}else
if($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['action']=='all_item'){
    echo  $obj->get_item($_REQUEST);
}else
if($_SERVER["REQUEST_METHOD"] == "POST" && $_GET['action']=='user_item'){
    echo  $obj->get_item($_REQUEST);
}else
if($_SERVER["REQUEST_METHOD"] == "POST" && $_GET['action']=='add_item'){
    echo  $obj->add_found_item($_REQUEST);
}else{
    echo 'invalid request';
}



