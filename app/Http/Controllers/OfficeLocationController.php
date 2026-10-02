<?php

namespace App\Http\Controllers;

use App\Models\OfficeLocation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfficeLocationController extends Controller
{
    public function index(): View
    {
        $locations = OfficeLocation::withCount('schedules')
            ->orderBy('name')
            ->get();

        return view('modules.attendance.locations.index', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:10', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $location = OfficeLocation::create($validated);

        return redirect()->route('locations.index')
            ->with('success', "Titik lokasi kantor '{$location->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, OfficeLocation $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:10', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $location->update($validated);

        return redirect()->route('locations.index')
            ->with('success', "Titik lokasi kantor '{$location->name}' berhasil diperbarui.");
    }

    public function destroy(OfficeLocation $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', "Titik lokasi kantor '{$location->name}' berhasil dihapus.");
    }
}
