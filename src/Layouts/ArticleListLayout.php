<?php

namespace AdminKit\Articles\Layouts;

use AdminKit\Articles\Models\Article;
use Illuminate\Support\Facades\Lang;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ArticleListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'article';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        $locale = Lang::locale();
        return [
            TD::make("id", '#')->render(function (Article $article) {
                return Link::make($article->id)->route('platform.articles.edit', $article->id);
            }),
            TD::make("title.$locale", 'Заголовок'),
            TD::make('active', 'Активен')->alignCenter()->bool(),
            TD::make('published_at', 'Дата публикации')->render(function (Article $article) {
                return $article->published_at->format('d.m.Y H:i');
            }),
            TD::make('edit', 'Действия')->render(function (Article $article) {
                return DropDown::make()
                    ->icon('options-vertical')
                    ->list([
                        Link::make(__('Edit'))
                            ->route('platform.articles.edit', $article->id)
                            ->icon('pencil'),
                        Button::make(__('Delete'))
                            ->method('remove')
                            ->icon('trash')
                            ->confirm(__('Вы уверены, что хотите удалить новость?'))
                            ->parameters([
                                'id' => $article->id,
                            ]),
                    ]);
            }),
        ];
    }
}
