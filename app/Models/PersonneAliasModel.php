<?php

// app/Models/PersonneAliasModel.php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\PersonneAlias;

class PersonneAliasModel extends Model
{
    protected $table            = 'personne_alias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = PersonneAlias::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'personne_id',
        'alias',
        'alias_type',
        'is_principal',
        'date_debut',
        'date_fin',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'personne_id'  => 'required|is_natural_no_zero',
        'alias'        => 'required|max_length[150]',
        // ENUM exact de la base — était max_length[50]
        'alias_type'   => 'permit_empty|in_list[pseudonyme,nom_naissance,nom_usage,nom_scene,nom_plume,translitteration,autre]',
        // NOT NULL DEFAULT 0 en base — permit_empty retiré
        'is_principal' => 'in_list[0,1]',
    ];
}
