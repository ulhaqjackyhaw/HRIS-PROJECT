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
use App\Http\Controllers\EmployeePortalController;
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

// ============================================================================
// 1. PUBLIC CANDIDATE & CAREER PORTAL ROUTES
// ============================================================================
Route::prefix('career')->name('career.')->group(function () {
    Route::get('/', [CareerController::class, 'index'])->name('landing');
    Route::get('/jobs', [CareerController::class, 'index'])->name('jobs');
    Route::get('/jobs/{slug}', [CareerController::class, 'show'])->name('jobs.show');

    // Candidate Auth Routes (Portal Pelamar Khusus)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [CareerController::class, 'loginForm'])->name('login');
        Route::post('/login', [CareerController::class, 'login'])->name('login.submit');
        Route::get('/register', [CareerController::class, 'registerForm'])->name('register');
        Route::post('/register', [CareerController::class, 'register'])->name('register.submit');
    });

    // Candidate Authenticated Actions (Dilindungi Role: Candidate)
    Route::middleware(['auth', 'role:candidate'])->group(function () {
        Route::get('/dashboard', [CareerController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [CareerController::class, 'profile'])->name('profile');
        Route::post('/profile', [CareerController::class, 'updateProfile'])->name('profile.update');
        Route::post('/jobs/{slug}/apply', [CareerController::class, 'apply'])->name('jobs.apply');
    });

    // Candidate Psychotests & Logout (Auth Protected & Application Ownership Checked)
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [CareerController::class, 'logout'])->name('logout');
        Route::get('/applications/{application}/psychotests', [CareerController::class, 'psychotestsIndex'])->name('psychotests.index');
        Route::get('/applications/{application}/psychotests/{psychotest}', [CareerController::class, 'showPsychotest'])->name('psychotests.show');
        Route::post('/applications/{application}/psychotests/{psychotest}/submit', [CareerController::class, 'submitPsychotest'])->name('psychotests.submit');
    });
});

// ============================================================================
// 2. GUEST AUTHENTICATION ROUTES (TERPISAH PER ROLE)
// ============================================================================
Route::middleware('guest')->group(function () {
    // A. HR Administrator Login
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store'])->name('login.submit');
    Route::get('hr/login', [AuthController::class, 'create'])->name('hr.login');
    Route::post('hr/login', [AuthController::class, 'store'])->name('hr.login.submit');

    // B. Employee Self-Service (ESS) Login
    Route::get('employee/login', [AuthController::class, 'createEmployee'])->name('employee.login');
    Route::post('employee/login', [AuthController::class, 'storeEmployee'])->name('employee.login.submit');
});

// ============================================================================
// 3. COMMON AUTHENTICATED ACTIONS
// ============================================================================
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
});

// ============================================================================
// ============================================================================
// 4. EMPLOYEE SELF-SERVICE (ESS) WORKSPACE (ROLE: EMPLOYEE & INTERNAL HR)
// ============================================================================
Route::middleware(['auth', 'role:employee'])->group(function () {
    // Dedicated Employee Portal (Dedicated views & layout in resources/views/employee/)
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [EmployeePortalController::class, 'dashboard'])->name('dashboard');

        // Attendance
        Route::get('/attendance/check-in', [EmployeePortalController::class, 'attendanceCheckIn'])->name('attendance.check-in');
        Route::post('/attendance/clock-in', [EmployeePortalController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('/attendance/clock-out', [EmployeePortalController::class, 'clockOut'])->name('attendance.clock-out');
        Route::get('/attendance/history', [EmployeePortalController::class, 'attendanceHistory'])->name('attendance.history');

        // Leaves
        Route::get('/leaves', [EmployeePortalController::class, 'leaves'])->name('leaves.index');
        Route::post('/leaves', [EmployeePortalController::class, 'storeLeave'])->name('leaves.store');

        // Overtimes
        Route::get('/overtimes', [EmployeePortalController::class, 'overtimes'])->name('overtimes.index');
        Route::post('/overtimes', [EmployeePortalController::class, 'storeOvertime'])->name('overtimes.store');

        // Schedules
        Route::get('/schedules', [EmployeePortalController::class, 'schedules'])->name('schedules.index');

        // Profile
        Route::get('/profile', [EmployeePortalController::class, 'profile'])->name('profile');
    });

    // Self-Service Attendance Check-In (Webcam Selfie & Geolocation)
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'checkInForm'])->name('attendance.check-in');
        Route::get('/check-in', [AttendanceController::class, 'checkInForm'])->name('attendance.check-in-alt');
        Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

        // Monitoring & Daily Attendance Log
        Route::get('/logs', [AttendanceController::class, 'index'])->name('attendance.index');

        // Personal Leave & Time-Off Management
        Route::get('leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::post('leaves', [LeaveRequestController::class, 'store'])->name('leaves.store');
        Route::get('/time-off', fn () => redirect()->route('leaves.index'))->name('timeoff.index');

        // Manager Self-Service (MSS) Approvals (Jika user membawahi bawahan)
        Route::get('approvals', [ManagerApprovalController::class, 'index'])->name('approvals.index');
        Route::post('approvals/leaves/{leaveRequest}/approve', [ManagerApprovalController::class, 'approveLeave'])->name('approvals.leave.approve');
        Route::post('approvals/leaves/{leaveRequest}/reject', [ManagerApprovalController::class, 'rejectLeave'])->name('approvals.leave.reject');

        // Employee Schedules / Roster View
        Route::get('schedules', [EmployeeScheduleController::class, 'index'])->name('schedules.index');

        // Overtime Requests
        Route::get('overtimes', [OvertimeRequestController::class, 'index'])->name('overtimes.index');
        Route::post('overtimes', [OvertimeRequestController::class, 'store'])->name('overtimes.store');
    });
});

// ============================================================================
// 5. HR ADMINISTRATOR WORKSPACE (ROLE: HR ADMINISTRATOR)
// ============================================================================
Route::middleware(['auth', 'role:hr'])->group(function () {
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

    // HR Controls for Attendance & Operations
    Route::prefix('attendance')->group(function () {
        // Monthly Attendance Summary (HR & Payroll Engine Prep)
        Route::get('/summary', [AttendanceController::class, 'summary'])->name('attendance.summary');

        // HR Direct Leave Approvals
        Route::post('leaves/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');

        // Shifts Management (Master Settings)
        Route::resource('shifts', ShiftController::class)->except(['create', 'show', 'edit']);

        // Office Geofence Locations (Master Settings)
        Route::resource('locations', OfficeLocationController::class)->except(['create', 'show', 'edit']);

        // Employee Schedules / Roster Assign
        Route::post('schedules', [EmployeeScheduleController::class, 'store'])->name('schedules.store');
        Route::delete('schedules/{schedule}', [EmployeeScheduleController::class, 'destroy'])->name('schedules.destroy');

        // Overtime Approvals
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

        // Psychotest Monitoring & Retake Management
        Route::get('/psychotests', [RecruitmentController::class, 'psychotests'])->name('psychotests.index');
        Route::post('/applications/{application}/psychotests/{psychotest}/allow-retake', [RecruitmentController::class, 'allowPsychotestRetake'])->name('applications.psychotests.allow-retake');
        Route::post('/applications/{application}/psychotests/{psychotest}/cancel-retake', [RecruitmentController::class, 'cancelPsychotestRetake'])->name('applications.psychotests.cancel-retake');
    });
});
