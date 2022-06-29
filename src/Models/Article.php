<?php

namespace AdminKit\Articles\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Article extends Model
{
    use AsSource, Attachable;
    use HasTranslations;
    use Sluggable;

    protected $casts = [
        'published_at' => 'datetime',
    ];
    protected $fillable = [
        'slug',
        'title',
        'content',
        'short_content',
        'active',
        'pinned',
        'published_at',
    ];
    protected $translatable = [
        'title',
        'content',
        'short_content',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function image()
    {
        return $this->morphToMany(Attachment::class, 'attachmentable', 'attachmentable');
    }
}
