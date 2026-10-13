<?php
 
namespace App\Commands;
 
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
 
class ModelGenerate extends BaseCommand
{
    protected $group       = 'Model Driven';
    protected $name        = 'model:generate';
    protected $description = 'Generates code from a platform independent model file.';
    protected $usage       = 'model:generate <modelfile> [target]';
    protected $arguments   = [
        'modelfile' => 'Path of the model file, for example app/PIM/customer_order.json',
        'target'    => 'ci4 (migration and model class) or sql (a SQL script). Default: ci4',
    ];
 
    // Mapping rules for the target ci4: model type => [database type, size]
    private array $ci4Types = [
        'text'        => ['VARCHAR', 255],
        'longtext'    => ['TEXT', null],
        'wholenumber' => ['INT', 11],
        'decimal'     => ['DECIMAL', '10,2'],
        'yesno'       => ['TINYINT', 1],
        'date'        => ['DATE', null],
        'datetime'    => ['DATETIME', null],
    ];
 
    // Mapping rules for the target sql: model type => SQL type
    private array $sqlTypes = [
        'text'        => 'VARCHAR(100)',
        'longtext'    => 'TEXT',
        'wholenumber' => 'INT',
        'decimal'     => 'DECIMAL(10,2)',
        'yesno'       => 'TINYINT(1)',
        'date'        => 'DATE',
        'datetime'    => 'DATETIME',
    ];
 
    public function run(array $params)
    {
        $file   = $params[0] ?? null;
        $target = $params[1] ?? 'ci4';
 
        if ($file === null || ! is_file(ROOTPATH . $file)) {
            CLI::error('Model file not found. Example: app/PIM/customer_order.json');
            return;
        }
        if (! in_array($target, ['ci4', 'sql'], true)) {
            CLI::error('The target must be ci4 or sql.');
            return;
        }
 
        $model = json_decode((string) file_get_contents(ROOTPATH . $file), true);
        $error = $this->check($model);
        if ($error !== null) {
            CLI::error($error);
            return;
        }
 
        $entity = $model['entity'];
        $table  = $this->snake($entity) . 's';
        $fields = [];
        foreach ($model['fields'] as $f) {
            $fields[] = [
                'column'   => $this->snake($f['name']),
                'type'     => $f['type'],
                'required' => ! empty($f['required']),
            ];
        }
 
        if ($target === 'sql') {
            $this->makeSql($table, $fields);
        } else {
            $this->makeMigration($entity, $table, $fields);
            $this->makeModelClass($entity, $table, $fields);
        }
    }
 
    // Returns an error message, or null when the model file is fine.
    private function check($model): ?string
    {
        if (! is_array($model)) {
            return 'The model file is not valid JSON.';
        }
        $entity = $model['entity'] ?? '';
        if (! preg_match('/^[A-Z][A-Za-z]*$/', $entity)) {
            return 'entity must be letters only and start with a capital letter.';
        }
        if (empty($model['fields']) || ! is_array($model['fields'])) {
            return 'The model needs a fields list.';
        }
        foreach ($model['fields'] as $f) {
            $name = $f['name'] ?? '';
            if (! preg_match('/^[a-z][A-Za-z]*$/', $name) || $name === 'id') {
                return "Field name \"{$name}\" is not allowed. Use letters only, "
                    . 'start with a small letter, and do not use id.';
            }
            if (! isset($this->ci4Types[$f['type'] ?? ''])) {
                $allowed = implode(', ', array_keys($this->ci4Types));
                return "Field \"{$name}\" has an unknown type. Use one of: {$allowed}.";
            }
        }
        return null;
    }
 
    private function snake(string $name): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }
 
    private function makeMigration(string $entity, string $table, array $fields): void
    {
        $lines = "            'id' => ['type' => 'INT', 'constraint' => 11, "
            . "'unsigned' => true, 'auto_increment' => true],\n";
        foreach ($fields as $f) {
            [$dbType, $size] = $this->ci4Types[$f['type']];
            $part = "'type' => '{$dbType}'";
            if ($size !== null) {
                $part .= ", 'constraint' => " . (is_int($size) ? $size : "'{$size}'");
            }
            $part  .= ", 'null' => " . ($f['required'] ? 'false' : 'true');
            $lines .= "            '{$f['column']}' => [{$part}],\n";
        }
 
        $class = "Create{$entity}Table";
        $code  = "<?php\n\nnamespace App\\Database\\Migrations;\n\n"
            . "use CodeIgniter\\Database\\Migration;\n\n"
            . "class {$class} extends Migration\n{\n    public function up()\n    {\n"
            . "        \$this->forge->addField([\n{$lines}        ]);\n"
            . "        \$this->forge->addKey('id', true);\n"
            . "        \$this->forge->createTable('{$table}');\n    }\n\n"
            . "    public function down()\n    {\n"
            . "        \$this->forge->dropTable('{$table}');\n    }\n}\n";
 
        $folder = APPPATH . 'Database/Migrations/';
        $old    = glob($folder . "*_{$class}.php");
        $path   = $old ? $old[0] : $folder . date('Y-m-d-His') . "_{$class}.php";
        file_put_contents($path, $code);
        CLI::write('Wrote ' . str_replace(ROOTPATH, '', $path), 'green');
    }
 
    private function makeModelClass(string $entity, string $table, array $fields): void
    {
        $names   = array_map(fn ($f) => "'" . $f['column'] . "'", $fields);
        $allowed = implode(', ', $names);
        $code    = "<?php\n\nnamespace App\\Models;\n\nuse CodeIgniter\\Model;\n\n"
            . "class {$entity}Model extends Model\n{\n"
            . "    protected \$table         = '{$table}';\n"
            . "    protected \$primaryKey    = 'id';\n"
            . "    protected \$allowedFields = [{$allowed}];\n}\n";
 
        $path = APPPATH . "Models/{$entity}Model.php";
        file_put_contents($path, $code);
        CLI::write('Wrote ' . str_replace(ROOTPATH, '', $path), 'green');
    }
 
    private function makeSql(string $table, array $fields): void
    {
        $rows = ["    id INT AUTO_INCREMENT PRIMARY KEY"];
        foreach ($fields as $f) {
            $type   = $this->sqlTypes[$f['type']];
            $rows[] = "    {$f['column']} {$type}" . ($f['required'] ? ' NOT NULL' : '');
        }
        $folder = WRITEPATH . 'generated/';
        if (! is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        $path = $folder . "{$table}.sql";
        $body = implode(",\n", $rows);
        file_put_contents($path, "CREATE TABLE {$table} (\n{$body}\n);\n");
        CLI::write('Wrote ' . str_replace(ROOTPATH, '', $path), 'green');
    }
}