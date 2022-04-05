@if($user->status == 0)
    <span class="badge badge-warning">대기</span>
@elseif($user->status == 1)
    <span class="badge badge-secondary">취소</span>
@elseif($user->status == 2)
    <span class="badge badge-primary">정상</span>
@elseif($user->status == 3)
    <span class="badge badge-danger">차단</span>
@elseif($user->status == 4)
    <span class="badge badge-dark">탈퇴</span>
@endif
