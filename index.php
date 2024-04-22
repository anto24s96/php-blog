<?php
session_start();

include "./db_connection.php";

// Recupero tutti i post degli utenti
$sql = "SELECT posts.*, users.username, categories.name AS category_name
        FROM posts 
        INNER JOIN users ON posts.user_id = users.id
        LEFT JOIN categories ON posts.category_id = categories.id
        ORDER BY id DESC";


$result = $conn->query($sql);

// Creo un array per memorizzare i risultati
$posts = array();

//Verifico se sono presenti dei risultati
if ($result->num_rows > 0) {
    //Itero per recuperare i dati dei post degli utenti
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
} else {
    echo "Nessun post trovato";
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php require_once './partials/header.php' ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="text-center py-3">All Post</h2>
            </div>

            <?php foreach ($posts as $post) { ?>
                <div class="col-12 col-lg-8">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title fs-5 mb-3 fw-bold"><?php echo $post['title'] ?></h2>

                            <?php if (!empty($post['image'])) { ?>
                                <div class="py-3">
                                    <img src="posts/<?php echo $post['image']; ?>" class="img-fluid rounded" alt="image_cover">
                                </div>
                            <?php } ?>

                            <div class="mb-3">
                                <p class="card-text"><?php echo substr($post['content'], 0, 150) ?>...</p>
                            </div>

                            <div class="mb-3">
                                <?php if (!empty($post['category_name'])) { ?>
                                    <span class="badge bg-primary"><?php echo $post['category_name']; ?></span>
                                <?php } else { ?>
                                    <span class="badge bg-secondary">No category</span>
                                <?php } ?>
                            </div>

                            <div class="py-3 text-center">Author: <span class="fw-bold"><?php echo $post['username'] ?></span></div>

                            <div class="text-end">
                                <a class="btn btn-primary" href="./posts/show.php?id=<?php echo $post['id']; ?>">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>