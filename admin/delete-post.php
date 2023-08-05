<?php
require 'config/database.php';


if(isset($_GET['id'])){
    $id=filter_var($_GET['id'] , FILTER_SANITIZE_NUMBER_INT);

    //featch user from datABASE 
    $query = "SELECT * FROM posts WHERE id=$id";
    $result=mysqli_query($connection , $query);
    
    //make sure we got back only one user 
    if(mysqli_num_rows($result) == 1){
        $post=mysqli_fetch_assoc($result);
        $thumbnail_name = $post['thumbnail'];
        $thumbnail_path= '../images/' . $thumbnail_name ; 
        //delete image if avilable 
        if ($thumbnail_path){
            @unlink($thumbnail_path);
        }
    }
    //for later 
    //featch all thumbnails of user posts  and delete them 
    






    //delete user erom data base 
    $delete_post_query="DELETE FROM posts WHERE  id=$id ";
    $delete_post_result=mysqli_query($connection , $delete_post_query);
    if(!mysqli_errno($connection)){
        $_SESSION['delete-post-success'] = " Post delete successfully ";  

    }
}
header('location:' . ROOT_URL . 'admin/');
die();