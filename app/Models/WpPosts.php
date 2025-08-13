<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WpPosts extends Model
{
    use HasFactory;

    protected $connection = 'mysql_read';
    protected $table = 'wp_posts';
    protected $primaryKey = 'ID';

    public function meta(): HasMany
    {
        return $this->hasMany(WpPostMeta::class, 'post_id', 'ID');
    }

    public function genres()
    {
        return $this->belongsToMany(
            WpTerms::class,
            'wp_term_relationships',
            'object_id',
            'term_taxonomy_id'
        )->with('termTaxonomy');
    }
}
