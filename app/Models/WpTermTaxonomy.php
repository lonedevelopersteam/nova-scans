<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WpTermTaxonomy extends Model
{
    use HasFactory;

    protected $connection = 'mysql_read';
    protected $table = 'wp_term_taxonomy';
    protected $primaryKey = 'term_taxonomy_id';
    public $timestamps = false;

    /**
     * Relationship back to WpTerms
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(WpTerms::class, 'term_id', 'term_id');
    }

    /**
     * Relationship to WpTermRelationships
     */
    public function termRelationships(): HasMany
    {
        return $this->hasMany(WpTermRelationships::class, 'term_taxonomy_id', 'term_taxonomy_id');
    }
}
