<?php
namespace App\Models;

use CodeIgniter\Model;

class WeatherLogModel extends Model
{
    protected $table = 'weather_logs';
    protected $allowedFields = ['city', 'temperature', 'fetched_at'];
}
