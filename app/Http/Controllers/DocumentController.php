<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Employee;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $teams = Team::withCount('employees')->get();
        return view('documents.index', compact('teams'));
    }

    public function showTeam($teamId)
    {
        $team = Team::with(['employees.user'])->findOrFail($teamId);
        return view('documents.team', compact('team'));
    }

    public function showEmployee($employeeId)
    {
        $employee = Employee::with(['user', 'team', 'documents'])->findOrFail($employeeId);
        return view('documents.employee', compact('employee'));
    }

    public function store(Request $request, $employeeId)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'document_name' => 'required|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        $employee = Employee::findOrFail($employeeId);

        $file = $request->file('file');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents', $filename, 'local');

        Document::create([
            'employee_id' => $employee->id,
            'file_path' => $filePath,
            'document_name' => $request->document_name,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->route('documents.employee', $employeeId)->with('success', 'Document added successfully!');
    }

    public function update(Request $request, $documentId)
    {
        $request->validate([
            'document_name' => 'required|string|max:255',
            'expiry_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $document = Document::findOrFail($documentId);

        if ($request->hasFile('file')) {
            if (Storage::disk('local')->exists($document->file_path)) {
                Storage::disk('local')->delete($document->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $filename, 'local');
            $document->file_path = $filePath;
        }

        $document->document_name = $request->document_name;
        $document->expiry_date = $request->expiry_date;
        $document->save();

        return redirect()->route('documents.employee', $document->employee_id)->with('success', 'Document updated successfully!');
    }

    public function destroy($documentId)
    {
        $document = Document::findOrFail($documentId);
        $employeeId = $document->employee_id;

        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.employee', $employeeId)->with('success', 'Document deleted successfully!');
    }

    public function download($documentId)
    {
        $document = Document::findOrFail($documentId);

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download($document->file_path, $document->document_name);
    }
}
