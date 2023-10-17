<?php

namespace Gtiger117\Athlo\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class VoucherEmailTemplate extends Model
{
    use HasFactory;
    use Searchable;
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = ['name', 'subject', 'description', 'notify_list'];

    protected $searchableFields = ['*'];

    protected $table = 'voucher_email_templates';

    protected $casts = [
        'subject' => 'array',
        'description' => 'array',
    ];

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }
}
