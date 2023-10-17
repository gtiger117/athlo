<?php

namespace Gtiger117\Athlo\Models;

use Gtiger117\Athlo\Models\Scopes\Searchable;
use App\Models\Category;
use App\Models\CharValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalVoucher extends Model
{
    use HasFactory;
    use Searchable;

    public function excludecategories()
    {
        return $this->belongsToMany(Category::class,'exclude_promotional_voucher_category','promotional_voucher_id','category_id');
        
    }
    public function excludecharacteristics()
    {
        return $this->belongsToMany(CharValue::class,'exclude_promotional_voucher_characteristics','promotional_voucher_id','characteristic_id');
        
    }

    public function includePromotionalVoucherCategory()
    {
        return $this->hasMany(IncludePromotionalVoucherCategory::class);
    }
    public function includePromotionalVoucherCharacteristic()
    {
        return $this->hasMany(IncludePromotionalVoucherCharacteristic::class);
    }
    
}
