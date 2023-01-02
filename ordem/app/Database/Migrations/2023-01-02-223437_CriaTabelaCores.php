<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriaTabelaCores extends Migration
{
    public function up()
    {
        //envia para o banco de dados.
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true
            ],

            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '100'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('cores');
    }

    public function down()
    {
        //remove a tabele se ela ja existir no banco de dados
        $this->forge->dropTable('cores');
    }
}
