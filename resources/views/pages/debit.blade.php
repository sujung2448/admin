@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">환전정보</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('debit')}}" class="row" method="GET">
                        <div class="flex gap-1">
                            <div class="col-auto">
                                <input name="id" type="id" class="input-mini form-control-sm border" 
                                       id="selectbox1" value="{{$id}}" placeholder="#" size=5>
                            </div>
                            <div class="col-auto">
                                <input name="search" value="{{$search}}" type="search" 
                                       class="form-control-sm border" id="selectbox" placeholder="회원번호/이름"> 
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
                            @foreach ($debit as $debits )
                            <tr class="text-center">
                                <td>{{$debits->id}}</td>
                                <td>{{$debits->created_at->locale('ko_KR')->diffForHumans(['parts'=>1,]) }}</td>
                                <td>{{$debits->user->name}}</td>
                                <td class="text-right">{{number_format($debits->amount)}}</td>
                                <td class="text-right">{{number_format($debits->balance)}}</td>
                                <td>
                                    @if($debits->status == 1)
                                        <span class="badge badge-success px-2">완료</span>
                                    @elseif($debits->status == 2)    
                                        <span class="badge badge-danger px-2">취소</span>
                                    @else
                                        <span class="badge badge-primary px-2">대기</span>
                                    @endif        
                                </td>
                                <td>
                                    @if($debits->status == 0)
                                    <div class="flex justify-center"> 
                                        <form action="{{route('debit.confirm')}}" method="POST" id="confirmForm-{{$debits->id}}">
                                            @csrf
                                            <span class="btn btn-sm btn-success mr-2" onclick="swalDebitConfirm({{$debits->id}})">승인</span>
                                            <input type="hidden" name="id" value="{{$debits->id}}"> 
                                        </form>
                                        <form action="{{route('debit.cancel')}}" method="POST" id="cancelForm-{{$debits->id}}">
                                            @csrf
                                            <span class="btn btn-sm btn-danger" onclick="swalDebitCancel({{$debits->id}})">취소</span>
                                            <input type="hidden" name="id" value="{{$debits->id}}"> 
                                        </form>
                                    </div>
                                    @elseif($debits->status == 1 && $debits->updated_at > now()->subMinutes(5))
                                    <div class="flex justify-center"> 
                                        <form action="{{route('debit.restore.confirm')}}" method="POST" id="restoreConfirmForm-{{$debits->id}}">
                                            @csrf
                                            <span class="btn btn-sm btn-danger mr-2" onclick="swalDebitRestoreConfirm({{$debits->id}})">승인복원</span>
                                            <input type="hidden" name="id" value="{{$debits->id}}"> 
                                        </form>
                                    @elseif($debits->status == 2 && $debits->updated_at > now()->subMinutes(5))    
                                        <form action="{{route('debit.restore.cancel')}}" method="POST" id="restoreCancelForm-{{$debits->id}}">
                                            @csrf
                                            <span class="btn btn-sm btn-primary" onclick="swalDebitRestoreCancel({{$debits->id}})">취소복원</span>
                                            <input type="hidden" name="id" value="{{$debits->id}}"> 
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{$debit->withQueryString()->links()}}
                    </div> 
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
<script>
    function swalDebitConfirm(id){
        Swal.fire({
            title: '환전신청',
            text: "환전신청을 승인하시겠습니까?",
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

    function swalDebitCancel(id){
        Swal.fire({
            title: '환전취소',
            text: "환전신청을 취소하시겠습니까?",
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

    function swalDebitRestoreConfirm(id){
        Swal.fire({
            title: '승인복원',
            text: "환전승인을 복원하시겠습니까?",
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

    function swalDebitRestoreCancel(id){
        Swal.fire({
            title: '취소복원',
            text: "환전취소를 복원하시겠습니까?",
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
