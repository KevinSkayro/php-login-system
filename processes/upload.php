<?php
include_once 'dbhandler.php';
$id = $_SESSION['id'];

$file = $_FILES['file'];

$fileName = $_FILES['file']['name'];
$fileTmpName = $_FILES['file']['tmp_name'];
$fileSize = $_FILES['file']['size'];
$fileError = $_FILES['file']['error'];
$fileType = $_FILES['file']['type'];

$fileExt = explode('.', $fileName);
$fileActualExt = strtolower(end($fileExt));

$allowed = array('jpg', 'jpeg');

if (isset($_POST['submit1'])) {

    if (in_array($fileActualExt, $allowed)) {
       if ($fileError === 0) {
           if ($fileSize < 10485760) {
            $fileNameNew = "profile1".$id.".".$fileActualExt;
            $fileDestination = '../employee_uploads/'.$fileNameNew;
            $sql = "UPDATE accounts SET imgStatus_1=0 WHERE id='$id'";
            $result = mysqli_query($con, $sql);
            move_uploaded_file($fileTmpName, $fileDestination);
            header("Location: ../profile.php?upload=success");
           } else {
                header("Location: ../profile.php?upload=file-too-big");
                exit();
           }
       } else {
            header("Location: ../profile.php?upload=error");
            exit();
       }
    } else {
        header("Location: ../profile.php?upload=incompatible");
        exit();
    }
}else if (isset($_POST['submit2'])) {
    if (in_array($fileActualExt, $allowed)) {
       if ($fileError === 0) {
           if ($fileSize < 10485760) {
            $fileNameNew = "profile2".$id.".".$fileActualExt;
            $fileDestination = '../employee_uploads/'.$fileNameNew;
            $sql = "UPDATE accounts SET imgStatus_2=0 WHERE id='$id'";
            $result = mysqli_query($con, $sql);
            move_uploaded_file($fileTmpName, $fileDestination);
            header("Location: ../profile.php?upload=success");
           } else {
                header("Location: ../profile.php?upload=file-too-big");
                exit();
           }
       } else {
            header("Location: ../profile.php?upload=error");
            exit();
       }
    } else {
        header("Location: ../profile.php?upload=incompatible");
        exit();
    }
}else if (isset($_POST['submit3'])) {
    if (in_array($fileActualExt, $allowed)) {
       if ($fileError === 0) {
           if ($fileSize < 10485760) {
            $fileNameNew = "guarantor1".$id.".".$fileActualExt;
            $fileDestination = '../employee_uploads/'.$fileNameNew;
            $sql = "UPDATE accounts SET guar_imgStatus_1=0 WHERE id='$id'";
            $result = mysqli_query($con, $sql);
            move_uploaded_file($fileTmpName, $fileDestination);
            header("Location: ../profile.php?upload=success");
           } else {
                header("Location: ../profile.php?upload=file-too-big");
                exit();
           }
       } else {
            header("Location: ../profile.php?upload=error");
            exit();
       }
    } else {
        header("Location: ../profile.php?upload=incompatible");
        exit();
    }
}else if (isset($_POST['submit4'])) {
    if (in_array($fileActualExt, $allowed)) {
       if ($fileError === 0) {
           if ($fileSize < 10485760) {
            $fileNameNew = "guarantor2".$id.".".$fileActualExt;
            $fileDestination = '../employee_uploads/'.$fileNameNew;
            $sql = "UPDATE accounts SET guar_imgStatus_2=0 WHERE id='$id'";
            $result = mysqli_query($con, $sql);
            move_uploaded_file($fileTmpName, $fileDestination);
            header("Location: ../profile.php?upload=success");
           } else {
                header("Location: ../profile.php?upload=file-too-big");
                exit();
           }
       } else {
            header("Location: ../profile.php?upload=error");
            exit();
       }
    } else {
        header("Location: ../profile.php?upload=incompatible");
        exit();
    }
}