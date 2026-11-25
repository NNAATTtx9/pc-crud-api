<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BoothStaff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class BoothStaffViewController extends Controller
{
    private const BOOTH_TYPES = ['game', 'food', 'merchandise'];
    public function index() {
        $items = BoothStaff::orderBy('id')->get();
        return view('booth-staff.index', compact('items'));
    }

    public function create(){
        return view('booth-staff.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:64', 'unique:booth_staff,username'],
            'password' => ['required', 'string', 'min:8'],
            'booth_type' => ['required', Rule::in(self::BOOTH_TYPES)],
        ]);
        BoothStaff::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'booth_type' => $data['booth_type'],
            'is_active' => $request->boolean('is_active'),
        ]);
        return redirect('/admin/booth-staff')->with('success', 'Booth staff added');
    }

    public function edit($id) {
        $item = BoothStaff::findOrFail($id);
        return view('booth-staff.edit', compact('item'));
    }

    public function update(Request $request, $id) {
        $item = BoothStaff::findOrFail($id);
        $data = $request -> validate([
            'password' => ['nullable', 'string', 'min:8'],
            'booth_type' => ['required', Rule::in(self::BOOTH_TYPES)],
        ]);
        $item->booth_type = $data['booth_type'];
        $item->is_active = $request->boolean('is_active');
        if (! empty($data['password'])) {
            $item->password = Hash::make($data['password']);
        }
        $item->save();
        return redirect('/admin/booth-staff')->with('success', 'Booth staff updated');
    }

    public function destroy($id)
    {
        $item = BoothStaff::findOrFail($id);
        $item->delete();
        return redirect('/admin/booth-staff')->with('success', 'Booth staff deleted.');
    }
}
