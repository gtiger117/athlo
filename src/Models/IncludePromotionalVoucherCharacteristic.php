<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Searchable;

class IncludePromotionalVoucherCharacteristic extends Model
{
    use HasFactory;
    use Searchable;

    protected $searchableFields = ['*'];

    protected $primaryKey = 'id';

    protected $table = 'include_promotional_voucher_characteristics';

    public function characteristic()
    {
        return $this->belongsTo(CharValue::class,'characteristic_id');
    }
}
