<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */
/* @var $user \Models\User */
?>

<?php $form = new \Libs\Form(['class' => 'form-signin']) ?>
    <a href="/">&larr; Go back</a>
    <h1 class="h3 mb-3 font-weight-normal">Вход</h1>

    <?= $form->field($user, 'name')->label(false)->textField(['placeholder' => 'Имя']) ?>
    <?= $form->field($user, 'password')->label(false)->password(['placeholder' => 'Пароль']) ?>

<hr class="mb-4">
<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
<p class="mt-5 mb-3 text-muted">© 2020</p>
<?php $form->end() ?>

