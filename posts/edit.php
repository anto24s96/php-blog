<?php
session_start();

include "../db_connection.php";
include "upload.php"; // Includi il file di upload

// Verifico se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    // Reindirizzo l'utente alla pagina di accesso se non è loggato
    header("Location: ../auth/login.php");
    exit();
}

// Definisco le variabili e inizializzo gli errori
$title = "";
$content = "";
$titleErr = "";
$contentErr = "";
$imageErr = ""; // Aggiungi l'errore per l'immagine

// Verifico se è stato fornito l'ID del post da modificare
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Reindirizzo l'utente alla pagina index.php se l'ID del post non è stato corretto
    header("Location: index.php");
    exit();
}

// Recupero l'ID del post dalla query string
$post_id = $_GET['id'];

// Recupero i dettagli del post dal database
$sql = "SELECT title, content, category_id, image FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Popolo i campi del modulo con i valori del post esistente
    $row = $result->fetch_assoc();
    $title = $row['title'];
    $content = $row['content'];
    $category_id = $row['category_id'];
    $existing_image = $row['image']; // Immagine attualmente associata al post
} else {
    // Reindirizzo l'utente alla pagina index.php se il post non esiste
    header("Location: index.php");
    exit();
}

// Verifico se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validazione del titolo
    if (empty($_POST["title"])) {
        $titleErr = "Il titolo è richiesto";
    } else {
        $title = $_POST["title"];
    }

    // Validazione del contenuto
    if (empty($_POST["content"])) {
        $contentErr = "Il contenuto è richiesto";
    } else {
        $content = $_POST["content"];
    }

    // Gestione dell'immagine
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
        $new_image = uploadImage($_FILES["image"]); // Carica la nuova immagine
        if ($new_image) {
            // Cancella l'immagine precedente solo se viene caricata una nuova immagine con successo
            unlink($existing_image);
            $existing_image = $new_image; // Aggiorna il percorso dell'immagine nel caso venga caricata una nuova immagine
        } else {
            $imageErr = "Si è verificato un errore durante il caricamento dell'immagine";
        }
    }

    // Se non ci sono errori, aggiorno il post nel database
    if (empty($titleErr) && empty($contentErr) && empty($imageErr)) {
        // Recupero l'id della categoria
        $category_id = $_POST['category_id'];

        // Query per l'aggiornamento del post
        $sql = "UPDATE posts SET title = ?, content = ?, category_id = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $title, $content, $category_id, $existing_image, $post_id);
        $stmt->execute();

        // Reindirizzo l'utente alla pagina index.php dopo aver modificato il post
        header("Location: index.php");
        exit();
    }
}

// Recupero le categorie dal database
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

// Creo un array per memorizzare i risultati
$categories = array();

// Verifico se sono presenti dei risultati
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Chiudo la connessione
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../partials/header.php' ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <h1 class="text-center">Edit Post</h1>
            </div>
            <div class="col-8">
                <!-- Form per la modifica del post -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $post_id); ?>" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" required>
                        <span class="text-danger"><?php echo $titleErr; ?></span>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Current Image</label>
                        <?php if (!empty($existing_image)) { ?>
                            <div>
                                <img src="<?php echo $existing_image; ?>" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        <?php } ?>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload New Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                        <span class="text-danger"><?php echo $imageErr; ?></span>
                    </div>



                    <div class="mb-3">
                        <label for="category" class="mb-2">Category</label>
                        <select class="form-select w-25" aria-label="Default select example" id="category" name="category_id">
                            <option selected>Select category</option>
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $category_id) echo "selected"; ?>><?php echo $category['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required><?php echo $content; ?></textarea>
                        <span class="text-danger"><?php echo $contentErr; ?></span>
                    </div>
                    <button type="submit" class="btn btn-primary">SAVE</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>