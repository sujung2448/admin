<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Credit;
use App\Models\Debit;

use DB;
use Hash;
use Str;
use Exception;



class UsersController extends Controller
{
    public $perPage = 10;
    public $search = '';
    public $user_ids = [];
    public $userStatusText = [
        '가입대기', // 0
        '가입거부', // 1
        '정상사용', // 2
        '접속차단', // 3
        '탈퇴처리', // 4
    ];
    public $userStatusA = [[3,4],[0],[0,1],[0,1],[0,1]]; //현 상태에 따라 안보이게..
    

    public function users(Request $request)
    
    {
        
        $this->search = $request->input('search', null);
        $this->perPage = $request->input('perPage', 20);

        if($this->search){
            $this->user_ids = User::where('name', 'like', '%'.$this->search.'%')
                 ->orWhere('id', $this->search)
                 ->get()->pluck('id');
        }        
        // dump($this->user_ids);
        $users = User::when($this->search !== null, function ($query){
            return $query->whereIn('id', $this->user_ids);
        })
            ->with('credit')
            ->with('debit')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
           
            // foreach ($users as $key => $user) {
            
            //     $user->total_balance = ($user->credit->sum('amount')) - ($user->debit->sum('amount'));
            //     $user->total_credit = $user->credit->sum('amount');
            //     $user->total_debit = $user->debit->sum('amount');
            //     $this->perPage = $request->input('perPage', 100);
            //     $user->save();
            // } 
           
            return view('pages.users', [
                'users'=>$users,
                'perPage'=>$this->perPage,
                'search'=>$this->search,
                
        ]);
        
    }

    public function usersInfo(User $user)
    {
        $banks = DB::table('banks')
                ->get()->pluck('name')
                ->toArray();
        sort($banks);  //은행정보는 seeder로 따로 만듬.
        
        $credits = Credit::where('user_id',$user->id)
                    ->orderBy('id','desc')
                    ->limit(5)
                    ->get();
                   
        $debits = Debit::where('user_id',$user->id)
                    ->orderBy('id','desc')
                    ->limit(5)
                    ->get();
                    //  dd($debits);
        

        return view('users.info',[
            'user' => $user,
            'userStatusText' => $this->userStatusText,
            'userStatusA' => $this->userStatusA,
            'banks' => $banks,
            'credits' => $credits,
            'debits' => $debits,

        ]);
    }

    public function usersInfoUpdate(Request $request, $id)
    {
        // dd($request->all());
        $validation = $request->validate([
            'userStatus' => ['required'],
            'type' => ['required'],
            'bank' => ['nullable'],
            'account' => ['nullable', 'regex:/^[0-9.·-]+$/'],
            'accountName' => ['nullable'],
            'desc' => ['nullable'],
            'password' => ['nullable','min:4'],
            'name' => ['required','regex:/^[ㄱ-ㅎ|가-힣|a-z|A-Z|]+$/'], 
            'personal_code' => ['nullable', 'min:6', 'max:6', 'regex:/^[0-9|A-Z|]+$/'],
        ]);
        
        $user = User::find($id);
           
        
        $user->name = $validation['name'];   
        $user->type = $validation['type'];    // 유저타입변경 : 0 일반회원, 1 총판 회원, 2 대리점 회원, 3 유령회원 
        $user->status = $validation['userStatus'];   // 유저상태변경 : 0 대기, 1 거부, 2 정상, 3 차단, 4 탈퇴
        $user->bank = $validation['bank'];
        $user->account = $validation['account'];
        $user->account_name = $validation['accountName'];
        $user->desc = $validation['desc'];
        $user->personal_code = $validation['personal_code'];


        if($validation['password']){  //password 강제수정
            $user->password = Hash::make($request->input('password'));
            $user->re_password = $request->input('password');
        }
       
       
        $user -> push();

        toastr()->success('수정되었습니다.');
        return redirect()-> route('users.info',[$id]);
        

    }

    /**
     * 회원정보 본인코드 변경용 코드 반환 axios
     */
    public function getNewUserPersonalCode(Request $request)
    {
        $newPersonalCode = $this->createPersonalCode();

        return response()->json($newPersonalCode);
    }
    /**
     * 회원별 6자리 개인코드 생성
     */
    public function createPersonalCode()
    {
        $code = Str::upper(Str::random(6));
        $check = User::wherePersonalCode($code)->first();
        if(!$check){
            return $code;
        }
        return $this->createPersonalCode();
    }

   
}    

    









