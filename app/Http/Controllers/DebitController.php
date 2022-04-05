<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Exception;
use App\Models\Debit;
use App\Models\User;
use Facades\App\Library\CommonLibrary;
use Log;

class DebitController extends Controller
{
    public $perPage = 10;
    public $search = '';
    public $id;
    public $user_ids = [];
    public $statusText = [
        '대기',
        '완료',
        '취소',
    ];
    public $slugText = [
        '승인',
        '취소',
    ];
    // $statusText[0]
    public function debit(Request $request)
    {
        // dump($request->all());
        $this->search = $request->input('search', null);
        $this->id = $request->input('id');
        $this->perPage = $request->input('perPage', 20);

        if($this->search){
            $this->user_ids = User::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('id', $this->search)
            ->get()->pluck('id');
        }        
        // dump($this->user_ids);
        $debits = Debit::when($this->search !== null,function ($query){
            return $query->whereIn('user_id', $this->user_ids);
        })
        ->when($this->id,function ($query, $id){
            return $query->where('id', $id);
        })
        ->orderBy('id', 'desc')
        ->with('user')
        ->paginate($this->perPage);
            
        // dump($debits); 
        return view('pages.debit', [
            'debit'=>$debits,
            'slugText'=>$this->slugText,
            'statusText'=>$this->statusText,
            'perPage'=>$this->perPage,
            'search'=>$this->search,
            'id'=>$this->id,
        ]);
        
    }

    public function confirm(Request $request)
    {
        // dd($request->all());

        $validateData = $request->validate([
            'id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $debits = Debit::find($validateData['id']);
            // dd($debits);
            $debits->status = 1;
            $debits->balance = $debits->user->balance; 
            $debits->user->balance -= $debits->amount;
            $debits->user->total_debit += $debits->amount;
            $debits->push();

            CommonLibrary::storePointLogs($debits, 201, 2);

            DB::commit();
            toastr()->success('승인처리되었습니다','message');
            return back();


        } catch (\Throwable $th) {
            DB::rollBack();
            toastr()->error('error');
            Log::error($th->getMessage());
            return back();
        }
        
            
    }        

    public function cancel(Request $request)
    {

        $validateData = $request->validate([
            'id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $debits = Debit::find($validateData['id']);
            
            $debits->status = 2;
          
            $debits->push();


            DB::commit();
            toastr()->success('취소처리되었습니다','message');
            return back();


        } catch (\Throwable $th) {
            DB::rollBack();
            toastr()->error('error');
            Log::error($th->getMessage());
            return back();
        }
        
            
    }           


    public function debitRestoreConfirm(Request $request)
    {
        // dd($request->all());

        $validateData = $request->validate([
            'id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $debits = Debit::find($validateData['id']);
            // dd($debits);
            if($debits->updated_at && $debits->updated_at < now()->subMinutes(5)){
                throw new Exception('복원가능시간은 처리후 5분 이내입니다.');
            }
            $debits->status = 0;
            $debits->balance = $debits->user->balance;
            $debits->user->balance += $debits->amount;
            $debits->user->total_debit += $debits->amount;
            $debits->push();


            CommonLibrary::storePointLogs($debits, 202, 1);
            

            DB::commit();
            toastr()->success('복원처리되었습니다','message');
            return back();


        } catch (\Throwable $th) {
            DB::rollBack();
            toastr()->error('error');
            Log::error($th->getMessage());
            return back();
        }
        
            
    }    
    
    public function debitRestoreCancel(Request $request)
    {
        // dd($request->all());

        $validateData = $request->validate([
            'id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $debits = Debit::find($validateData['id']);
            // dd($debits);
            if($debits->updated_at && $debits->updated_at < now()->subMinutes(5)){
                throw new Exception('복원가능시간은 처리후 5분 이내입니다.');
            }
            $debits->status = 0;
            // $debits->updated_at = null;
            
            $debits->push();


            DB::commit();
            toastr()->success('복원처리되었습니다','message');
            return back();


        } catch (\Throwable $th) {
            DB::rollBack();
            toastr()->error('error');
            Log::error($th->getMessage());
            return back();
        }
        
            
    }                

}    

