<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdicionaColunaAtivaEmCores extends Migration
{
    public function up()
    {
        $this->forge->addColumn('cores',[
            'ativa' => [
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => false
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('cores','ativa');
    }
}
