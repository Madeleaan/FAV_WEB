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
<div class="mx-5 d-block overflow-x-hidden">
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
                    <th>Aktivní</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user->login) ?></td>
                        <td><?php echo htmlspecialchars($user->name) ?></td>
                        <td class="text-start"><?php echo $user->role->value ?></td>
                        <td class="text-end"><?php echo $user->role->value < $api->currentUser()->role->value ? json_encode($user->enabled) : "" ?></td>
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
        <div class="alert alert-danger" role="alert">Nedostatečná práva pro zobrazení</div>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">Chyba: <?php echo $users->getMessage() ?></div>
    <?php endif ?>
</div>
<?php include '../template/footer.php' ?>
</body>
<script>
    $(() => {
        let table = $('table').DataTable({
            scrollX: true,
            order: [[2, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/cs.json'
            },
            columns: [
                { data: 'login' },
                { data: 'user', type: 'czech'},
                {
                    data: 'role',
                    render: (data, type) => {
                        if (type !== 'display') return data
                        const roles = {
                            1: 'Autor',
                            2: 'Editor',
                            3: 'Admin',
                            4: 'SUPERADMIN'
                        }
                        return roles[data] ?? 'Neznámá role'
                    },
                },
                {
                    data: 'enabled',
                    render: (data, type, row, meta) => {
                        if (type !== 'display') return data
                        if (data === 'true') {
                            return `<button class="btn btn-outline-danger" data-login=${row.login} data-row=${meta.row}
                                data-action="disable" data-bs-toggle="modal" data-bs-target="#modal">ⓧ Zakázat</button>`
                        } else if (data === 'false') {
                            return `<button class="btn btn-outline-success" data-login=${row.login} data-row=${meta.row}
                                data-action="enable" data-bs-toggle="modal" data-bs-target="#modal">☑ Povolit</button>`
                        } else {
                            return data
                        }
                    }
                }
            ],
        })

        $('#modal').on('show.bs.modal', (e) => {
            const btn = e.relatedTarget;
            const action = btn.getAttribute('data-action');
            const okBtn = $('#modalOk');

            okBtn.attr('data-login', btn.getAttribute('data-login'));
            okBtn.attr('data-row', btn.getAttribute('data-row'));

            if (action === 'disable') {
                $('.modal-title').text('Zakázat uživatele')
                $('.modal-body').text('Opravdu chcete zakázat uživatele?')
            } else if (action === 'enable') {
                $('.modal-title').text('Povolit uživatele')
                $('.modal-body').text('Opravdu chcete povolit uživatele?')
            }
        })

        $('#modalOk').on('click', () => {
            const okBtn = $('#modalOk');
            const login = okBtn.attr('data-login')
            const row = Number(okBtn.attr('data-row'))
            $.post(
                '/api/admin',
                JSON.stringify({"task": "toggle-user", "login": login}),
                () => {
                    const cell = table.cell({row: row, column: 3})
                    cell.data(cell.data() === 'true' ? 'false' : 'true').draw()
                }
            )
        })
    })
</script>
</html>