<div class="card">
    <div class="card-header debit-header flex justify-between"  >
        <div class="flex-1">환전내역</div>
        <span class="card-collapse-btn">
            <i class="fa fa-minus" aria-hidden="true"></i>
        </span>
    </div>
    <div id="debitSection">
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
                    @if(count($debits) == 0)
                    <tr>
                        <td colspan="6">
                            <div class="p-3">
                                환전내역 없음
                            </div>
                        </td>
                    </tr>
                    @endif
                    @foreach ($debits as $debit)
                    <tr>
                        <td>{{$debit->created_at->format('Y-m-d H:i:s')}}</td>
                        <td>
                            @if($debit->status == 0)
                                승인대기중
                            @elseif($debit->status == 1)
                                승인완료됨
                            @else
                                승인거부
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($debit->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-300">
                    <tr>
                        <td colspan="2">
                            합계 :
                        </td>
                        <td class="text-right">
                            {{ number_format($user->total_debit) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="text-right">
                <a href="javascript:openPopup('/debit?id=&search={{ $user->name }}')">
                    + 자세히
                </a>
            </div>
        </div>
    </div>
</div>


@push('css')
<style>
    .card-collapse-btn {
    position: absolute;
    right: 15px;
    display: inline-block;
    padding: 5px 10px;
    top: 5px;
    cursor: pointer;
}
</style>
@endpush




@push('js')
<script>
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