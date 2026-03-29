<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClasseMatiere extends Pivot
{
    use HasFactory;

    protected $table = 'classe_matieres';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'classe_annee_id',
        'matiere_id',
        'coefficient',
        'enseignant_id'
    ];

    protected $casts = [
        'coefficient' => 'decimal:1',
    ];

    public function classeAnnee()
    {
        return $this->belongsTo(ClasseAnnee::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class);
    }

    public function seances()
    {
        return $this->hasMany(Seance::class, 'classe_annee_id', 'classe_annee_id')
                    ->where('matiere_id', $this->matiere_id);
    }
}
