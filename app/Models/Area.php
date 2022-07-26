<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\States;
use App\Models\City;

class Area extends Model
{
    use HasFactory;

    protected $table = 'trenta_master_area'; 
    
    protected $fillable = [
        'country_id',
        'state_id',
        'city_id',
        'area_name',
        'pincode',
        'created_ip_address',
        'modified_ip_address',
        'created_by',
        'modified_by'
    ];

    public function country()
    {
        return $this->hasOne(Country::class,'id','country_id')->select('id','country_name');
    }

    public function states()
    {
        return $this->hasOne(States::class,'id','state_id')->select('id','state_name');
    }

    public function cities()
    {
        return $this->hasOne(City::class,'id','city_id')->select('id','city_name');
    }
}
