<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* 관리자 라우터 */
Route::prefix('/admin')->group(function () {
    /* 유학생 관리 */
    Route::prefix('/foreigner')->group(function () {
        /** 특정 유학생 정보 조회 */
        Route::get('', 'ForeignerController@show')->name('foreigners.show');

        /** 해당 학기 미등록 유학생 정보 조회 */
        Route::get('no_work/{sect_id}', 'ForeignerController@std_for_index_no_data_by_sect')->name('foreigners.std_for_index_no_data_by_sect');

        /** 학생정보 CSV 파일 다운로드 */
        // Route::get('data/{id}', 'ForeignerController@csv')->name('foreigners.csv');

        /* 학기별 유학생 관리 */
        Route::prefix('work')->group(function () {
            /** 학기별 전체 유학생 정보 조회 */
            Route::get('{sect_id}', 'ForeignerController@index')->name('foreigners.index');

            /** 학기별 유학생 등록 */
            Route::post('', 'ForeignerController@store')->name('foreigners.store');

            /** 학기별 유학생 삭제 */
            Route::delete('{work_list_id}', 'ForeignerController@destroy')->name('foreigners.destroy');
        });

        /* 유학생 계정 관리 */
        Route::prefix('account')->group(function () {
            /** 유학생 계정 생성 (회원가입) */
            Route::post('', 'ForeignerController@registerAccount')->name('foreigners.registerAccount');

            /** 유학생 비밀번호 변경 */
            Route::patch('{std_for_id}', 'ForeignerController@updateAccount')->name('foreigners.updateAccount');

            /** 유학생 계정 삭제 */
            Route::delete('{std_for_id}', 'ForeignerController@destroyAccount')->name('foreigners.destroyAccount');

            /** 유학생 즐겨찾기 등록 & 해제 */
            Route::get('{std_for_id}', 'ForeignerController@set_std_for_favorite')->name('foreigners.set_std_for_favorite');
        });
    });

    /* 한국인학생 관리 */
    Route::prefix('/korean')->group(function () {
        /** 전체 한국인 학생 정보 조회 */
        Route::get('', 'KoreanController@index')->name('koreans.index');

        /** 특정 한국인 학생 정보 조회 */
        Route::post('', 'KoreanController@search_std_kor_data_res')->name('koreans.search_std_kor_data_res');

        /** 학년도별 학생정보 CSV 파일 다운로드 */
        // Route::get('data/{id}', 'KoreanController@csv')->name('koreans.csv');

        /* 한국인학생 계정 관리 */
        Route::prefix('account')->group(function () {
            /** 계정 등록 - 대기 명단 조회 */
            Route::get('', 'KoreanController@indexApproval')->name('koreans.indexApproval');

            /** 계정 등록 - 승인 */
            Route::patch('', 'KoreanController@updateApproval')->name('koreans.updateApproval');

            /** 계정 삭제 */
            Route::delete('{std_kor_id}', 'KoreanController@destroyAccount')->name('koreans.destroyAccount');
        });


        /* 이용제한 한국인 학생 관리 */
        Route::prefix("restrict")->group(function () {
            /** 이용제한 학생 조회 */
            Route::get('', "RestrictKoreanController@index")->name("koreans.indexRestrict");

            /** 이용제한 등록 */
            Route::post('', "RestrictKoreanController@register")->name("koreans.registerRestrict");

            /** 이용제한 해제 */
            Route::patch('{restrict_id}', "RestrictKoreanController@update")->name("koreans.updateRestrict");
        });
    });

    /* 학기 관리 라우터 */
    Route::prefix('/section')->group(function () {
        /** 년도별 등록된 전체 학기 목록 조회 */
        Route::get('', 'SectionController@index')->name('sections.index');

        /** 학기 등록 */
        Route::post('', 'SectionController@store')->name('sections.store');

        /** 학기 수정 */
        Route::patch('{sect_id}', 'SectionController@update')->name('sections.update');

        /** 학기 삭제 */
        Route::delete('{sect_id}', 'SectionController@destroy')->name('sections.destroy');
    });

    /* 스케줄 관리 라우터 */
    Route::prefix('/schedule')->group(function () {
        /* 특정 날짜 전체 유학생 스케줄 조회 */
        Route::get('', 'ScheduleController@showForeignerSchedules')->name('schedules.showForeignerSchedules');

        /* 스케줄 등록 */
        Route::post('', 'ScheduleController@store')->name('schedules.store');

        /* 해당 학기 해당 유학생 전체 스케줄 삭제 */
        Route::delete('', 'ScheduleController@destroy_all_schedule')->name('schedules.destroy_all_schedule');

        /* 특정 스케줄 업데이트 */
        Route::patch('some/{sch_id}', 'ScheduleController@update')->name('schedules.update');

        /* 특정 스케줄 삭제 */
        Route::delete('some/{sch_id}', 'ScheduleController@destroy')->name('schedules.destroy');

        /* 해당 스케줄 학생 추가 */
        Route::post('add/{sch_id}', 'ReservationController@add_kor_schedule_by_admin')->name('reservations.add_kor_schedule_by_admin');

        /* 스케줄 관련 미입력 관리 라우터 */
        /** 해당 날짜 출석 결과 미입력건 조회 */
        Route::get('uninputed/{date}', 'ScheduleController@indexUninputedList')->name('schedules.indexUninputedList');

        /** 해당 날짜 출석 결과 미승인건 조회 */
        Route::get('unapproved/{date}', 'ScheduleController@indexUnapprovedList')->name('schedules.indexUnapprovedList');

        /** 출석 결과 미승인 건 승인 */
        Route::patch('approval/{sch_id}', 'ScheduleController@updateApprovalOfUnapprovedCase')->name('schedules.updateApprovalOfUnapprovedCase');
    });

    /* 계열 / 학과 관리 라우터 */
    Route::prefix('/department')->group(function () {
        /** 계열 & 학과 등록 */
        Route::post('', 'DepartmentController@store')->name('departments.store');

        /** 계열 & 학과 이름 변경 */
        Route::patch('{dept_id}', 'DepartmentController@update')->name('departments.update');

        /** 계열 & 학과 삭제 */
        Route::delete('{dept_id}', 'DepartmentController@destroy')->name('departments.destroy');
    });

    /* 환경변수 관리 라우터 */
    Route::prefix('/setting')->group(function () {
        /** 등록된 환경변수 조회 */
        Route::get('', 'SettingController@index')->name('settings.index');

        /** 환경변수 저장 */
        Route::post('', 'SettingController@store')->name('settings.store');
    });
});

