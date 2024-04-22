<?php
session_start();

include "../db_connection.php";

// Verifico se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    // Reindirizzo l'utente alla pagina di accesso se non loggato
    header("Location: ../auth/login.php");
    exit();
}

// Verifico se è stato fornito l'ID del post da eliminare
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Reindirizzo l'utente alla pagina index.php se l'ID del post non è stato fornito
    header("Location: index.php");
    exit();
}

// Recupero l'ID del post dalla query
$post_id = $_GET['id'];

// Eseguo l'operazione di eliminazione del post nel database
$sql = "DELETE FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();

$stmt->close();

// Reindirizzo l'utente alla pagina index.php dopo aver eliminato il post
header("Location: index.php");
exit();

$conn->close();
