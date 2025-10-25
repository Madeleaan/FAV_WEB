<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <title>Title</title> <!-- TODO branding -->

    <?php include('../template/commonhead.php') ?>
</head>
<body>
<?php include '../template/navigation.php' ?>
<div class="mx-2 mx-md-4">
    <h2 class="text-center">Správa článků</h2>
    <?php
    $api = new API();
    $articles = $api->listArticles();
    if (is_array($articles)):
        foreach ($articles as $article):?>
        <p><?= $article['title'] ?></p>
    <?php endforeach ?>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">Obsah pouze pro administrátory</div>
    <?php endif ?>
</div>
<?php include '../template/footer.php' ?>
</body>
</html>