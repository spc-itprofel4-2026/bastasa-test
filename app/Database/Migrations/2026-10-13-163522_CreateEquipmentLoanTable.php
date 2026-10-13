<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEquipmentLoanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'borrower_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'equipment_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'days_borrowed' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'deposit_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false],
            'is_returned' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true],
            'borrowed_on' => ['type' => 'DATE', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('equipment_loans');
    }

    public function down()
    {
        $this->forge->dropTable('equipment_loans');
    }
}
