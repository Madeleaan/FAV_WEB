<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <title>Title - Články</title> <!-- TODO branding -->

    <?php include('template/commonhead.php') ?>
</head>
<body>
<?php include 'template/navigation.php' ?>
<div class="mx-2 mx-md-4">
    <?php
    $api = new API();
    $user = $api->currentUser();
    if ($user != null && $user->role == Role::AUTHOR): $articles = $api->getUserArticles($user->login); ?>
    <ul class="list-group gap-3">
        <?php foreach ($articles as $article): ?>
        <div class="card">
            <div class="d-flex">
                <div class="card-body me-auto">
                    <h5 class="card-title text-decoration-underline mb-2"><?=$article->title?></h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary"><i class="fas fa-calendar-days"></i> <?=$article->date->format('d.m.Y')?></h6>
                    <p class="card-text"><b>Abstrakt: </b> <?=$article->abstract?></p>
                </div>
                <div class="btn-group ms-auto my-5 h-100">
                    <button type="button" class="btn btn-primary"><i class="fas fa-pencil me-1"></i>Upravit</button>
                    <button type="button" class="btn btn-warning" data-view-file="<?=$article->file?>"><i class="fas fa-eye me-1"></i>Zobrazit</button>
                    <button type="button" class="btn btn-outline-danger" data-delete="<?=$article->id?>"><i class="fas fa-trash me-1"></i>Vymazat</button>
                </div>
            </div>
            <iframe class="w-100 vh-100 d-none" src="../storage/articles/<?=$article->file?>"></iframe>
        </div>
        <?php endforeach ?>
    </ul>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">Obsah pouze pro autory článků</div>
    <?php endif ?>
</div>
<?php include 'template/footer.php' ?>
</body>
<script>
    $('button[data-view-file]').on('click', (e) => {
        let file = $(e.target).attr('data-view-file')
        $(`iframe[src*='${file}']`).each((_, el) => $(el).toggleClass('d-none'))
    })

    $('button[data-delete]').on('click', (e) => {
        let id = $(e.target).attr('data-delete')
        $.ajax({
            url: 'articles/?id=' + id,
            type: 'DELETE',
            processData: false,
            contentType: false,
            success: () => {
                $(e.target).parentsUntil('.card').parent().remove()
            },
            error: (data) => {
                console.log(data)
            }
        })
    })
</script>
</html>