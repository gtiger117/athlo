<?php

namespace Gtiger117\Athlo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Gtiger117\Athlo\Models\Scopes\Searchable;

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
