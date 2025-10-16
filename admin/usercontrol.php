<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <title>Title</title> <!-- TODO branding -->

    <?php include('../template/commonhead.php') ?>
    <link rel="stylesheet" href="../node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <script src="../node_modules/datatables.net/js/dataTables.min.js"></script>
    <script src="../node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>
<?php include '../template/navigation.php' ?>
<div class="mx-2 mx-md-4 d-block overflow-x-hidden">
    <h2 class="text-center">Správa uživatelů</h2>
    <?php
        $api = new Api();
        $users = $api->listUsers();
        if (is_array($users)):
    ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Login</th>
                    <th>Jméno</th>
                    <th>Role</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="align-middle"><?php echo htmlspecialchars($user->login) ?></td>
                        <td class="align-middle"><?php echo htmlspecialchars($user->name) ?></td>
                        <td class="text-end align-middle"><?php echo $user->role->value ?></td>
                        <td class="text-end align-middle">
                            <?php echo $user->role->value < $api->currentUser()->role->value ? json_encode($user->enabled) : "" ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <div class="modal fade" id="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                        <button type="button" class="btn btn-primary" id="modalOk" data-bs-dismiss="modal">Potvrdit</button>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($users->getCode() == 403): ?>
        <div class="alert alert-danger" role="alert">Obsah pouze pro administrátory</div>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">Chyba: <?php echo $users->getMessage() ?></div>
    <?php endif ?>
</div>
<?php include '../template/footer.php' ?>
</body>
<script>
    $(async () => {
        const roles = {
            1: 'Autor',
            2: 'Editor',
            3: 'Admin',
            4: 'SUPERADMIN'
        }

        let admin;
        await $.get(
            `/api/user?login=${$('#userDropdown').text().trim()}`,
            (data) => admin = data
        )

        let table = $('table').DataTable({
            scrollX: true,
            order: [
                [2, 'desc'],
                [0, 'asc']
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/cs.json'
            },
            rowCallback: (row, data) => {
                $(row).children().each((i, el) => {
                    if (data['enabled'] === 'false') {
                        $(el).addClass('bg-danger bg-opacity-10')
                    } else {
                        $(el).removeClass('bg-danger bg-opacity-10')
                    }
                })
            },
            columns: [
                { data: 'login' },
                { data: 'user', type: 'czech'},
                {
                    data: 'role',
                    width: '10%',
                    render: (data, type, row, meta) => {
                        if (type !== 'display') return data
                        if (row.login === admin.login) return `<b>${roles[data]}</b>`

                        let html = `<select class="form-select roleSelect w-auto ms-auto" aria-label="Role select"
                            data-action="change" data-login="${row.login}" data-row=${meta.row}>`
                        for (const [k, v] of Object.entries(roles)) {
                            html += `<option value="${k}" data-bs-toggle="modal" data-bs-target="#modal"`
                            if (k === data) html += " selected"
                            if (k >= admin.role) html += " disabled "
                            html += `>${v}</option>`
                        }
                        html += `</select>`

                        $('.roleSelect')
                            .on('click', (ev) => ev.target.setAttribute('data-prev', ev.target.value))

                        return html
                    },
                },
                {
                    data: 'enabled',
                    width: '20%',
                    render: (data, type, row, meta) => {
                        if (type !== 'display') return data
                        const dataTags = `data-login="${row.login}" data-row=${meta.row} data-bs-toggle="modal" data-bs-target="#modal"`
                        const delBtn = `<button class="btn btn-outline-info ms-1" ${dataTags} data-action="delete">
                            <i class="fa-solid fa-trash-can me-1"></i>Smazat</button></div>`

                        if (data === 'true') {
                            return `<div class="btn-group"><button class="btn btn-outline-danger" ${dataTags} data-action="disable">
                                <i class="fa-solid fa-square-xmark me-1"></i>Zakázat</button>` + delBtn
                        } else if (data === 'false') {
                            return `<div class="btn-group"><button class="btn btn-outline-success" ${dataTags} data-action="enable">
                                <i class="fa-solid fa-square-check me-1"></i>Povolit</button>` + delBtn
                        } else {
                            return data
                        }
                    }
                }
            ],
        })

        $('#modal')
            .on('hidden.bs.modal', () => $('.modal-backdrop').hide())
            .on('show.bs.modal', async (e) => {
                const btn = e.relatedTarget;
                const action = btn.getAttribute('data-action');
                const login = btn.getAttribute('data-login');
                const title = $('.modal-title')
                const body = $('.modal-body')
                const okBtn = $('#modalOk');

                okBtn.attr('data-login', login);
                okBtn.attr('data-row', btn.getAttribute('data-row'));
                okBtn.attr('data-action', action);

                if (action === 'disable') {
                    title.text('Zakázat uživatele')
                    body.text(`Opravdu chcete zakázat uživatele '${login}'?`)
                } else if (action === 'enable') {
                    title.text('Povolit uživatele')
                    body.text(`Opravdu chcete povolit uživatele '${login}'?`)
                } else if (action === 'delete') {
                    title.text('Smazat uživatele')
                    body.text(`Opravdu chcete smazat uživatele '${login}'?\nTato akce nejde vrátit!!`)
                    body.html(body.html().replace('\n', '<br>'))
                } else {
                    const parent = btn.parentElement
                    title.text('Upravit uživatele')
                    await body.text(`Opravdu chcete nastavit roli uživatele '${parent.getAttribute('data-login')}'
                        na '${roles[btn.value]}'?`)
                    await okBtn.attr('data-role', btn.value)
                    okBtn.attr('data-login', parent.getAttribute('data-login'))
                    okBtn.attr('data-row', parent.getAttribute('data-row'))
                    parent.value = parent.getAttribute('data-prev');
                }
            })


        $('#modalOk').on('click', () => {
            const okBtn = $('#modalOk');
            const login = okBtn.attr('data-login')
            const row = Number(okBtn.attr('data-row'))
            const action = okBtn.attr('data-action');
            if (['enable', 'disable'].includes(action)) {
                $.post(
                    '/api/admin',
                    JSON.stringify({"task": "toggle-user", "login": login}),
                    () => {
                        const cell = table.cell({row: row, column: 3})
                        cell.data(cell.data() === 'true' ? 'false' : 'true').draw()
                    }
                )
            } else if (action === 'delete') {
                $.post(
                    '/api/admin',
                    JSON.stringify({"task": "delete-user", "login": login}),
                    () => {
                        table.row(row).remove().draw()
                    }
                )
            } else {
                $.post(
                    '/api/admin',
                    JSON.stringify({"task": "change-role", "login": login, "role": okBtn.attr('data-role')}),
                    () => {
                        table.cell({row: row, column: 2}).data(okBtn.attr('data-role')).draw()
                    }
                )
            }
        })
    })
</script>
</html>