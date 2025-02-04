<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCompany extends Pivot
{
    //
    protected $table = 'users_companies';
}
