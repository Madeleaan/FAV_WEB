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
                <?php switch($article->status):
                case 'waiting': ?>
                    <span class="badge text-bg-info">Čeká na hodnocení</span>
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

                <div class="row">
                    <div class="col-lg-9">
                        <h5 class="card-title text-decoration-underline my-2"><?=htmlspecialchars($article->title)?></h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">
                            <i class="fas fa-calendar-days"></i> <?=$article->date->format('d.m.Y')?>
                        </h6>
                    </div>
                    <div class="btn-group col-lg-3 my-auto justify-content-end">
                        <?php $disabled = sizeof($completedReviews) < 3 ? 'disabled' : '' ?>

                        <button class="btn btn-outline-success acceptArticle flex-lg-grow-0" <?= $disabled ?>>
                            <i class="fa-solid fa-square-check me-1"></i>Akceptovat
                        </button>
                        <button class="btn btn-outline-danger ms-1 denyArticle flex-lg-grow-0" <?= $disabled ?>>
                            <i class="fa-solid fa-square-xmark me-1"></i>Zamítnout
                        </button>
                    </div>
                </div>

                <div class="card-text col-lg-6 my-2">
                    <?php
                    if(is_array($editors) && sizeof($editors) > 0):?>
                        <div class="input-group">
                            <label class="input-group-text" for="selectEditor">Přidání editora</label>
                            <select class="form-select" id="selectEditor">
                                <?php foreach ($editors as $editor): ?>
                                    <option value="<?=$editor->id;?>"><?=htmlspecialchars($editor->name)?></option>
                                <?php endforeach ?>
                            </select>
                            <button type="button" class="btn btn-success addEditor">Přidat</button>
                        </div>
                    <?php endif ?>

                    <table class="table table-responsive table-striped table-bordered mt-2">
                        <thead>
                        <tr class="align-middle">
                            <th scope="col" class="w-50">Editor</th>
                            <th scope="col" class="w-25">Kvalita</th>
                            <th scope="col" class="w-25">Jazyk</th>
                            <th scope="col" class="w-25">Relevance</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if(is_array($reviews)): foreach ($reviews as $review): ?>
                        <tr class="align-middle">
                            <th scope="row" data-id="<?= $review->id ?>">
                                <?=htmlspecialchars($api->getUserFromId($review->editor)->name) ?>
                            </th>

                            <?php if($review->quality == -1): ?>
                            <td colspan="3"><span class="badge text-bg-secondary">Čeká se na recenzi</span></td>
                            <?php else: ?>
                            <td><?=$review->quality ?> / 5</td>
                            <td><?=$review->language ?> / 5</td>
                            <td><?=$review->relevancy ?> / 5</td>
                            <?php endif ?>

                            <td>
                                <button class="btn btn-sm btn-outline-danger deleteReview"><i class="fas fa-square-xmark"></i></button>
                            </td>
                        </tr>
                        <?php endforeach ?>
                        <?php endif ?>

                        <?php if (sizeof($reviews) < 3): ?>
                        <td colspan="5"><div class="alert alert-warning mb-0">
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

    $('.deleteReview').on('click', (e) => {
        const review = $(e.target).closest('tr').find('th')

        $.post({
            url: '/api/admin',
            data: JSON.stringify({
                'task': 'delete-review',
                'id': review.attr('data-id')
            }),
            success: () => location.reload()
        })
    })

    $('.acceptArticle').on('click', (e) => {
        const article = $(e.target.closest('.card'))
        console.log(article)

        $.post({
            url: '/api/admin',
            data: JSON.stringify({
                'task': 'accept-article',
                'id': article.attr('data-article'),
                'accept': 'true'
            }),
            success: () => location.reload()
        })
    })

    $('.denyArticle').on('click', (e) => {
        const article = $(e.target.closest('.card'))

        $.post({
            url: '/api/admin',
            data: JSON.stringify({
                'task': 'accept-article',
                'id': article.attr('data-article'),
                'accept': 'false'
            }),
            success: () => location.reload()
        })
    })
</script>
</html>