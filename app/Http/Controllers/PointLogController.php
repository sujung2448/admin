<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Models\PointLog;
use App\Models\User;
use Facades\App\Library\CommonLibrary;


class PointLogController extends Controller
{
    public $perPage = 10;
    public $search = '';
    public $user_ids = [];
    public $typeIdByText = [
        101 => 'credit', //충전승인
        102 => 'credit', //충전복원
        201 => 'debit', //환전승인
        202 => 'debit', //환전복원
        // 301 => 'manual', //수동지급/회수
        
    ];


    public function pointlog(Request $request)
    {
        
        // dump($request->all());
        $this->search = $request->input('search');
        $this->perPage = $request->input('perPage', 20);

        if($this->search){
            $this->user_ids = User::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('id', $this->search)
            ->get()->pluck('id');
        }        
        // dump($this->user_ids);
        $pointlogs = PointLog::when($this->search !== null,function ($query){
            return $query->whereIn('user_id', $this->user_ids);
        })
        ->orderBy('id', 'desc')
        ->with('user')
        ->paginate($this->perPage);
        

        foreach ($pointlogs as $key => $value) {
            $value->link = '';
            if(array_key_exists($value->type, $this->typeIdByText)){
                $value->link = route($this->typeIdByText[$value->type],['id'=>$value->type_id]);
            }
            
        } //자세히보기 링크
            
        return view('pages.pointlog', [
            'pointlog'=>$pointlogs,
            // 'statusText'=>$this->statusText,
            // 'slugText'=>$this->slugText,
            'perPage'=>$this->perPage,
            'search'=>$this->search,
        ]);
        

    }
}
