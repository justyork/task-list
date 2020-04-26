<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */
?>

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">Task list</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="/">Главная <span class="sr-only">(current)</span></a>
            </li>
            <?php if (\Core\Auth::isGuest()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/login">Вход</a>
                </li>
            <?php else:?>
                <li class="nav-item">
                    <a class="nav-link" href="/logout">Выход</a>
                </li>
            <?php endif ?>

        </ul>
    </div>
</nav>
