<?php
require 'config/database.php';

//make sure fedit post button was clicked 
if (isset($_POST['submit'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    //set is_featured to 0 if it  was unchecked 
    $is_featured = $is_featured == 1 ?: 0;

    //check and validate input values 
    if (!$title) {
        $_SESSION['edit-post'] = "Couldn't update post. Invalid form data on edit post page ";
    } elseif (!$category_id) {
        $_SESSION['edit-post'] = "Couldn't update post. Invalid form data on edit post page ";
    } elseif (!$body) {
        $_SESSION['edit-post'] = "Couldn't update post. Invalid form data on edit post page ";
    } else {
        //delete existing thumbnail if new is available
        if ($thumbnail['name']) {
            $previous_thumbnail_path = '../images/' . $previous_thumbnail_name;
            if ($previous_thumbnail_path) {
                @unlink($previous_thumbnail_path);
            }
            //work on thumbnail
            //rename the image 
            $time = time(); //make echo image name uniqe
            $thumbnail_name = $time . $thumbnail['name'];
            $thumbnail_tmp_name = $thumbnail['tmp_name'];
            $thumbnail_destination_path = '../images/' . $thumbnail_name;

            //make sure fille is an image
            $allowed_files = ['png', 'jpg', 'jpeg'];
            $extension = explode('.', $thumbnail_name);
            $extension = end($extension);
            if (in_array($extension, $allowed_files)) {
                //make sure imager is not too large
                if ($thumbnail['size'] < 2_000_000) {
                    //upload thumbnail
                    move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
                } else {
                    $_SESSION['edit-post'] = "Couldn't update post. thumbnai size too big  ";
                }
            } else {
                $_SESSION['edit-post'] = "Couldn't update post. Invalid form data on edit post page ";
            }
        }
    }


    //set is_featured all posts to 0 if is_featured for this post is 1 
    if ($is_featured == 1) {
        $zero_all_is_featured_query = "UPDATE posts SET is_featured = 0 ";
        $zero_all_is_featured_result = mysqli_query($connection, $zero_all_is_featured_query);
    }

    //set thumbnail name if new one was uploded , else keep old thumbnail name 
    $thumbnail_to_insert = $thumbnail_name ?? $previous_thumbnail_name;
    $query = "UPDATE posts  SET title ='$title' , body = '$body' , thumbnail = '$thumbnail_to_insert' , category_id = '$category_id' ,is_featured = '$is_featured' WHERE id=$id LIMIT 1 ";
    $result = mysqli_query($connection, $query);
    if (!mysqli_errno($connection)) {
        $_SESSION['edit-post-success'] = "Post updated successfully";
        header('location:' . ROOT_URL . 'admin/');
        die();
    }
    header('location:' . ROOT_URL . 'admin/');
    die();
}
