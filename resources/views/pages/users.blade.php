@extends($popup == 'true' ? 'adminlte::page-popup' : 'adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">회원정보</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('users')}}" class="row" method="GET">
                        <div class="flex gap-1">
                            <div class="col-auto">
                                <input name="search" type="search" value="{{$search}}" 
                                       class="form-control-sm border" id="selectbox" placeholder="회원번호/이름"> 
                            </div>
                            <div class="col-auto">
                                <input name="searchRec" type="search" value="{{$searchRec}}" 
                                       class="form-control-sm border" id="selectbox" placeholder="추천인ID/코드"> 
                            </div>
                            <div class="col-auto">
                                <select name="perPage" class="form-control-sm border" aria-label=".form-select-sm example">
                                    <option value="20" @if($perPage == 20) selected @endif>20</option>
                                    <option value="30" @if($perPage == 30) selected @endif>30</option>
                                    <option value="50" @if($perPage == 50) selected @endif>50</option>
                                    <option value="100" @if($perPage == 100) selected @endif>100</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary px-3">검색</button>
                            </div>
                        </div>
                    </form>         
                    <table class="table table-bordered table-sm mt-3">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>상태</th>
                                <th>이름</th>
                                <th>이메일</th>
                                <th>상위</th>
                                <th>하위</th>
                                <th>현재보유금액</th>
                                <th>총충전금액</th>
                                <th>총환전금액</th>
                                <th>가입일</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user )
                            <tr class="text-center">
                                <td>{{$user->id}}</td>
                                <td>@include('partials.user-status')</td>
                                <td>@include('partials.user-sidemenu', ['user' => $user])</td>
                                <td>{{$user->email}}</td>
                                <td class="text-center">
                                    @if($user->recommend)
                                    <div class="flex justify-center">
                                        @include('partials.user-sidemenu', ['user' => $user->recommend])
                                        <span>
                                            <a href="/users?popup={{ $popup }}&searchRec={{ $user->recommend->id }}">
                                                <i class="fas fa-search pl-3"></i>
                                            </a>
                                        </span>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @if($user->underUser->count() > 0)
                                    <div>
                                        <span>
                                            {{ $user->underUser->count() }}명
                                        </span>
                                        <span>
                                            <a href="/users?popup={{ $popup }}&searchRec={{ $user->id }}">
                                                <i class="fas fa-search"></i>
                                            </a>
                                        </span>
                                    </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class=text-right>{{number_format($user->balance) }}</td>
                                <td class=text-right>{{number_format($user->total_credit)}}</td>
                                <td class=text-right>{{number_format($user->total_debit)}}</td>
                                <td>{{$user->created_at->format('Y-m-d')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{$users->withQueryString()->links()}}
                    </div> 
                </div>
            </div>
        </div>
    </div>
@endsection


