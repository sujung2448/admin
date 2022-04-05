@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">MoneyLog</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('pointlog')}}" class="row" method="GET">
                        <div class="flex gap-1">
                            <div class="col-auto">
                                <input name="search" type="search" class="form-control-sm border" 
                                       id="selectbox" value="{{$search}}" placeholder="회원번호/이름"> 
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
                                <th>이름</th>
                                <th>금액</th>
                                <th>잔액</th>
                                <th>상태</th>
                                <th>처리시간</th>
                                <th>비고</th>
                                <th>자세히</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pointlog as $pointlogs)
                            <tr class="text-center">
                                <td>{{$pointlogs->id}}</td>
                                <td>{{$pointlogs->user->name}}</td>
                                <td class="text-right">{{number_format($pointlogs->amount)}}</td>
                                <td class="text-right">{{number_format($pointlogs->balance)}}</td>
                                <td>
                                    @if($pointlogs->type == 101 || $pointlogs->type == 102)
                                        <span class="text-blue">{{$pointlogs->type_text}}</span>
                                    @elseif($pointlogs->type == 201 || $pointlogs->type == 202)    
                                        <span class="text-red">{{$pointlogs->type_text}}</span>
                                    @elseif($pointlogs->type == 301)    
                                        <span class="text-black">{{$pointlogs->type_text}}</span>    
                                    @endif
                                </td>
                                <td>{{$pointlogs->updated_at}}</td>
                                <td>{{$pointlogs->slug}}</td>
                                
                                <td>
                                    @if ($pointlogs->link)
                                        <a href="javascript:openPopup('{{ $pointlogs->link }}')">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{$pointlog->withQueryString()->links()}}
                    </div>  
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- openPopup함수 : views->vendor->adminlte->page.blade에 있음. --}}