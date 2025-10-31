<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <title>Title - Recenze</title> <!-- TODO branding -->

    <link rel="stylesheet" href="/node_modules/quill/dist/quill.bubble.css">
    <script src="/node_modules/quill/dist/quill.js"></script>
    <?php include('template/commonhead.php') ?>
</head>
<body>
<?php include 'template/navigation.php' ?>
<div class="mx-2 mx-md-4">
    <h2 class="text-center">Moje recenze</h2>
    <?php
    $api = new API();
    $user = $api->currentUser();
    if ($user != null && $user->role == Role::EDITOR): $reviews = $api->getUserReviews($user->id) ?>
    <ul class="list-group gap-3">
        <?php foreach ($reviews as $review): $article = $api->getArticle($review->article) ?>
        <div class="card">
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
                    <div class="col-lg-10">
                        <h5 class="card-title text-decoration-underline my-2"><?=htmlspecialchars($article->title)?></h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">
                            <i class="fas fa-calendar-days"></i> <?=$article->date->format('d.m.Y')?>
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

                <?php if ($article->status == 'waiting'): ?>
                <form id="reviewForm" data-id="<?= $review->id ?>">
                        <div class="row mx-3">
                            <div class="input-group mb-3 col-lg">
                                <label class="input-group-text" for="quality">Kvalita</label>
                                <input type="number" min="1" max="5" step="0.5"
                                       value="<?= $review->quality != -1 ? $review->quality : '' ?>"
                                       class="form-control" id="quality" name="quality">
                            </div>
                            <div class="input-group mb-3 col-lg">
                                <label class="input-group-text" for="language">Jazyk</label>
                                <input type="number" min="1" max="5" step="0.5"
                                       value="<?= $review->language != -1 ? $review->language : '' ?>"
                                       class="form-control" id="language" name="language">
                            </div>
                            <div class="input-group mb-3 col-lg">
                                <label class="input-group-text" for="relevancy">Relevance</label>
                                <input type="number" min="1" max="5" step="0.5"
                                       value="<?= $review->relevancy != -1 ? $review->relevancy : '' ?>"
                                       class="form-control" id="relevancy" name="relevancy">
                            </div>
                            <button type="submit" class="btn btn-success mb-3 col-12 col-lg-2">Uložit recenzi</button>
                        </div>
                </form>
                <?php endif ?>
            </div>
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
    $('form').on('submit', (e) => {
        e.preventDefault()

        const formData = $(e.target).serializeArray()
        let postData = {}
        $.each(formData, (k, v) => postData[v.name] = v.value)
        postData['id'] = $(e.target).attr('data-id')

        $.post({
            url: '/api/review',
            data: JSON.stringify(postData),
            success: () => location.reload(),
            processData: false,
            contentType: false
        })
    })
</script>
</html>