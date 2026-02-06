<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    /**
     * Boot the BelongsToCompany trait for a model.
     */
    protected static function bootBelongsToCompany(): void
    {
        // Automatically add company_id when creating a new record
        static::creating(function (Model $model) {
            if (Auth::check() && !$model->company_id) {
                $model->company_id = Auth::user()->company_id;
            }
        });

        // Automatically filter queries by company_id
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where($builder->getQuery()->from . '.company_id', Auth::user()->company_id);
            }
        });
    }

    /**
     * Get the company that owns the record.
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
