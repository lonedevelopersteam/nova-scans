<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterHistory extends Model
{
    protected $table = 'chapter_histories';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = ["user_id", "slug_series", "slug_chapter"];
    public $timestamps = true;
}
