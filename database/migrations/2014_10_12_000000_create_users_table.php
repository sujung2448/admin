<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bank')->nullable(); //은행
            $table->integer('status')->default(0); //회원상태
            // 0 대기, 1 거부, 2 정상, 3 차단, 4 탈퇴
            $table->integer('type')->default(0); // 회원타입
            // 0 일반회원, 1 총판 회원, 2 대리점 회원, 3 유령회원 
            $table->string('account')->nullable(); //계좌번호
            $table->string('account_name')->nullable(); //예금주
            $table->string('code')->nullable(); //추천코드
            $table->string('personal_code')->nullable(); //개인코드
            $table->bigInteger('recommend_id')->nullable(); //추천인 아이디
            $table->bigInteger('partner_id')->nullable();//총판아아디
            $table->bigInteger('agent_id')->nullable();//대리점 아이디
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); 
            $table->string('re_password')->nullable(); //비밀번호수정
            $table->integer('balance')->default(0); //총 잔액
            $table->integer('total_credit')->default(0); //총 충전금
            $table->integer('total_debit')->default(0); //총 환전금
            $table->text('desc')->nullable(); //특이사항
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
