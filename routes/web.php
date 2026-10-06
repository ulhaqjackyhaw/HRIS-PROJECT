<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeCareerHistoryController;
use App\Http\Controllers\EmployeeContractController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeEducationController;
use App\Http\Controllers\EmployeeScheduleController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ManagerApprovalController;
use App\Http\Controllers\ModulePortalController;
use App\Http\Controllers\OfficeLocationController;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

// Public Candidate Portal & Career Routes
Route::prefix('career')->name('career.')->group(function () {
    Route::get('/', [CareerController::class, 'index'])->name('landing');
    Route::get('/jobs', [CareerController::class, 'index'])->name('jobs');
    Route::get('/jobs/{slug}', [CareerController::class, 'show'])->name('jobs.show');

    // Candidate Auth Routes
    Route::get('/login', [CareerController::class, 'loginForm'])->name('login');
    Route::post('/login', [CareerController::class, 'login'])->name('login.submit');
    Route::get('/register', [CareerController::class, 'registerForm'])->name('register');
    Route::post('/register', [CareerController::class, 'register'])->name('register.submit');

    // Candidate Authenticated Actions
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [CareerController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [CareerController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [CareerController::class, 'profile'])->name('profile');
        Route::post('/profile', [CareerController::class, 'updateProfile'])->name('profile.update');
        Route::post('/jobs/{slug}/apply', [CareerController::class, 'apply'])->name('jobs.apply');

        // Candidate Psychotests
        Route::get('/applications/{application}/psychotests', [CareerController::class, 'psychotestsIndex'])->name('psychotests.index');
        Route::get('/applications/{application}/psychotests/{psychotest}', [CareerController::class, 'showPsychotest'])->name('psychotests.show');
        Route::post('/applications/{application}/psychotests/{psychotest}/submit', [CareerController::class, 'submitPsychotest'])->name('psychotests.submit');
    });
});

