<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WpTerms extends Model
{
    use HasFactory;

    protected $connection = 'mysql_read';
    protected $table = 'wp_terms';
    protected $primaryKey = 'term_id';
    public $timestamps = false;

    /**
     * Relationship to WpTermTaxonomy
     */
    public function termTaxonomy(): HasOne
    {
        return $this->hasOne(WpTermTaxonomy::class, 'term_id', 'term_id');
    }

    /**
     * Direct relationship to WpTermRelationships through termTaxonomy
     */
    public function termRelationships(): HasMany
    {
        return $this->hasMany(WpTermRelationships::class, 'term_taxonomy_id', 'term_id')
            ->join('wp_term_taxonomy', 'wp_term_relationships.term_taxonomy_id', '=', 'wp_term_taxonomy.term_taxonomy_id')
            ->where('wp_term_taxonomy.term_id', '=', $this->term_id);
    }

    /**
     * Relationship through termTaxonomy (recommended)
     */
    public function relationshipsThroughTaxonomy(): HasManyThrough
    {
        return $this->hasManyThrough(
            WpTermRelationships::class,
            WpTermTaxonomy::class,
            'term_id', // Foreign key on wp_term_taxonomy table
            'term_taxonomy_id', // Foreign key on wp_term_relationships table
            'term_id', // Local key on wp_terms table
            'term_taxonomy_id' // Local key on wp_term_taxonomy table
        );
    }
}
