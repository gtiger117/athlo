<?php

namespace Gtiger117\Athlo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Gtiger117\Athlo\Models\Scopes\Searchable;

class Category extends Model
{
    use HasFactory;
    use Searchable;

    protected $searchableFields = ['CLMCATEGORY_ID','CLMCATEGORY_ML_NAME'];

    protected $table = 'tbpc_categories';

    protected $primaryKey = 'CLMCATEGORY_ID';

    public function category()
    {
        return $this->belongsTo(Category::class, 'CLMMASTER_CATEGORY_ID');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'CLMMASTER_CATEGORY_ID');
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'CLMMASTER_CATEGORY_ID');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'CLMMASTER_CATEGORY_ID');
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'CLMMASTER_CATEGORY_ID')->with('subcategories');
    }
}
