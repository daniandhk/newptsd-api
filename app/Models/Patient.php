<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends BaseModel
{
    protected $table = 'patients';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'datebirth',
        'city',
        'province',
        'phone',
        'image',
    ];

    protected $appends = [
        'latest_test',
        'has_relation'
    ];
    
    function getLatestTestAttribute(){
        return $this->tests()->with('test_type')->whereNull(['videocall_date', 'videocall_link'])->orderBy('created_at', 'desc')->first();
    }

    function getHasRelationAttribute(){
        $relation = $this->relations()->where('is_active', true)->first();
        if($relation){
            return true;
        }
        return false;
    }

    public function tests() {
        return $this->hasMany(Test::class);
    }

    public function relations() {
        return $this->hasMany(Relation::class);
    }

    public function guardian() {
        return $this->hasOne(Guardian::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
