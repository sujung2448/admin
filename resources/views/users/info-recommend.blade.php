<div class="card">
    <div class="card-header recommend-header flex justify-between"  >
        <div class="flex-1">추천내역</div>
        <span class="card-collapse-btn">
            <i class="fa fa-minus" aria-hidden="true"></i>
        </span>
    </div>
    <div id="recommendSection" class="card-body  bg-gray-50" >
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <div>최상위 추천인에서 {{ $user->name }} 까지</div>
                </div>
                <div class="flex flex-wrap">
                    @foreach ($recTrees as $tree)
                        <span class="px-2">
                            @include('partials.user-sidemenu', ['user' => $tree])
                            @if(!$loop->last)
                            <i class="fa-solid fa-arrow-right text-orange-400" aria-hidden="true"></i>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
        <form action="{{ route('user.recommend.update', ['user'=> $user]) }}" method="post" id="updateUserRecommendForm">
            @csrf
            <div class="card">
                <div class="pt-2 pl-2">
                    <span>현재 상위 추천인 : </span>
                    <span class="text-bold">
                        @if($user->recommend)
                            @include('partials.user-sidemenu', ['user' => $user->recommend])
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <div class="form-group mr-1">
                        <label for="">변경할 추천인 코드</label>
                        <span class="flex">
                            <input type="text" class="form-control form-control-sm col-sm-5" id="recommendCode"
                                name="recommendCode" autocomplete="off">
                            @error('recommendCode')
                                <b class="text-danger">{{ $message }}</b>
                            @enderror
                            <div class="btn btn-sm btn-primary ml-2" onclick="updateUserRecommend()">추천인변경</div>
                        </span>
                    </div>
                </div>
            </div>
        </form>
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    {{ $user->name }}의 하위 추천인 {{ count($underUsers) }}명
                </div>
                <table class="table table-sm table-bordered text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>이름</th>    
                            <th>하위추천수</th>
                            <th>충전총액</th>
                            <th>환전총액</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($underUsers) == 0)
                            <tr>
                                <td colspan="5">
                                    <div class="p-3">
                                        추천인 내역없음
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @foreach ($underUsers as $under)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span>
                                    @include('partials.user-sidemenu', ['user' => $under])
                                </span>
                            </td>
                            <td>
                                {{ count($under->underUser) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($under->confirmedCredit->sum('amount')) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($under->confirmedDebit->sum('amount')) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-300">
                        <tr>
                            <td colspan="2" class="text-center">합계</td>
                            <td>
                                {{ $underTotalRecommend }}
                            </td>
                            <td class="text-right">
                                {{ number_format($underTotalCredit) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($underTotalDebit) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


@push('js')
<script>
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
            reverseButtons: true,
        }).then(result => {
            if(options.callback){
                return options.callback(result);
            }
        })
    }

    function updateUserRecommend(){
        const recommend = $('#recommendCode').val()
        if(recommend === ''){
            toastr.error('코드입력값을 확인해주세요.')
            $('#recommendCode').focus()
            return
        }

        const options = {
            'title':'회원 상위추천인',
            'html':`해당 회원의 상위추천인을 강제로 변경합니다.`,
            callback:function(result){
                if (result.value) {
                    $('#updateUserRecommendForm').submit()
                }
            }
        }
        updateSwalDefault(options);
    }
</script>
@endpush