<?php

namespace AdminKit\Articles\Screens;

use AdminKit\Articles\Models\Article;
use AdminKit\Articles\Layouts\ArticleListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ArticleListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'article' => Article::orderBy('id', 'desc')->paginate(),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Новости';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить')
                ->icon('plus')
                ->route('platform.articles.create')
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            ArticleListLayout::class,
        ];
    }

    public function remove(Request $request): void
    {
        Article::findOrFail($request->get('id'))->delete();
        Toast::info('Новость успешно удалена');
    }
}
