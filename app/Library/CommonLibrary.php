<?php 

namespace App\Library;

use App\Models\PointLog;
use App\Models\User;


class CommonLibrary 
{
    public $types = [
        101 => '충전승인',
        102 => '충전복원',
        201 => '환전승인',
        202 => '환전복원',
        301 => '수동지급/회수'
    ];



    public function storePointLogs($logData, $type = 101, $plus = 1)
    {
        $amount = $logData->amount;

        if($plus !== 1 && $amount > 0 ){
            $amount = $amount * (-1);
        }
        

        PointLog::create([
            'user_id'=>$logData->user_id,
            'amount'=>$amount,
            'balance'=>$logData->user->balance,
            'type'=>$type,
            'type_text'=> $this->types[$type],
            'type_id'=>$logData->id,
            'slug' => $logData->slug ?: ''
        ]);
    }



    
}