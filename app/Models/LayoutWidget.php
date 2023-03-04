<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LayoutWidget extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'layout_id',
        'widget_id',
        'container',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content' => 'array',
    ];

    public $timestamps = false;

    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(Widget::class);
    }
}
