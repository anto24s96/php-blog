<?php
session_start();

include "../db_connection.php";

// Verifico se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    // Reindirizzo l'utente alla pagina di accesso se non loggato
    header("Location: ../auth/login.php");
    exit();
}

// Recupero l'ID dell'utente loggato
$id_user = $_SESSION['user_id'];

// Faccio la query per recuperare i post associati all'utente loggato
$sql = "SELECT posts.*, categories.name AS category_name
        FROM posts
        LEFT JOIN categories ON posts.category_id = categories.id 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

// Se è stata inviata una nuova categoria e non è già stata inserita, la inserisco nel DB
if (isset($_POST['new_category']) && !isset($_SESSION['category_added'])) {
    $new_category = $_POST['new_category'];

    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $new_category);
    $stmt->execute();

    // Imposto una variabile di sessione per indicare che la categoria è stata inserita con successo
    $_SESSION['category_added'] = true;
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Post</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../partials/header.php' ?>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center">My Post</h1>
            </div>

            <div class="col-12 d-flex justify-content-end py-3">
                <div class="me-3">
                    <a class="btn btn-danger" href="./create.php">New Post</a>
                </div>
                <form method="POST">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="new_category" placeholder="add a category" aria-label="Inserisci un testo">
                        <button class="btn btn-primary" type="submit">Create</button>
                    </div>
                </form>

            </div>

            <!-- TABLE -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Category</th>
                        <th>Tools</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) {
                        while ($post = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $post['id'] ?></td>
                                <td><?php echo $post['title'] ?></td>
                                <td><?php echo substr($post['content'], 0, 50) ?>...</td>
                                <td><?php echo isset($post['category_name']) ? $post['category_name'] : "" ?></td>
                                <td>
                                    <a class="btn btn-outline-primary" href="./show.php?id=<?php echo $post['id']; ?>">DETAILS</a>
                                    <a class="btn btn-outline-primary" href="./edit.php?id=<?php echo $post['id']; ?>">MODIFY</a>

                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $post['id']; ?>">
                                        DELETE
                                    </button>

                                    <!-- Modale di conferma eliminazione -->
                                    <div class="modal fade" id="deleteModal<?php echo $post['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Conferma eliminazione</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Sei sicuro di voler eliminare il post "<span class="fst-italic"><?php echo $post['title']; ?>"?</span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                                    <form action="delete.php?id=<?php echo $post['id']; ?>" method="post">
                                                        <button type="submit" class="btn btn-danger">Elimina</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="4" class="text-center">Nessun post presente</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>