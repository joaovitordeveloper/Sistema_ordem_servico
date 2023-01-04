<?php

namespace App\Models;

use CodeIgniter\Model;

class CorModel extends Model
{
    protected $table            = 'cores';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nome'];

}
