<?php

require 'config/database.php';

//get signup from  data if signup button was clicked 
if(isset($_POST['submit'])){
$firstname =filter_var($_POST['firstname'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$lastname =filter_var($_POST['lastname'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$username =filter_var($_POST['username'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email =filter_var($_POST['email'] , FILTER_VALIDATE_EMAIL);
$creatpassword =filter_var($_POST['creatpassword'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$confirmpassword =filter_var($_POST['confirmpassword'] , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$is_admin = filter_var($_POST['userrole'],FILTER_SANITIZE_NUMBER_INT);
$avatar =$_FILES['avatar'];

//VALIDAE INPUT VALUE 
if(!$firstname){
    $_SESSION['add-user']="Pleas Enter Your First Name";
}elseif(!$lastname){
    $_SESSION['add-user']="Pleas Enter Your Last Name";  
}elseif(!$username){
    $_SESSION['add-user']="Pleas Enter Your username";
}elseif(!$email){
    $_SESSION['add-user']="Pleas Enter Your a Valid Email";
}elseif(strlen($creatpassword) < 8 || strlen($confirmpassword) <8 ){
    $_SESSION['add-user']="password Should be 8+ character ";
}elseif (!$avatar['name']){
    $_SESSION['add-user']= "Pleas Add Avatar";
}else {
    if($creatpassword !== $confirmpassword){
        $_SESSION['signup']="Passwords do not Match!!";
    }else{
        //hash password
        $hashed_password=password_hash($creatpassword , PASSWORD_DEFAULT);
        // echo $creatpassword . '<br/>';
        // echo $hashed_password ;
        $user_check_query ="SELECT* FROM  users WHERE username='$username' OR email='$email'";
        $user_check_result= mysqli_query($connection , $user_check_query);
        if(mysqli_num_rows($user_check_result)> 0 ){
            $_SESSION['add-user']= "username or Email  already exist";
        }else{
            //work  on avatar 
            //rename avater 
            $time=time();
            $avatar_name =$time . $avatar['name'];
            $avatar_tmp_name=$avatar['tmp_name'];
            $avatar_distination_path ='../images/'. $avatar_name;
             
            //make sure file is an image 
            $allowed_files =['png' , 'jpg' , 'jpeg' ]; 
            $extention =explode('.',$avatar_name);
            $extention=end($extention);
            if(in_array($extention ,$allowed_files)){
              //make sure  image is not too large (1mb+)
              if ($avatar['size'] < 1000000) {
                //upload avatar 
                move_uploaded_file($avatar_tmp_name , $avatar_distination_path);
              }else{
                $_SESSION['add-user']="File Size too big . Should be less than 1mb";
              }      
            }else{
                $_SESSION['add-user']="File Should be png , jpg or jpeg";
            }
        }

    }
} 
    //redirect back to add-user pag eif there waas any problem 
    if (isset($_SESSION['add-user'])){
        //pass from data back to add-user page 
        $_SESSION['add-user-data']=$_POST;
        header('Location:' . ROOT_URL . '/admin/add-user.php');
        die();
    }else{
        //inseart new user into users table 
        $inset_user_query = "INSERT INTO users SET firstname ='$firstname' , lastname='$lastname' , 
        username='$username' , email='$email' , password='$hashed_password' , 	avatar ='$avatar_name' ,
        is_admin=$is_admin";
        $insert_user_result =mysqli_query($connection , $inset_user_query);
    
        if(!mysqli_errno($connection)){
            //redirect to login page with success message
            $_SESSION['add-user-success'] ="New user $firstname $lastname added ssuccessfully.";
            header('Location:' . ROOT_URL . 'admin/manage-users.php');
            die(); 
        }
    }


}else{
    //IF BUTTON WASN,T CLICKED BOUNCE BACK TO SIGNUP PAGE 
    header('location:'.ROOT_URL. 'admin/add-user.php');
    die();
}