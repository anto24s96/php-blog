<?php
session_start();

include '../db_connection.php';

// Definizione di variabili per i messaggi di errore
$username_err = $password_err = $confirm_password_err = "";

// Verifica se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupero i dati dal form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validazione dell'username
    if (empty(trim($username))) {
        $username_err = "Inserisci l'username";
    } else {
        // Controllo se l'username è già stato utilizzato
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $param_username);
        $param_username = $username;
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $username_err = "L'username è già stato utilizzato";
            }
        } else {
            echo "Qualcosa è andato storto. Riprova più tardi.";
        }
        $stmt->close();
    }

    // Validazione della password
    if (empty(trim($password))) {
        $password_err = "Inserisci la password";
    } elseif (strlen(trim($password)) < 8) {
        $password_err = "La password deve contenere almeno 8 caratteri";
    }

    // Validazione della conferma password
    if (empty(trim($confirm_password))) {
        $confirm_password_err = "Conferma la password";
    } else {
        if ($password != $confirm_password) {
            $confirm_password_err = "Le password non corrispondono";
        }
    }

    // Inserimento dell'utente nel database se non ci sono errori
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        // Prepare la query di inserimento
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $param_username, $param_password);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Hash della password
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $conn->insert_id;
                // Reindirizza alla pagina di login dopo la registrazione
                header("location: /php-blog/auth/login.php");
            } else {
                echo "Qualcosa è andato storto. Riprova più tardi.";
            }
            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Registrazione
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
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                <span class="text-danger"><?php echo $confirm_password_err; ?></span>
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>