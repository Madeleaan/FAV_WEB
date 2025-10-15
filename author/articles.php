<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <title>Title - Články</title> <!-- TODO branding -->

    <link rel="stylesheet" href="/node_modules/quill/dist/quill.bubble.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
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
        <div class="card" data-article="<?=$article->id;?>">
            <div class="card-body">
                <div class="d-flex">
                    <div class="me-auto">
                        <h5 class="card-title text-decoration-underline mb-2"><?=$article->title?></h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary"><i class="fas fa-calendar-days"></i> <?=$article->date->format('d.m.Y')?></h6>
                    </div>
                    <div class="btn-group ms-auto mb-auto">
                        <button type="button" class="btn btn-warning" data-view-file="<?=$article->file?>"><i class="fas fa-eye me-1"></i>Zobrazit</button>
                        <button type="button" class="btn btn-primary"
                                data-bs-toggle="modal" data-bs-target="#editModal" <?php if ($article->public) echo "disabled"?>>
                            <i class="fas fa-pencil me-1"></i>Upravit</button>
                        <button type="button" class="btn btn-outline-danger"
                                data-delete="<?=$article->id?>" <?php if ($article->public) echo "disabled"?>>
                            <i class="fas fa-trash me-1"></i>Vymazat</button>
                    </div>
                </div>
                <div class="card-text">
                    <b>Abstrakt: </b>
                    <div class="abstract"><?=$article->abstract?></div>
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

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upravit článek</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editTitle" class="form-label">Nadpis</label>
                    <input type="text" id="editTitle" class="form-control">
                </div>
                <div>
                    <label class="form-label">Abstrakt</label>
                    <div id="editEditor" class="bg-body-tertiary fs-6"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                <button type="button" class="btn btn-primary" id="editOk">Potvrdit</button>
            </div>
        </div>
    </div>
</div>
</body>
<script type="module">
    const editQuill = new Quill('#editEditor', {
        theme: 'bubble',
    })

    $('#editModal').on('show.bs.modal', (e) => {
        const article = $(e.relatedTarget).closest('.card[data-article]')
        const abstract = article.find('.abstract').html()
        editQuill.setContents(editQuill.clipboard.convert({html: abstract}), "silent")
        $('#editTitle').val(article.find('.card-title').text())
        $('#editOk').attr('data-article', article.attr('data-article'))
    })

    $('#editOk').on('click', (e) => {
        let data = {
            'id': $(e.target).attr('data-article'),
            'title': $('#editTitle').val(),
            'abstract': editQuill.getSemanticHTML().replaceAll('&nbsp;', ' ')
        }

        $.ajax({
            url: '/api/article',
            type: 'PUT',
            data: JSON.stringify(data),
            processData: false,
            contentType: false,
            success: () => location.reload(),
            error: (data) => console.log(data)
        })
    })

    $('button[data-view-file]').on('click', (e) => {
        let file = $(e.target).attr('data-view-file')
        $(`iframe[src*='${file}']`).each((_, el) => $(el).toggleClass('d-none'))
    })

    $('button[data-delete]').on('click', (e) => {
        let id = $(e.target).attr('data-delete')
        $.ajax({
            url: '/api/article?id=' + id,
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