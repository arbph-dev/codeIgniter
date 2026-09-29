<?php

// App/Models/PersonneParcoursModel.php
namespace App\Models;

use CodeIgniter\Model;
use App\Entities\PersonneParcours;

class PersonneParcoursModel extends Model
{
    protected $table            = 'personne_parcours';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = PersonneParcours::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'personne_id',
        'type',
        'titre',
        'description',
        'date_debut',
        'precision_debut',
        'date_fin',
        'precision_fin',
        'structure_objet',
        'structure_id',
        'adresse_id',
        'source',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'personne_id' => 'required|is_natural_no_zero',
        // NOT NULL en base — était permit_empty|max_length[255]
        'titre'       => 'required|max_length[255]',
        // FK vers parcours_types.id, bigint unsigned NOT NULL — était permit_empty|max_length[50]
        'type'        => 'required|is_natural_no_zero',
    ];
}
