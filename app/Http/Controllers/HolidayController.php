<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\OfficeLocation;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $type = $request->input('type');
        $locationId = $request->input('office_location_id');

        $query = Holiday::with('officeLocation')
            ->whereYear('date', $year);

        if ($type) {
            $query->where('type', $type);
        }

        if ($locationId) {
            $query->where(function ($q) use ($locationId) {
                $q->whereNull('office_location_id')
                    ->orWhere('office_location_id', $locationId);
            });
        }

        $holidays = $query->orderBy('date')->get();

        // Summary counts
        $nationalCount = Holiday::whereYear('date', $year)->where('type', 'NATIONAL')->count();
        $massLeaveCount = Holiday::whereYear('date', $year)->where('type', 'MASS_LEAVE')->count();
        $companyCount = Holiday::whereYear('date', $year)->whereIn('type', ['COMPANY', 'REGIONAL'])->count();

        $locations = OfficeLocation::where('is_active', true)->orderBy('name')->get();

        return view('modules.attendance.holidays.index', compact(
            'holidays',
            'year',
            'type',
            'locationId',
            'nationalCount',
            'massLeaveCount',
            'companyCount',
            'locations'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'date' => ['required', 'date'],
            'type' => ['required', 'in:NATIONAL,MASS_LEAVE,COMPANY,REGIONAL'],
            'office_location_id' => ['nullable', 'exists:office_locations,id'],
            'reduces_annual_leave' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['reduces_annual_leave'] = $request->boolean('reduces_annual_leave');

        $holiday = Holiday::create($validated);

        return redirect()->route('holidays.index', ['year' => Carbon::parse($holiday->date)->year])
            ->with('success', "Hari libur '{$holiday->name}' ({$holiday->date->translatedFormat('d F Y')}) berhasil ditambahkan ke kalender.");
    }

    public function update(Request $request, Holiday $holiday): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'date' => ['required', 'date'],
            'type' => ['required', 'in:NATIONAL,MASS_LEAVE,COMPANY,REGIONAL'],
            'office_location_id' => ['nullable', 'exists:office_locations,id'],
            'reduces_annual_leave' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['reduces_annual_leave'] = $request->boolean('reduces_annual_leave');

        $holiday->update($validated);

        return redirect()->route('holidays.index', ['year' => Carbon::parse($holiday->date)->year])
            ->with('success', "Informasi hari libur '{$holiday->name}' berhasil diperbarui.");
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $name = $holiday->name;
        $year = Carbon::parse($holiday->date)->year;
        $holiday->delete();

        return redirect()->route('holidays.index', ['year' => $year])
            ->with('success', "Hari libur '{$name}' telah dihapus dari kalender perusahaan.");
    }

    /**
     * Otomatis memuat daftar Hari Libur Nasional & Cuti Bersama Standar Indonesia
     */
    public function seedIndonesianHolidays(Request $request): RedirectResponse
    {
        $year = (int) $request->input('year', 2026);

        $presets = [
            ['name' => 'Tahun Baru 2026 Masehi', 'date' => "{$year}-01-01", 'type' => 'NATIONAL'],
            ['name' => "Isra Mi'raj Nabi Muhammad SAW", 'date' => "{$year}-01-16", 'type' => 'NATIONAL'],
            ['name' => 'Tahun Baru Imlek 2577 Kongzili', 'date' => "{$year}-02-17", 'type' => 'NATIONAL'],
            ['name' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)', 'date' => "{$year}-03-03", 'type' => 'NATIONAL'],
            ['name' => 'Hari Raya Idul Fitri 1447 H (Hari Pertama)', 'date' => "{$year}-03-20", 'type' => 'NATIONAL'],
            ['name' => 'Hari Raya Idul Fitri 1447 H (Hari Kedua)', 'date' => "{$year}-03-21", 'type' => 'NATIONAL'],
            ['name' => 'Cuti Bersama Idul Fitri 1447 H (1)', 'date' => "{$year}-03-23", 'type' => 'MASS_LEAVE'],
            ['name' => 'Cuti Bersama Idul Fitri 1447 H (2)', 'date' => "{$year}-03-24", 'type' => 'MASS_LEAVE'],
            ['name' => 'Wafat Yesus Kristus (Jumat Agung)', 'date' => "{$year}-04-03", 'type' => 'NATIONAL'],
            ['name' => 'Hari Buruh Internasional', 'date' => "{$year}-05-01", 'type' => 'NATIONAL'],
            ['name' => 'Kenaikan Yesus Kristus', 'date' => "{$year}-05-14", 'type' => 'NATIONAL'],
            ['name' => 'Hari Raya Idul Adha 1447 Hijriah', 'date' => "{$year}-05-27", 'type' => 'NATIONAL'],
            ['name' => 'Hari Lahir Pancasila', 'date' => "{$year}-06-01", 'type' => 'NATIONAL'],
            ['name' => 'Tahun Baru Islam 1448 Hijriah', 'date' => "{$year}-06-16", 'type' => 'NATIONAL'],
            ['name' => 'HUT Kemerdekaan Republik Indonesia ke-81', 'date' => "{$year}-08-17", 'type' => 'NATIONAL'],
            ['name' => 'Maulid Nabi Muhammad SAW', 'date' => "{$year}-08-25", 'type' => 'NATIONAL'],
            ['name' => 'Hari Raya Natal', 'date' => "{$year}-12-25", 'type' => 'NATIONAL'],
            ['name' => 'Cuti Bersama Hari Raya Natal', 'date' => "{$year}-12-26", 'type' => 'MASS_LEAVE'],
        ];

        $count = 0;
        foreach ($presets as $item) {
            Holiday::updateOrCreate(
                [
                    'date' => $item['date'],
                    'office_location_id' => null,
                ],
                [
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'reduces_annual_leave' => ($item['type'] === 'MASS_LEAVE'),
                    'description' => "Penetapan Hari Libur & Cuti Bersama Nasional {$year} (SKB 3 Menteri).",
                ]
            );
            $count++;
        }

        return redirect()->route('holidays.index', ['year' => $year])
            ->with('success', "Berhasil menyinkronkan {$count} Hari Libur Nasional & Cuti Bersama resmi untuk tahun {$year}.");
    }
}
