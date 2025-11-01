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

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mt-2">
                            <thead>
                            <tr class="align-middle">
                                <th scope="col">Editor</th>
                                <th scope="col">Kvalita</th>
                                <th scope="col">Jazyk</th>
                                <th scope="col">Relevance</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php if(is_array($reviews)): foreach ($reviews as $review): ?>
                            <tr class="align-middle">
                                <th scope="row" data-id="<?= $review->id ?>" class="text-nowrap">
                                    <?=htmlspecialchars($api->getUserFromId($review->editor)->name) ?>
                                </th>

                                <?php if($review->quality == -1): ?>
                                <td colspan="3"><span class="badge text-bg-secondary">Čeká se na recenzi</span></td>
                                <?php else: ?>
                                <td class="stars text-nowrap"><?=$review->quality ?></td>
                                <td class="stars text-nowrap"><?=$review->language ?></td>
                                <td class="stars text-nowrap"><?=$review->relevancy ?></td>
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
            contentType: 'application/json',
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
            contentType: 'application/json',
            success: () => location.reload()
        })
    })

    $('.acceptArticle').on('click', (e) => {
        const article = $(e.target.closest('.card'))

        $.post({
            url: '/api/admin',
            data: JSON.stringify({
                'task': 'accept-article',
                'id': article.attr('data-article'),
                'accept': 'true'
            }),
            contentType: 'application/json',
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
            contentType: 'application/json',
            success: () => location.reload()
        })
    })

    $('.stars').each((_, el) => {
        const rating = Number(el.textContent);

        const full = new Array(Math.floor(rating + 1)).join('<i class="fas fa-star"></i>')
        const half = ((rating % 1) !== 0) ? '<i class="fas fa-star-half-stroke"></i>' : ''
        const empty = new Array(Math.floor(6 - rating)).join('<i class="far fa-star"></i>')

        el.innerHTML = full + half + empty
    })
</script>
</html>