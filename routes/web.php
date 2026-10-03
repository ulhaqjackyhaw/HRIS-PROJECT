<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
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
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
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
});
