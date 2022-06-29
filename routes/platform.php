<?php

use Illuminate\Support\Facades\Route;
use AdminKit\Articles\Screens\ArticleListScreen;
use AdminKit\Articles\Screens\ArticleEditScreen;

Route::group(['prefix' => 'articles'], function () {
    Route::screen('/', ArticleListScreen::class)->name('platform.articles.list');
    Route::screen('/create', ArticleEditScreen::class)->name('platform.articles.create');
    Route::screen('/{post}/edit', ArticleEditScreen::class)->name('platform.articles.edit');
});
