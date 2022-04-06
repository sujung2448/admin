<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Log;
use Exception;
use App\Models\User;
use App\Models\Credit;
use App\Models\Debit;
use Facades\App\Library\CommonLibrary;




class CashController extends Controller
{
    public function manualCashChange(Request $request, User $user)
    {
        //    dd($request->all());
        $validation = $request->validate([
            'manualAmount'=> ['required','numeric'],
            'manualMemo'=> ['required'],
        ]); 

        $amount = $validation['manualAmount'];
        $memo = $validation['manualMemo'];
        
        
        try {
            DB::beginTransaction();
            
            if($user->balance + $amount < 0) {
                throw new Exception("조정금액이 잔액을 초과했습니다.");
            }
            
            if($amount == 0) {
                throw new Exception("조정금액이 0입니다. 확인해주세요.");
            }

            $manual = (object)([ //오브젝트로 변환 <-> (array)
                'user_id' => $user->id,
                'user' => $user,
                'amount'=> $amount,
                'balance'=> $user->balance + $amount,
                'id'=> 0,
                'slug' => $memo,
            ]);
            // dump($user);
            // dd($manual->amount);
             
            CommonLibrary::storePointLogs($manual, 301);
            
            $user->balance += $amount;
            $user->push();

            DB::commit();
            toastr()->success('수동으로 금액이 반영되었습니다.');
            return back();
            
            
        } catch (\Throwable $th) {
            DB::rollBack();
            toastr()->error($th->getMessage());
            Log::error($th->getMessage());
            return back()->withInput();
            
        }

    }

}
