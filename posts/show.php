<?php
session_start();

include "../db_connection.php";

// Verifica se l'ID del post è stato passato tramite URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Reindirizzo l'utente alla pagina index.php se l'ID del post non è stato passato
    header("Location: index.php");
    exit();
}

// Recupero l'ID del post dalla query nell'url
$id_post = $_GET['id'];

//Faccio la query per andare a recuperare i dettagli del post
$stmt = $conn->prepare("
    SELECT posts.*, categories.name AS category_name
    FROM posts
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE posts.id = ?
");

$stmt->bind_param("i", $id_post);
$stmt->execute();
$result = $stmt->get_result();

//Verifico se è stato trovato un post con l'ID specificato
if ($result->num_rows > 0) {
    // Estrarre i dati dal post
    $post = $result->fetch_assoc();
} else {
    //Reindirizzo l'utente alla pagina index se il post non è stato trovato
    header("Location: index.php");
    exit();
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Detail</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php require_once '../partials/header.php' ?>

    <div class="container-fluid bg-light py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="post-detail p-4 rounded shadow">
                    <h2 class="text-center mb-4"><?php echo $post['title'] ?></h2>

                    <?php if (!empty($post['image'])) { ?>
                        <div class="image-container mb-4">
                            <img src="<?php echo $post['image']; ?>" class="img-fluid rounded" alt="Image Cover">
                        </div>
                    <?php } ?>

                    <div class="my-4">
                        <h5 class="text-center">Description</h5>
                        <p class="text-center"><?php echo $post['content'] ?></p>
                    </div>

                    <div class="text-center mb-4">
                        <?php if (!empty($post['category_name'])) { ?>
                            <span class="badge bg-primary"><?php echo $post['category_name']; ?></span>
                        <?php } else { ?>
                            <span class="badge bg-secondary">No category</span>
                        <?php } ?>
                    </div>

                    <div class="text-center">
                        <a href="../index.php" class="btn btn-primary">Back to All Posts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>