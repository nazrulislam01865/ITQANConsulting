<?php

namespace App\Models\StarPmAminul;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['section_key', 'data'])]
class PortfolioSection extends Model
{
    protected $connection = 'starpmaminul';

    protected $table = 'portfolio_sections';

    protected function casts(): array
    {
        return [
            'data' => AsArrayObject::class,
        ];
    }
}
