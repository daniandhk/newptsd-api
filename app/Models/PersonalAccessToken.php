<?php

namespace App\Models;

use App\Traits\HasULID;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
 
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasULID;
}