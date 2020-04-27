<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */
/* @var $model \Models\Task */
?>


<?php $form = new \Libs\Form() ?>

    <h2>Добавить задачу</h2>
    <div class="row">
        <div class="col-sm-6">

            <?= $form->field($model, 'name')->textField() ?>
        </div>
        <div class="col-sm-6">

            <?= $form->field($model, 'email')->textField() ?>
        </div>
    </div>
    <?= $form->field($model, 'text')->textarea() ?>
    <hr class="mb-4">
    <fieldset>
        <button class="btn btn-success" type="submit">Добавить</button>
    </fieldset>

<?php $form->end(); ?>
