<div class="card">
    <div class="card-header credit-header flex justify-between"  >
        <div class="flex-1">충전내역</div>
        <span class="card-collapse-btn">
            <i class="fa fa-minus" aria-hidden="true"></i>
        </span>
    </div>
    <div id="creditSection">
        <div class="card-body">
            <table class="table table-sm table-bordered text-center">
                <thead>
                    <tr>
                        <th>신청시간</th>
                        <th>상태</th>
                        <th>금액</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($credits) == 0)
                    <tr>
                        <td colspan="6">
                            <div class="p-3">
                                충전내역 없음
                            </div>
                        </td>
                    </tr>
                    @endif
                    @foreach ($credits as $credit)
                    <tr onclick="showCreditDetail({{ $credit->id }})">
                        <td>{{$credit->created_at->format('Y-m-d H:i:s')}}</td>
                        <td>
                            @if($credit->status == 0)
                                승인대기중
                            @elseif($credit->status == 1)
                                승인완료됨
                            @else
                                승인거부
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($credit->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-300">
                    <tr>
                        <td colspan="2">
                            합계 :
                        </td>
                        <td class="text-right">
                            {{ number_format($user->total_credit) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="text-right">
                <a href="javascript:openPopup('/credit?id=&search={{ $user->name }}')">
                    + 자세히
                </a>
            </div>
        </div>
    </div>
</div>


@push('js')
<script>
    // 카드 보이기,숨기기 기능
    const CardCollapseBtns =  document.querySelectorAll('.card-collapse-btn');
    CardCollapseBtns.forEach(item => {
        item.addEventListener('click',ToggleCollapseCard)
    });

    function ToggleCollapseCard(){
        $(this).find('.fa').toggleClass('fa-plus')
        $(this).parents('.card').find('.card-body').toggle() // find('.card-body,.card-footer').toggle()
    }
    
</script>
@endpush