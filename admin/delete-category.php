<?php
require 'config/database.php';


if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    $update_query = "UPDATE posts SET category_id =40 where category_id=$id ";
    $update_result = mysqli_query($connection, $update_query);

    //delete category from data base 
    $delete_post_query = "DELETE FROM categories WHERE  id=$id LIMIT 1";
    $delete_post_result = mysqli_query($connection, $delete_post_query);
    if (!mysqli_errno($connection)) {
        $_SESSION['delete-category-success'] = " Category deleted successfully ";
    }
}
header('location:' . ROOT_URL . 'admin/manage-categories.php');
die();
