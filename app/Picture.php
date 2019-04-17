<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'article_id', 'picture',
    ];

    public function article() {
        return $this->belongsTo('App\Article');
    }
}
