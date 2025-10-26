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
    if (is_array($articles)): ?>
    <ul class="list-group gap-3">
        <?php foreach ($articles as $article):
            $editors = $api->listAvailableEditors($article->id);
            $reviews = $api->getReviews($article->id);
            $completedReviews = array_filter($reviews, function ($review) {return $review->quality != -1;}); ?>
            <div class="card" data-article="<?=$article->id;?>">
                <div class="card-body">
                    <div class="d-flex gap-4">
                        <div class="me-auto">
                            <?php switch($article->status):
                            case 'waiting': ?>
                                <span class="badge text-bg-info">Hodnocený</span>
                            <?php break;
                            case 'accepted': ?>
                                <span class="badge text-bg-success">Akceptovaný</span>
                            <?php break;
                            case 'denied': ?>
                                <span class="badge text-bg-danger">Zamítnutý</span>
                            <?php break;
                            default: ?>
                                <span class="badge text-bg-secondary">Neznámý status</span>
                            <?php endswitch ?>
                            <h5 class="card-title text-decoration-underline mb-2"><?=$article->title?></h5>
                        </div>
                        <div class="btn-group ms-auto my-auto">
                            <?php $disabled = sizeof($completedReviews) < 3 ? 'disabled' : '' ?>
                            <button class="btn btn-outline-success" <?= $disabled ?>><i class="fa-solid fa-square-check me-1"></i>Akceptovat</button>
                            <button class="btn btn-outline-danger ms-1" <?= $disabled ?>><i class="fa-solid fa-square-xmark me-1"></i>Zamítnout</button>
                        </div>
                    </div>
                    <h6 class="card-subtitle mb-2 text-body-secondary"><i class="fas fa-calendar-days"></i> <?=$article->date->format('d.m.Y')?></h6>
                    <div class="card-text col-12 col-lg-6">
                        <?php
                        if(is_array($editors) && sizeof($editors) > 0):?>
                            <div class="input-group">
                                <label class="input-group-text" for="selectEditor">Přidání editora</label>
                                <select class="form-select" id="selectEditor">
                                    <?php foreach ($editors as $editor): ?>
                                        <option value="<?=$editor->id;?>"><?=$editor->name;?></option>
                                    <?php endforeach ?>
                                </select>
                                <button type="button" class="btn btn-success addEditor">Přidat</button>
                            </div>
                        <?php endif ?>
                        <table class="table table-responsive table-striped table-bordered mt-2">
                            <thead>
                            <tr>
                                <th scope="col">Editor</th>
                                <th scope="col">Kvalita</th>
                                <th scope="col">Jazyk</th>
                                <th scope="col">Relevance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($reviews)): foreach ($reviews as $review): ?>
                            <tr>
                                <th scope="row"><?=$api->getUserFromId($review->editor)->name ?></th>

                                <?php if($review->quality == -1): ?>
                                <td colspan="3"><span class="badge text-bg-secondary">Čeká se na recenzi</span></td>

                                <?php else: ?>
                                <td><?=$review->quality ?> / 5</td>
                                <td><?=$review->language ?> / 5</td>
                                <td><?=$review->relevancy ?> / 5</td>
                                <?php endif ?>
                            </tr>
                            <?php endforeach ?>
                            <?php endif ?>

                            <?php if (sizeof($reviews) < 3): ?>
                            <td colspan="4"><div class="alert alert-warning mb-0">
                                Nízký počet recenzí, přidejte ještě <?= 3 - sizeof($reviews) ?>
                            </div></td>
                            <?php endif ?>
                            </tbody>
                            </table>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">Obsah pouze pro administrátory</div>
    <?php endif ?>
</div>
<?php include '../template/footer.php' ?>
</body>
<script>
    $('.addEditor').on('click', (e) => {
        const article = $(e.target).closest('.card[data-article]')
        const editor = $(e.target).siblings('select')

        $.post({
            url: '/api/admin',
            data: JSON.stringify({
                'task': 'add-editor',
                'article': article.attr('data-article'),
                'editor': editor.val()
            }),
            success: () => location.reload()
        })
    })
</script>
</html>