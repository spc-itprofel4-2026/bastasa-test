<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentLoanModel extends Model
{
    protected $table         = 'equipment_loans';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['borrower_name', 'equipment_name', 'days_borrowed', 'deposit_amount', 'is_returned', 'borrowed_on'];
}