// 관리자, 유학생 미들웨어 적용
Route::middleware('auth.multi')->group(static function () {
    Route::post('logout', 'LoginController@logout')->name('auth.logout');

    Route::prefix('request')->group(static function () {
        Route::get('admin', 'LoginController@request_user_data')->name('auth.adminsRequest');
        Route::get('foreigner', 'LoginController@request_user_data')->name('auth.foreignersRequest');
    });

    /* 유학생 라우터 */
    Route::prefix('foreigner')->group(function () {
        /* 유학생 - 특정 기간 개인 스케줄 조회 */
        Route::get('schedule', 'ScheduleController@std_for_show_sch_by_date')->name('schedules.show');

        /** 유학생 비밀번호 변경 */
        Route::patch('/password', 'ForeignerController@updateAccount')->name('foreigners.updateAccount');

        /* 예약 관련 */
        Route::prefix('reservation')->group(function () {
            /** 해당 스케줄 신청 학생 명단 조회 */
            Route::get('{sch_id}', 'ReservationController@std_for_show_res_by_id')->name('reservations.showReservation');

            /** 해당 스케줄 신청 학생 명단 승인 */
            Route::patch('permission/{sch_id}', 'ReservationController@std_for_update_res_permission')->name('reservations.updateReservaion');

            /** 해당 스케줄 출석 결과 입력 */
            Route::post('result/{sch_id}', 'ReservationController@std_for_input_sch_result')->name('reservations.inputResult');
        });
    });
});

// 한국인 학생 미들웨어 적용
Route::middleware('auth.korean')->group(function () {
    /* 한국인학생 라우터 */
    Route::prefix('/korean')->group(function () {
        /** 한국인 학생 계정 생성 (회원가입) */
        Route::post('account', 'KoreanController@registerAccount')->name('koreans.registerAccount');

        /* 한국인학생 - 현재 날짜 기준 스케줄 조회 */
        Route::get('schedule', 'ScheduleController@index')->name('schedules.index');

        // ID 값으로 스케줄 정보 조회
        Route::get('schedule/{sch_id}', 'ScheduleController@std_kor_show_sch')->name('schedules.show_schedule_by_id');

        /** 참석한 학기 목록 조회 */
        Route::get('section', 'SectionController@std_kor_attendanced_index')->name('section.std_kor_attendanced_index');

        /* 예약 관련 */
        Route::prefix('reservation')->group(function () {
            /** 해당 일자에 대한 예약 조회 */
            Route::get('', 'ReservationController@std_kor_show_res_by_date')->name('reservations.show');

            /** 예약 신청 */
            Route::post('{sch_id}', 'ReservationController@std_kor_store_res')->name('reservations.store');

            /** 내 예약 일정 삭제 */
            Route::delete('{res_id}', 'ReservationController@std_kor_destroy_res')->name('reservations.destroy');

            /** 학기별 미팅 목록 결과 조회 */
            Route::get('/result', 'ReservationController@std_kor_show_res_by_sect')->name('reservations.show');
        });
    });
});

// 공용 라우터
/** 로그인 */
Route::prefix('login')->group(static function () {
    Route::post('admin', 'LoginController@login_admin')->name('login.adminsLogin');
    Route::post('foreigner', 'LoginController@login_std_for')->name('login.foreignersLogin');
});

/** 관리자 비밀번호 초기화 */
Route::prefix('reset')->group(function () {
    Route::post('/', 'MailController@request_reset');
    Route::get('/', 'MailController@run_reset');
});

/** 등록된 계열 & 학과 목록 조회 */
Route::get('department', 'DepartmentController@index')->name('departments.index');
