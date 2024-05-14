<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    // GET all projects
    public function index()
    {
        $projects = Project::latest()->get();
        return response()->json($projects);
    }

    // GET a single project by id
    public function show($id)
    {
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }
        return response()->json($project);
    }

    // POST a new project
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'company_name' => 'required|string|max:50',
            'rfc' => 'required|string|max:50',
            'address' => 'required|string|max:100',
            'phone_number' => 'required|string|max:50',
            'email' => 'required|string|max:50|email',
            'client_name' => 'required|string|max:100',
        ]);

        $project = Project::create($request->all());
        return response()->json($project, 201);
    }

    // PUT or PATCH update a project
    public function update(Request $request, $id)
    {
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $request->validate([
            'name' => 'string|max:100',
            'description' => 'nullable|string',
            'company_name' => 'string|max:50',
            'rfc' => 'string|max:50',
            'address' => 'string|max:100',
            'phone_number' => 'string|max:50',
            'email' => 'string|max:50|email',
            'client_name' => 'string|max:100',
        ]);

        $project->update($request->all());
        return response()->json($project);
    }

    // DELETE a project
    public function destroy($id)
    {
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }
}

