<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Exception;
use App\Models\Credit;
use App\Models\User;
use Facades\App\Library\CommonLibrary;
use Log;

class CreditController extends Controller
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
    public function credit(Request $request)
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
        $credits = Credit::when($this->search !== null,function ($query){
            return $query->whereIn('user_id', $this->user_ids);
        })
        ->when($this->id,function ($query, $id){
            return $query->where('id', $id);
        })
        ->orderBy('id', 'desc')
        ->with('user')
        ->paginate($this->perPage);
            
        
        // dump($credits); 
        return view('pages.credit', [
            'credit'=>$credits,
            'statusText'=>$this->statusText,
            'slugText'=>$this->slugText,
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

            $credits = Credit::find($validateData['id']);
            // dd($credits);
            $credits->status = 1;
            $credits->balance = $credits->user->balance;
            $credits->user->balance += $credits->amount;
            $credits->user->total_credit += $credits->amount;
            $credits->push();

            CommonLibrary::storePointLogs($credits);

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

            $credits = Credit::find($validateData['id']);
            
            $credits->status = 2;
          
            $credits->push();

            

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
    
            
    public function creditRestoreConfirm(Request $request)
    {
        // dd($request->all());

        $validateData = $request->validate([
            'id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $credits = Credit::find($validateData['id']);
            // dd($credits);
            if($credits->updated_at && $credits->updated_at < now()->subMinutes(5)){
                throw new Exception('복원가능시간은 처리후 5분 이내입니다.');
            }
            $credits->status = 0;
            $credits->balance = $credits->user->balance;
            $credits->user->balance -= $credits->amount;
            $credits->user->total_credit -= $credits->amount;
            $credits->push();

            CommonLibrary::storePointLogs($credits,102,2);

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
    
    public function creditRestoreCancel(Request $request)
    {
        // dd($request->all());

        $validateData = $request->validate([
            'id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $credits = Credit::find($validateData['id']);
            // dd($credits);
            if($credits->updated_at && $credits->updated_at < now()->subMinutes(5)){
                throw new Exception('복원가능시간은 처리후 5분 이내입니다.');
            }
            $credits->status = 0;
            // $credits->updated_at = null;
            
            $credits->push();


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
