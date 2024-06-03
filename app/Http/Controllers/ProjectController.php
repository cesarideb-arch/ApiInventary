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

    public function SearchProject(Request $request) {
        // Obtener el parámetro de búsqueda desde la solicitud
        $search = $request->input('search');
    
        // Si el parámetro de búsqueda está presente, filtrar los proyectos
        if ($search) {
            $projects = Project::where('name', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%")
                                ->orWhere('company_name', 'like', "%{$search}%")
                                ->orWhere('rfc', 'like', "%{$search}%")
                                ->orWhere('address', 'like', "%{$search}%")
                                ->orWhere('phone_number', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('client_name', 'like', "%{$search}%")
                                ->get();
        } else {
            // Si no hay parámetro de búsqueda, obtener todos los proyectos
            $projects = Project::all();
        }
    
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
            'rfc' => 'nullable|string',
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
            'rfc' => 'nullable|string',
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

    // Verificar si el proyecto está relacionado con registros en las tablas entrances o outputs
    if ($project->entrances()->exists() || $project->outputs()->exists()) {
        return response()->json(['message' => 'El proyecto está relacionado con entradas o salidas y no puede ser eliminado'], 400);
    }

    $project->delete();
    return response()->json(['message' => 'Project deleted successfully']);
}
}

