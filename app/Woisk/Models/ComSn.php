<?php

namespace App\Woisk\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ComSn
 */
class ComSn extends Model
{
    protected $table = 'com_sns';

    public $timestamps = false;

    protected $fillable = [
        'account',
        'count',
        'com_sns_isp_info_id'
    ];

    protected $guarded = [];

        
}