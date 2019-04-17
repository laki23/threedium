<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'user_id', 'title', 'text', 
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
    public function picture() {
        return $this->hasMany('App\Picture');
    }
}
