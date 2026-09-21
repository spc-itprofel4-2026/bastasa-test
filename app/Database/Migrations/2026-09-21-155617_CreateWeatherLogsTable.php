<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWeatherLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'city'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'temperature' => ['type' => 'FLOAT'],
            'fetched_at'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('weather_logs');
    }

    public function down()
    {
        $this->forge->dropTable('weather_logs');
    }
}
