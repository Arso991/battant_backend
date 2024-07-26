<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CollectionCategorie extends Pivot
{
    use HasFactory;

    protected $table = ['collection_categories'];

    protected $fillable = ['collection_id', 'category_id', 'start_date', 'end_date'];
}
