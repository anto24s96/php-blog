<?php
session_start();

include "../db_connection.php";
include "upload.php";

// Recupero le categorie
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

// Definisco le variabili e inizializzazione degli errori
$title = "";
$content = "";
$titleErr = "";
$contentErr = "";
$image = "";
$imageErr = "";

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

    // Validazione dell'immagine
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {

        //Utilizzo la funzione che ho creato nel file upload.php
        $image = uploadImage($_FILES["image"]);
        if (!$image) {
            $imageErr = "Si è verificato un errore durante il caricamento dell'immagine";
        }
    }

    // Se non ci sono errori, inserisco il post nel database
    if (empty($titleErr) && empty($contentErr) && empty($imageErr)) {
        // Recupero l'id della categoria
        $category_id = $_POST['category_id'];

        $sql = "INSERT INTO posts (title, content, user_id, category_id, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $title, $content, $_SESSION['user_id'], $category_id, $image);
        $stmt->execute();

        // Reindirizzo l'utente alla pagina index.php dopo aver creato il post
        header("Location: index.php");
        exit();
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../partials/header.php' ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <h1 class="text-center">New Post</h1>
            </div>
            <div class="col-8">

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" required>
                        <span class="text-danger"><?php echo $titleErr; ?></span>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                        <span class="text-danger"><?php echo $imageErr; ?></span>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="mb-2">Category</label>
                        <select class="form-select w-25" aria-label="Default select example" id="category" name="category_id">
                            <option selected>Select category</option>
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo $category['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required><?php echo $content; ?></textarea>
                        <span class="text-danger"><?php echo $contentErr; ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary fw-bolder">SAVE</button>
                </form>


            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>