// Guest Authentication Routes (Internal HR)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store']);
});

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

    // Enterprise App Launcher / Module Hub
    Route::get('/', [ModulePortalController::class, 'index'])->name('portal');
    Route::get('/portal', [ModulePortalController::class, 'index'])->name('portal.index');
    Route::get('/modules/{module}', [ModulePortalController::class, 'show'])->name('modules.show');

    // Core HR Domain Workspace
    Route::prefix('core-hr')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('departments', DepartmentController::class);
        Route::resource('positions', PositionController::class);
        Route::resource('employees', EmployeeController::class);

        Route::post('employees/{employee}/contracts', [EmployeeContractController::class, 'store'])
            ->name('employees.contracts.store');
        Route::delete('employees/{employee}/contracts/{contract}', [EmployeeContractController::class, 'destroy'])
            ->name('employees.contracts.destroy');

        Route::post('employees/{employee}/educations', [EmployeeEducationController::class, 'store'])
            ->name('employees.educations.store');
        Route::delete('employees/{employee}/educations/{education}', [EmployeeEducationController::class, 'destroy'])
            ->name('employees.educations.destroy');

        Route::post('employees/{employee}/careers', [EmployeeCareerHistoryController::class, 'store'])
            ->name('employees.careers.store');
        Route::delete('employees/{employee}/careers/{career}', [EmployeeCareerHistoryController::class, 'destroy'])
            ->name('employees.careers.destroy');
    });

    // Attendance Domain Workspace
    Route::prefix('attendance')->group(function () {
        // Self-Service Check-In (Webcam Selfie & Geolocation)
        Route::get('/', [AttendanceController::class, 'checkInForm'])->name('attendance.check-in');
        Route::get('/check-in', [AttendanceController::class, 'checkInForm'])->name('attendance.check-in-alt');
        Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

        // Monitoring & Daily Attendance Log
        Route::get('/logs', [AttendanceController::class, 'index'])->name('attendance.index');

        // Monthly Attendance Summary (HR & Payroll Engine Prep)
        Route::get('/summary', [AttendanceController::class, 'summary'])->name('attendance.summary');

        // Leave & Time-Off Management
        Route::get('leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::post('leaves', [LeaveRequestController::class, 'store'])->name('leaves.store');
        Route::post('leaves/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');
        Route::get('/time-off', fn () => redirect()->route('leaves.index'))->name('timeoff.index');

        // Manager Self-Service (MSS) Approvals
        Route::get('approvals', [ManagerApprovalController::class, 'index'])->name('approvals.index');
        Route::post('approvals/leaves/{leaveRequest}/approve', [ManagerApprovalController::class, 'approveLeave'])->name('approvals.leave.approve');
        Route::post('approvals/leaves/{leaveRequest}/reject', [ManagerApprovalController::class, 'rejectLeave'])->name('approvals.leave.reject');

        // Shifts Management
        Route::resource('shifts', ShiftController::class)->except(['create', 'show', 'edit']);

        // Office Geofence Locations
        Route::resource('locations', OfficeLocationController::class)->except(['create', 'show', 'edit']);

        // Employee Schedules / Roster
        Route::get('schedules', [EmployeeScheduleController::class, 'index'])->name('schedules.index');
        Route::post('schedules', [EmployeeScheduleController::class, 'store'])->name('schedules.store');
        Route::delete('schedules/{schedule}', [EmployeeScheduleController::class, 'destroy'])->name('schedules.destroy');

        // Overtime Requests & Approval
        Route::get('overtimes', [OvertimeRequestController::class, 'index'])->name('overtimes.index');
        Route::post('overtimes', [OvertimeRequestController::class, 'store'])->name('overtimes.store');
        Route::post('overtimes/{overtime}/approve', [OvertimeRequestController::class, 'approve'])->name('overtimes.approve');
        Route::post('overtimes/{overtime}/reject', [OvertimeRequestController::class, 'reject'])->name('overtimes.reject');
    });

    // Recruitment & ATS Workspace (HR Administration Panel)
    Route::prefix('recruitment')->name('recruitment.')->group(function () {
        Route::get('/', [RecruitmentController::class, 'dashboard'])->name('dashboard');

        // Job Postings Management
        Route::get('/jobs', [RecruitmentController::class, 'jobs'])->name('jobs.index');
        Route::get('/jobs/create', [RecruitmentController::class, 'createJob'])->name('jobs.create');
        Route::post('/jobs', [RecruitmentController::class, 'storeJob'])->name('jobs.store');
        Route::get('/jobs/{job}/edit', [RecruitmentController::class, 'editJob'])->name('jobs.edit');
        Route::put('/jobs/{job}', [RecruitmentController::class, 'updateJob'])->name('jobs.update');
        Route::patch('/jobs/{job}/toggle-status', [RecruitmentController::class, 'toggleJobStatus'])->name('jobs.toggle-status');
        Route::delete('/jobs/{job}', [RecruitmentController::class, 'destroyJob'])->name('jobs.destroy');

        // ATS Pipeline & Candidate Applications
        Route::get('/applications', [RecruitmentController::class, 'applications'])->name('applications.index');
        Route::get('/applications/{application}', [RecruitmentController::class, 'showApplication'])->name('applications.show');
        Route::patch('/applications/{application}/stage', [RecruitmentController::class, 'updateStage'])->name('applications.stage');
        Route::post('/applications/{application}/interview', [RecruitmentController::class, 'scheduleInterview'])->name('applications.interview');
        Route::post('/applications/{application}/communicate', [RecruitmentController::class, 'sendCommunication'])->name('applications.communicate');
        Route::post('/applications/{application}/convert-employee', [RecruitmentController::class, 'convertToEmployee'])->name('applications.convert-employee');

        // Psychotest Monitoring
        Route::get('/psychotests', [RecruitmentController::class, 'psychotests'])->name('psychotests.index');
    });
});
