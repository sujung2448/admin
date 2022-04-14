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
    public $recTree = [];
    public $recIds = [];
    public $perPage = 10;
    public $search = '';
    public $user_ids = [];
    public $user_under_ids = [];
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
        $popup = $request->input('popup', false);
        $this->search = $request->input('search', null);
        $this->searchRec = $request->input('searchRec', null);
        $this->perPage = $request->input('perPage', 20);

        if($this->search){
            $this->user_ids = User::where('name', 'like', '%'.$this->search.'%')
                 ->orWhere('id', $this->search)
                 ->get()->pluck('id');
        }
        if($this->searchRec){
            $this->user_under_ids = User::where('recommend_id', '=', $this->searchRec)
                 ->orWhere('code', $this->searchRec)
                 ->get()->pluck('id');
        }

        // dump($this->user_ids);
        $users = User::when($this->search !== null, function ($query){
            return $query->whereIn('id', $this->user_ids);
        })
            ->when($this->searchRec, function ($query){
            return $query->where(function($query){
                $query->whereIn('id',$this->user_under_ids);
            });
        })
            ->with('credit')
            ->with('debit')
            ->with('recommend')
            ->with('underUser')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
            
            
            foreach ($users as $key => $value) {
                $value->underUserCount = count($value->underUser);
            }

            return view('pages.users', [
                'users'=>$users,
                'perPage'=>$this->perPage,
                'search'=>$this->search,
                'searchRec' => $this->searchRec,
                'popup' => $popup,




                
        ]);
        
    }

    public function usersInfo(User $user)
    
    {
        $this->getRecommendTree($user,false);
        $recTrees = array_reverse($this->recTree);
        
        $banks = DB::table('banks')
                ->get()->pluck('name')
                ->toArray();
        sort($banks);  //은행정보는 seeder로 따로 만듬.
        
        $credits = Credit::where('user_id',$user->id)
                    ->where('status',1)
                    ->orderBy('id','desc')
                    ->limit(5)
                    ->get();
                   
        $debits = Debit::where('user_id',$user->id)
                    ->where('status', 1)
                    ->orderBy('id','desc')
                    ->limit(5)
                    ->get();
                    //  dd($debits);

        $underUsers = User::query()
            ->where('recommend_id', $user->id)
            ->where('status',2)
            ->with('underUser.confirmedCredit')
            ->with('underUser.confirmedDebit')
            ->get();

        $underTotalCredit = 0;
        $underTotalDebit = 0;
        $underTotalRecommend = 0;
        foreach ($underUsers as $under) {
            $underTotalCredit += $under->confirmedCredit->sum('amount');
            $underTotalDebit += $under->confirmedDebit->sum('amount');
            $underTotalRecommend += count($under->underUser);
        }
        

        
            return view('users.info',[
                'user' => $user,
                'userStatusText' => $this->userStatusText,
                'userStatusA' => $this->userStatusA,
                'banks' => $banks,
                'credits' => $credits,
                'debits' => $debits,
                'underUsers' => $underUsers,
                'underTotalCredit' => $underTotalCredit,
                'underTotalDebit' => $underTotalDebit,
                'underTotalRecommend' => $underTotalRecommend,
                'recTrees' => $recTrees,

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

    // 개인코드 변경 코드 반환 axios
    public function getNewUserPersonalCode(Request $request)
    
    {
        $newPersonalCode = $this->createPersonalCode();

            return response()->json($newPersonalCode);
    }
    
    // 개인코드 생성
    public function createPersonalCode()
    
    {
        $code = Str::upper(Str::random(6));
        $check = User::wherePersonalCode($code)->first();
        if(!$check){
            return $code;
        }
            return $this->createPersonalCode();
    }

   
    public function userRecommendUpdate(Request $request, User $user)
    
    {
        // dd($user);
        $validation = $request->validate([
            'recommendCode'=> ['required','min:6','max:6'],
        ]);

        $codeUser = User::where('personal_code', $validation['recommendCode'])->first();

        try {
            if($codeUser->status != 2){
                throw new Exception("변경하려는 추천인이 정상사용상태가 아닙니다.");
            }
            if($user->recommend_id == $codeUser->id){
                throw new Exception("기존과 같은 추천인을 입력하셨습니다. 코드를 확인해주세요.");
            }
            if($user->id == $codeUser->id){
                throw new Exception("회원본인을 추천인으로 입력하셨습니다. 코드를 확인해주세요.");
            }

            $user->recommend_id = $codeUser->id;
            $user->code = $codeUser->personal_code;
            $user->save();

            toastr()->success('추천인이 변경되었습니다.');

        } catch (\Throwable $th) {
            toastr()->error($th->getMessage());
        }

            return redirect()->route('users.info',[$user->id]);
    }

    public function getRecommendTree($user, $recId = false)
    {
        if($recId){
            $rec = User::where('id',$recId)->first();

            if($rec){
                $this->recIds[] = $rec->id;
                $stopid = in_array($rec->recommend_id,$this->recIds);
                array_push($this->recTree,$rec);

                if($rec->recommend_id && $stopid == false){
                    $this->getRecommendTree($rec, $rec->recommend_id);
                } else {
                    return $this->recTree;
                }
            }
            return $this->recTree;
        }else{
            array_push($this->recTree,$user);
            if($user->recommend_id){
                $this->getRecommendTree($user, $user->recommend_id);
            } else {
                return $this->recTree;
            }

        }
    }
}    

    









