@extends('adminlte::page-popup')

@section('title', '회원정보')

@section('content_header')

@stop

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-8 sm:gap-4">
    <div class="col-span-3">
        <form id="usersInfoForm" action="{{ route('users.info.update', ['user' => $user->id]) }}" method="POST" autocomplete="off">
            @csrf
            <div class="card">
                <div class="card-header flex">기본정보</div>
                <div class="card-body">
                    <div class="flex justify-center">
                        <span>{{$user->name}} @include('partials.user-status')</span>   
                    </div>
                    <div class="flex justify-between my-2">
                        <span class="font-bold">회원번호</span>
                        <span class="text-gray-500">{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between my-2">
                        <span class="font-bold">가입일</span>
                        <span class="text-gray-500">{{ $user->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label">분류</label>
                        <div class="col-sm-8">
                            <select class="form-select form-select-sm" name="type">
                                <option value="0" @if($user->type == 0) selected @endif>일반 회원</option>
                                <option value="1" @if($user->type == 1) selected @endif>총판 회원</option>
                                <option value="2" @if($user->type == 2) selected @endif>대리점 회원</option>
                                <option value="3" @if($user->type == 3) selected @endif>유령 회원</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label">상태</label>
                        <div class="col-sm-8">
                            <select name="userStatus" class="form-select form-select-sm">
                                @foreach ($userStatusText as $status => $item)
                                    @if (in_array($status,$userStatusA[$user->status]))
                                        @continue
                                    @endif
                                    <option @if($user->status === $status) selected @endif value="{{ $status }}">{{ $item  }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label">잔액</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm text-right" id="userBalance" value="{{ number_format($user->balance) }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label">이름</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm text-center"  name="name" value="{{ $user->name }}">
                            @error('name')
                                <b class="text-danger">{{ $message }}</b>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label">예금주</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm text-center"  name="accountName" value="{{ $user->account_name }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label">은행</label>
                        <div class="col-sm-8">
                            <select class="form-select form-select-sm" name="bank">
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank }}" @if($bank == $user->bank) selected @endif>{{ $bank }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label">계좌번호</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm text-center"  name="account" value="{{ $user->account }}">
                            @error('account')
                                <b class="text-danger">{{ $message }}</b>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label">Email</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm text-center"  name="email" value="{{ $user->email }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label">비밀번호</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm text-center"  name="password" >
                            @error('password')
                                <b class="text-danger">{{ $message }}</b>
                            @enderror
                        </div>
                        <small class="text-red text-right">회원의 비번을 강제로 변경합니다.</small>
                    </div>
                    <div class="form-group">
                        <label for="">특이사항</label>
                        <textarea name="desc"  cols="30" rows="5" class="form-control text-xs">{{ $user->desc }}</textarea>
                    </div>
                    <div class="card-footer flex justify-end">
                        <span class="btn btn-primary" onclick="updateUsersInfo({{ $user->id }})">정보수정</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-span-5">
        @include('users.info-credits') 
        @include('users.info-debits') 
        <div class="card">
            <div class="card-body flex">
                <div class="flex-1 flex justify-center">
                    <span>총충전 : </span>
                    <span>{{ number_format($user->total_credit) }}</span>
                </div>
                <div class="flex-1 flex justify-center">
                    <span>총환전 : </span>
                    <span>{{ number_format($user->total_debit) }}</span>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header credit-header flex justify-between"  >
                <div class="flex-1">수동 금액 조정</div>
                <span class="card-collapse-btn">
                    <i class="fa fa-minus" aria-hidden="true"></i>
                </span>
            </div>
            <div id="manual-cash">
                <div class="card-body">
                    <form action="{{ route('manual.cash', $user) }}" method="post" id="manualCashForm">
                        @csrf
                        <div class="flex gap-2">
                            <div class="form-group mr-1">
                                <label for="">지급/회수 할 금액</label>
                                <input type="text" class="form-control form-control-sm"  name="manualAmount" value="">
                            </div>
                            <div class="form-group mr-1">
                                <label for="">조정 사유</label>
                                <input type="text" class="form-control form-control-sm"  name="manualMemo" value="">
                            </div>
                            <div class="align-self-center mt-3">
                                <span class="btn btn-sm btn-primary" onclick="manualCash('manualCashForm')">조정하기</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>    
        </div>
    </div>  
</div>
@stop            
            
            
      

@push('css')
<style>
    .card-header {
        padding: 0.5rem;
        background-color: #6c757d;
        color: #fff;
    }

    .card-body {
        padding: 0.5rem;
    }

    .table {
        font-size: 12px;
    }

    .password {
        input:invalid;
    }

   

    

</style>
@endpush


@push('js')
<script>
    // 알림창 기본값
    function updateSwalDefault(options){
        Swal.fire({
            'icon':'warning',
            'title':options.title,
            'html':options.html,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            'confirmButtonText':'확인',
            'cancelButtonText':'취소',
        }).then(result => {
            if(options.callback){
                return options.callback(result);
            }
        })
    }

    function updateUsersInfo(id){
        const options = {
            'title':'정보수정',
            'html':'유저정보를 수정하시겠습니까?',
            callback:function(result){
                if (result.value) {
                    console.log(result)
                    $('#usersInfoForm').submit()
                }
            }
        }
        updateSwalDefault(options);

    }

    function manualCash(id){
        const options = {
            'title':'수동 금액 조정',
            'html':'해당 회원의 금액을 수동으로 지급/회수 하시겠습니까?',
            callback:function(result){
                if (result.value) {
                    $('#'+id).submit()
                }
            }
        }
        updateSwalDefault(options);
    }

</script>
@endpush
