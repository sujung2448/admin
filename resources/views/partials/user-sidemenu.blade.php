<span class="dropdown user-sidemenu">
    <span class="dropdown-toggle cursor-pointer"  data-toggle="dropdown"
        title="{{$user->name}}" aria-hidden="true">
    </span>
    <div class="dropdown-menu">
        <div class="flex">
            <div class="sidemenu-infoLink flex-1">
                <div class="dropdown-item-text">
                    <span class="user-side-id">#{{$user->id}}</span>
                    <span class="user-side-name">{{$user->name}}</span>
                    <span>@include('partials.user-status')</span>
                </div>
                <div class="dropdown-item-text">
                    <div class="flex justify-between">
                        <span>가입:</span>
                        <span>{{ $user->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>잔액:</span>
                        <span class="text-right">{{ number_format($user->balance) }}</span>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:openPopup('/users/{{ $user->id }}')">회원정보</a>
                <a class="dropdown-item" href="javascript:openPopup('/credit?id=&search={{ $user->id }}')">충전내역</a>
                <a class="dropdown-item" href="javascript:openPopup('/debit?id=&search={{ $user->id }}')">환전내역</a>
            </div>
        </div>
    </div>
</span>



@push('css')
<style>

    .dropdown-menu {
        font-size: 12px;

    }


</style>
@endpush