<?php
session_start();

include '../db_connection.php';

// Definizione di variabili per i messaggi di errore
$username_err = $password_err = "";

// Verifica se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupero i dati dal form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Preparo la query SQL utilizzando una prepared statement
    $sql = "SELECT * FROM users WHERE BINARY username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);

    // Eseguo la query
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Utente trovato, verifico la password
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Password corretta, memorizzo l'username nella sessione e reindirizzo all'index'
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id'];
            header("Location: ../index.php"); // Redirect all'index
            exit();
        } else {
            // Password non corretta, setto l'errore
            $password_err = "Password non corretta";
        }
    } else {
        // Utente non trovato, setto l'errore
        $username_err = "Utente non trovato";
    }

    // Chiusura dello statement
    $stmt->close();
}

// Chiusura della connessione al database
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Login
                    </div>
                    <div class="card-body">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username">
                                <span class="text-danger"><?php echo $username_err; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <span class="text-danger"><?php echo $password_err; ?></span>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>