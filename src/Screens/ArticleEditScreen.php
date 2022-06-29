<?php

namespace AdminKit\Articles\Screens;

use AdminKit\Articles\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ArticleEditScreen extends Screen
{
    public $article;

    public function query(Article $article): iterable
    {
        $article->load('attachment');
        return [
            'article' => $article,
        ];
    }

    public function name(): ?string
    {
        return $this->article->exists ? 'Редактировать новость' : 'Создать новость';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Добавить новость')
                ->icon('plus')
                ->method('save')
                ->canSee(!$this->article->exists),

            Button::make('Обновить')
                ->icon('note')
                ->method('save')
                ->canSee($this->article->exists),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->article->exists),
        ];
    }

    public function layout(): iterable
    {
        $layout = [];
        $defaultLocale = Lang::getLocale();
        $locales = config('admin-kit.locales');
        foreach ($locales as $locale) {
            $layout[$locale] = [
                Layout::rows([
                    Input::make("title[$locale]")
                        ->title("Заголовок $locale")
                        ->required($locale === $defaultLocale)
                        ->placeholder('Введите Заголовок')
                        ->value($this->article->getTranslation('title', $locale)),
                    Quill::make("short_content[$locale]")
                        ->title("Короткое описание $locale")
                        ->value($this->article->getTranslation('short_content', $locale)),
                    Quill::make("content[$locale]")
                        ->title("Описание $locale")
                        ->required($locale === $defaultLocale)
                        ->value($this->article->getTranslation('content', $locale)),
                ]),
            ];
        }

        return [
            Layout::rows([
                Cropper::make('image_id')
                    ->height(300)
                    ->width(500)
                    ->targetId()
                    ->value($this->article->image->first()?->id),
            ]),
            Layout::tabs($layout),
            Layout::rows([
                DateTimer::make('published_at')->enableTime()
                    ->title('Дата публикации')
                    ->required()
                    ->value($this->article->published_at),
                CheckBox::make('active')
                    ->value($this->article->active)
                    ->sendTrueOrFalse()
                    ->placeholder('Отображать на сайте'),
            ]),
        ];
    }

    public function save(Article $article, Request $request)
    {
        // validate
        $defaultLocale = Lang::getLocale();
        $locales = implode(',', config('admin-kit.locales'));
        $validated = $request->validate([
            'title' => ['required', "array:$locales"],
            "title.$defaultLocale" => ['required', 'string', 'max:255'],
            'short_content' => ['nullable', "array:$locales"],
            "short_content.$defaultLocale" => ['nullable', 'string', 'max:10000'],
            'content' => ['required', "array:$locales"],
            "content.$defaultLocale" => ['required', 'string', 'max:65535'],
            'active' => ['required', 'boolean'],
            'published_at' => ['required', 'date'],
            'image_id' => ['required', 'exists:attachments,id'],
        ]);

        // save
        $article->fill($validated)->save();
        $article->attachment()->sync([$validated['image_id']]);
        Alert::info('Вы успешно добавили новость.');

        return redirect()->route('platform.articles.list');
    }

    public function remove(Article $article)
    {
        $article->delete();
        Alert::info('Вы успешно удалили новость.');

        return redirect()->route('platform.articles.list');
    }
}
