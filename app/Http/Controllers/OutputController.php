<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Output;

class OutputController extends Controller
{
    // GET all outputs
    public function index()
    {
        $outputs = Output::with(['project', 'product'])->get();
        return response()->json($outputs);
    }

    // GET a single output by id
    public function show($id)
    {
        $output = Output::with(['project', 'product'])->find($id);
        if (!$output) {
            return response()->json(['message' => 'Output not found'], 404);
        }
        return response()->json($output);
    }

    // POST a new output
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'product_id' => 'required|exists:products,id',
            'responsible' => 'required|string|max:100',
            'quantity' => 'required|integer',
            'description' => 'nullable|string|max:100',
            'date' => 'required|date_format:Y-m-d H:i:s'
        ]);

        $output = Output::create($request->all());
        return response()->json($output, 201);
    }

    // PUT or PATCH update an output
    public function update(Request $request, $id)
    {
        $output = Output::find($id);
        if (!$output) {
            return response()->json(['message' => 'Output not found'], 404);
        }

        $request->validate([
            'project_id' => 'exists:projects,id',
            'product_id' => 'exists:products,id',
            'responsible' => 'string|max:100',
            'quantity' => 'integer',
            'description' => 'nullable|string|max:100',
            'date' => 'date_format:Y-m-d H:i:s'
        ]);

        $output->update($request->all());
        return response()->json($output);
    }

    // DELETE an output
    public function destroy($id)
    {
        $output = Output::find($id);
        if (!$output) {
            return response()->json(['message' => 'Output not found'], 404);
        }
        $output->delete();
        return response()->json(['message' => 'Output deleted successfully']);
    }
}
