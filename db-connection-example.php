<?php
// Copia questo file in db-connection.php e inserisci le tue credenziali
$host = "localhost";
$username = "inserisci_nome_utente";
$password = "inserisci_password";
$database = "inserisci_nome_database";

// Connessione al database
$conn = new mysqli($host, $username, $password, $database);

// Verifico se c'è un errore di connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
