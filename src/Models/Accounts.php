<?php

namespace Gtiger117\Athlo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    use HasFactory;
    protected $table = 'tbaccounts_user';
    protected $primaryKey = 'CLM_ACCOUNT_ID';
}
