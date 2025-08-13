<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class WpTermRelationships extends Model
{
    use HasFactory;

    protected $connection = 'mysql_read';
    protected $table = 'wp_term_relationships';
    public $timestamps = false;

    // Composite primary key (jika tidak ada primary key tunggal)
    protected $primaryKey = ['object_id', 'term_taxonomy_id'];
    public $incrementing = false;

    /**
     * Relationship to WpTermTaxonomy
     */
    public function termTaxonomy(): BelongsTo
    {
        return $this->belongsTo(WpTermTaxonomy::class, 'term_taxonomy_id', 'term_taxonomy_id');
    }

    /**
     * Relationship to WpTerms through termTaxonomy
     */
    public function term(): HasOneThrough
    {
        return $this->hasOneThrough(
            WpTerms::class,
            WpTermTaxonomy::class,
            'term_taxonomy_id', // Foreign key on wp_term_taxonomy
            'term_id', // Foreign key on wp_terms
            'term_taxonomy_id', // Local key on wp_term_relationships
            'term_id' // Local key on wp_term_taxonomy
        );
    }
}
