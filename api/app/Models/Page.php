<?php

namespace App\Models;

use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Page extends Model
{
    use Searchable;

    public $timestamps = false;
    protected $fillable = [
        'label',
        'icon',
        'route',
        'order',
        'state',
        'page_id',
        'type',
    ];

    protected $appends = ['active'];

    public function parent() {
        return $this->belongsTo(Page::class,'page_id');
    }

    public function childrens() {
        return $this->hasMany(Page::class,'page_id');
    }

    public function menu() {
        return $this->belongsToMany(Menu::class,'pages_menu');
    }

    public function getActiveAttribute() {
        return false;
    }

}
