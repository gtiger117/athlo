<?php

namespace Gtiger117\Athlo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Searchable;

class CharValue extends Model
{
    use HasFactory;
    use Searchable;

    protected $searchableFields = ['CLMCHARVALUEID','CLMCHARVALUE_ML_NAME'];

    protected $primaryKey = 'CLMCHARVALUEID';

    protected $table = 'tbchars_values';
}
