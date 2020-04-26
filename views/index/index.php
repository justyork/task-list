<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 25.04.2020
 */
/* @var $tasks [] */
/* @var $newTask \Models\Task */
/* @var $pagination \Libs\Pagination */

?>

<?= $this->render('index.form', ['model' => $newTask]) ?>

<?php if ($tasks): ?>
    <table class="table table-striped form-table" style="margin-top: 40px">
        <thead>
            <tr>
                <th><?= $pagination->sortLInks('name', 'Имя') ?></th>
                <th><?= $pagination->sortLInks('email', 'Email') ?></th>
                <th>Текст</th>
                <th><?= $pagination->sortLInks('status', 'Статус') ?></th>
                <th>Дата добавления</th>
                <?php if (!\Core\Auth::isGuest()): ?>
                    <th></th>
                    <th></th>
                <?php endif;?>

            </tr>
        </thead>
        <tbody>
            <?php foreach($tasks as $task): ?>
                <tr data-id="<?= $task->id ?>">
                    <td><?= $task->name ?></td>
                    <td><?= $task->email ?></td>
                    <td class="task-text"><?= $task->text ?></td>
                    <td>
                        <div><?= $task->status ? 'выполнено' : 'Открыта' ?></div>
                        <div><?= $task->is_updated ? 'отредактировано администратором' : '' ?></div>
                    </td>
                    <td><?= $task->created_at ?></td>
                    <?php if (!\Core\Auth::isGuest()): ?>
                        <td>
                            <input type="checkbox" class="checkbox-status" data-id="<?= $task->id ?>" <?= $task->status ? 'checked' : '' ?> />
                        </td>
                        <td>
                            <a href="#" class="fa fa-pencil update-text"></a>
                        </td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <nav class="navigation">
        <?= $pagination->links() ?>
    </nav>
<?php endif ?>
