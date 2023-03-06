<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriaTabelaGrupos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
            ],
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => '240',
            ],
            'exibir' => [// se o grupo estiver com esse campo como true ai sera exibido como opção na hora de definir o responsavel pela ordem de serviço
                'type' => 'BOOLEAN',
                'null' => false,
            ],
            'criado_em' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'atualizado_em' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'deletado_em' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nome');

        $this->forge->createTable('grupos');
    }

    public function down()
    {
        $this->forge->dropTable('grupos');
    }
}
?>