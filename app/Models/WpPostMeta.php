<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WpPostMeta extends Model
{
    use HasFactory;
    protected $connection = 'mysql_read';
    protected $table = 'wp_postmeta';

    public function post(): BelongsTo
    {
        return $this->belongsTo(WpPosts::class, 'post_id', 'ID');
    }
}
