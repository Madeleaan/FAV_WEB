<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <title>Title - Články</title> <!-- TODO branding -->

    <?php include('template/commonhead.php') ?>
</head>
<body>
<?php include 'template/navigation.php' ?>
<div class="mx-2 mx-md-4">
    <h2 class="text-center">Veřejné články</h2>
    <?php
    $api = new API();
    $articles = $api->getPublicArticles(); ?>
    <ul class="list-group gap-3">
        <?php foreach ($articles as $article): ?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-10">
                        <h5 class="card-title text-decoration-underline my-2"><?=htmlspecialchars($article->title)?></h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">
                            <i class="fas fa-calendar-days"></i> <?=$article->date->format('d.m.Y')?>
                        </h6>
                        <h6 class="card-subtitle mb-2 text-body-secondary">
                            <i class="fas fa-pen-fancy"></i> <?=htmlspecialchars($article->author->name)?>
                        </h6>
                    </div>
                    <div class="btn-group col-lg-2 my-auto justify-content-end">
                        <a type="button" class="btn btn-primary flex-lg-grow-0" href="/storage/articles/<?= $article->file ?>" target="_blank">
                            <i class="fas fa-download me-1"></i>Stáhnout
                        </a>
                    </div>
                </div>

                <div class="card-text my-2">
                    <b>Abstrakt: </b>
                    <div class="abstract"><?=$article->abstract?></div>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </ul>
</div>
<?php include 'template/footer.php' ?>
</body>
</html>