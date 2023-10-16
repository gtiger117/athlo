<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Searchable;

class IncludePromotionalVoucherCategory extends Model
{
    use HasFactory;
    use Searchable;

    protected $searchableFields = ['*'];

    protected $primaryKey = 'id';

    protected $table = 'include_promotional_voucher_category';

    public function promotionalVoucher()
    {
        return $this->belongsTo(PromotionalVoucher::class);
    }

    public function categories()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    

    
}
