<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Layout extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'containers',
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'containers' => 'array',
    ];

    public function layoutWidgets(): HasMany
    {
        return $this->hasMany(LayoutWidget::class);
    }

    public function widgets(): HasManyThrough
    {
        return $this->hasManyThrough(Widget::class, LayoutWidget::class, 'layout_id', 'id', 'id', 'widget_id');
    }
}
