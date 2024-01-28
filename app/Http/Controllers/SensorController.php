<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function sensorView()
    {
        $sensors = Sensor::all();
        return view('admin.pages.iot', ['sensors' => $sensors]);
    }
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'code' => 'required',
        'longitude' => 'required',
        'latitude' => 'required',
        'radius' => 'required'
    ]);

    Sensor::create($validatedData);

    return redirect()->back()->with('success', 'Sensor added successfully');
}


    public function update(Request $request, $id)
    {
        $sensor = Sensor::findOrFail($id);
        $sensor->update($request->all());
        return redirect()->back()->with('success', 'Sensor updated successfully');
    }

    public function destroy($id)
    {
        $sensor = Sensor::findOrFail($id);
        $sensor->delete();
        return redirect()->back()->with('success', 'Sensor deleted successfully');
    }
}
