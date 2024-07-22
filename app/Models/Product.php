<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Attributs qui peuvent être assignés en masse
    protected $fillable = [
        'name',
        'description',
        'price',
        'size',
        'color',
        'quantity',
        'imageUrl',
        'stock',
        'category_id'
    ];

    // Cast JSON fields to array
    protected $casts = [
        'size' => 'array',
        'color' => 'array',
        'imageUrl' => 'array',
    ];

    // Définir la relation avec la table catégories
    public function categorie()
    {
        return $this->belongsTo(Category::class);
    }

    // Définir la relation avec la table utilisateurs pour les mises à jour
    /* public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    } */
}
