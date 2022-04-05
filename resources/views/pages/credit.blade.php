@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">충전정보</h1>
@stop
@push('css')
    <style>
        .input-mini{
            width:60px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('credit')}}" class="row" method="GET">
                        <div class="flex gap-1">
                            <div class="col-auto">
                                <input name="id" type="id" class="input-mini form-control-sm border" 
                                       id="selectbox1" value="{{$id}}" placeholder="#"> 
                            </div>
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
                                <th>신청시간</th>
                                <th>이름</th>
                                <th>금액</th>
                                <th>잔액</th>
                                <th>상태</th>
                                <th>비고</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($credit as $credits )
                            <tr class="text-center">
                                <td>{{$credits->id}}</td>
                                <td>{{$credits->created_at->locale('ko_KR')->diffForHumans(['parts'=>1,]) }}</td>
                                <td>{{$credits->user->name}}</td>
                                <td class="text-right">{{number_format($credits->amount)}}</td>
                                <td class="text-right">{{number_format($credits->balance)}}</td>
                                <td>
                                    @if($credits->status == 1)
                                        <span class="badge badge-success px-2">완료</span>
                                    @elseif($credits->status == 2)    
                                        <span class="badge badge-danger px-2">취소</span>
                                    @else
                                        <span class="badge badge-primary px-2">대기</span>
                                    @endif        
                                </td>
                                <td>
                                    @if($credits->status == 0)
                                    <div class="flex justify-center"> 
                                        <form action="{{route('credit.confirm')}}" method="POST" id="confirmForm-{{$credits->id}}">
                                            @csrf
                                            <span class="btn btn-sm btn-success mr-2" onclick="swalCreditConfirm({{$credits->id}})">승인</span>
                                            <input type="hidden" name="id" value="{{$credits->id}}"> 
                                        </form>
                                        <form action="{{route('credit.cancel')}}" method="POST" id="cancelForm-{{$credits->id}}">
                                            @csrf
                                            <span class="btn btn-sm btn-danger" onclick="swalCreditCancel({{$credits->id}})">취소</span>
                                            <input type="hidden" name="id" value="{{$credits->id}}"> 
                                        </form>
                                    </div>
                                    @elseif($credits->status == 1 && $credits->updated_at > now()->subMinutes(5))
                                    <div class="flex justify-center"> 
                                        <form action="{{route('credit.restore.confirm')}}" method="POST" id="restoreConfirmForm-{{$credits->id}}">
                                            @csrf
                                            <span class="btn btn-sm btn-danger mr-2" onclick="swalCreditRestoreConfirm({{$credits->id}})">승인복원</span>
                                            <input type="hidden" name="id" value="{{$credits->id}}"> 
                                        </form>
                                    @elseif($credits->status == 2 && $credits->updated_at > now()->subMinutes(5))    
                                        <form action="{{route('credit.restore.cancel')}}" method="POST" id="restoreCancelForm-{{$credits->id}}">
                                            @csrf
                                            <span class="btn btn-sm btn-primary" onclick="swalCreditRestoreCancel({{$credits->id}})">취소복원</span>
                                            <input type="hidden" name="id" value="{{$credits->id}}"> 
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{$credit->withQueryString()->links()}}
                    </div> 
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
<script>
    function swalCreditConfirm(id){
        Swal.fire({
            title: '충전신청',
            text: "충전신청을 승인하시겠습니까?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(result => {
            // console.log(id)
            if (result.value) {
                $(`#confirmForm-${id}`).submit();
                
                }
            })
    }

    function swalCreditCancel(id){
        Swal.fire({
            title: '충전취소',
            text: "충전신청을 취소하시겠습니까?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(result => {
            // console.log(id)
            if (result.value) {
                $(`#cancelForm-${id}`).submit();
                
                }
            })
    }

    function swalCreditRestoreConfirm(id){
        Swal.fire({
            title: '승인복원',
            text: "충전승인을 복원하시겠습니까?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(result => {
            // console.log(id)
            if (result.value) {
                $(`#restoreConfirmForm-${id}`).submit();
                
                }
            })
    }

    function swalCreditRestoreCancel(id){
        Swal.fire({
            title: '취소복원',
            text: "충전취소를 복원하시겠습니까?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '확인',
            cancelButtonText: '취소',
        }).then(result => {
            // console.log(id)
            if (result.value) {
                $(`#restoreCancelForm-${id}`).submit();
                
                }
            })
    }
</script>
@endsection