<?php
function uploadImage($file)
{
    $uploadDirectory = "uploads/";

    // Genero un nome di file univoco aggiungendo un timestamp
    $uniqueFileName = time() . '_' . basename($file["name"]);
    $uploadPath = $uploadDirectory . $uniqueFileName;

    if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
        return $uploadPath;
    } else {
        return false;
    }
}
