<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agente extends Model
{
    protected $fillable = ['nome'];

    /**
     * Relacionamento com SaudeDanos
     */
    public function saudeDanos(): HasMany
    {
        return $this->hasMany(SaudeDano::class);
    }
}
