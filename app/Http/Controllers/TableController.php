<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('tables.index', compact('tables'));
    }

    public function show($id)
    {
        $table = Table::findOrFail($id);
        return view('tables.show', compact('table'));
    }

    public function create()
    {
        return view('tables.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:8',
            'extra_deluxe_menu' => 'boolean', // Optional field for extra deluxe menu, defaults to false
        ]);

        // Set default values
        $validatedData['round'] = 1;
        $validatedData['last_ordered_at'] = null;
        $validatedData['extra_deluxe_menu'] = $validatedData['extra_deluxe_menu'] ?? false;

        Table::create($validatedData);

        return redirect()->route('tables.index')
            ->with('success', 'Table created successfully.');
    }

    public function edit($id)
    {
        $table = Table::findOrFail($id);
        return view('tables.edit', compact('table'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:8',
            'extra_deluxe_menu' => 'boolean', // Optional field for extra deluxe menu, defaults to false
        ]);

        // Set default values
        $validatedData['extra_deluxe_menu'] = $validatedData['extra_deluxe_menu'] ?? false;

        $table = Table::findOrFail($id);
        $table->update($validatedData);

        return redirect()->route('tables.show', $table)
            ->with('success', 'Table updated successfully.');
    }

    public function reset($id)
    {
        $table = Table::findOrFail($id);

        // Delete all people from the table
        $table->people()->delete();

        // Remove table association from all sales
        $table->sales()->update(['table_id' => null]);

        // Reset table status
        $table->update([
            'round' => 1,
            'last_ordered_at' => null,
            'extra_deluxe_menu' => false,
        ]);

        return redirect()->route('tables.show', $table)
            ->with('success', 'Tafel succesvol gereset. Alle personen en verkoop associaties zijn verwijderd.');
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Table deleted successfully.');
    }

    public function addPerson(Request $request, $tableId)
    {
        $validatedData = $request->validate([
            'age' => 'required|integer|min:0',
        ]);

        $table = Table::findOrFail($tableId);

        // Check if adding another person would exceed the table's capacity
        if ($table->people()->count() >= $table->capacity) {
            return redirect()->route('tables.show', $tableId)
                ->with('error', 'Kan geen persoon toevoegen. Tafel heeft al de maximale capaciteit van ' . $table->capacity . ' personen bereikt.');
        }

        $table->people()->create($validatedData);

        return redirect()->route('tables.show', $tableId)
            ->with('success', 'Person added to table successfully.');
    }

    public function destroyPerson($tableId, $personId)
    {
        $table = Table::findOrFail($tableId);
        $person = $table->people()->findOrFail($personId);
        $person->delete();

        return redirect()->route('tables.show', $tableId)
            ->with('success', 'Person removed from table successfully.');
    }

    public function addTablet(Request $request, $tableId)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $table = Table::findOrFail($tableId);

        // Check if table already has a tablet
        if ($table->tablet) {
            return redirect()->route('tables.show', $tableId)
                ->with('error', 'This table already has a tablet assigned.');
        }

        $tablet = $table->tablet()->create($validatedData);

        return redirect()->route('tables.show', $tableId)
            ->with('success', 'Tablet added to table successfully. Ordering URL: ' . $tablet->ordering_url);
    }

    public function destroyTablet($tableId)
    {
        $table = Table::findOrFail($tableId);
        $tablet = $table->tablet()->first();

        if ($tablet) {
            $tablet->delete();
            return redirect()->route('tables.show', $tableId)
                ->with('success', 'Tablet removed from table successfully.');
        }

        return redirect()->route('tables.show', $tableId)
            ->with('error', 'No tablet found for this table.');
    }
}
