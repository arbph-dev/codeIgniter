<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCodesNaf extends Migration
{
    public function up()
    {
        $this->forge->addField([  
            'codenaf' => ['type' => 'VARCHAR', 'constraint' => 10],  
            'nom' => ['type' => 'TEXT'],  
            'parentcode' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],  
            'created_at' => ['type' => 'DATETIME', 'null' => true],  
            'updated_at' => ['type' => 'DATETIME', 'null' => true],  
            ]);  
              
            $this->forge->addPrimaryKey('codenaf');  
            $this->forge->addKey('parentcode');  
              
            // FK AVANT createTable  
            $this->forge->addForeignKey(  
            'parentcode',  
            'codesnaf',  
            'codenaf',  
            'SET NULL',  
            'NO ACTION'  
            );  
              
            $this->forge->createTable('codesnaf');  
              
            // FULLTEXT après  
            $this->db->query('ALTER TABLE codesnaf ADD FULLTEXT nom (nom)');
    }

    public function down()
    {
        $this->forge->dropTable('codesnaf', true);
    }
}
