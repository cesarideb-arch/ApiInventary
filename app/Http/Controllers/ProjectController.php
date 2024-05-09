<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // GET all suppliers
    public function index()
    {
        $project= Project::all();
        return response()->json($project);
    }

     // GET a specific project
     public function show($id)
     {
         $project = Project::find($id);
         
         if (!$project) {
             return response()->json(['message' => 'Project not found'], 404);
         }
         
         return response()->json($project);
     }


     // POST a new supplier
   
    public function store(Request $request)
{
    // Validation rules
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'reason_social' => 'required|string|max:255',
        'rfc' => 'required|string|max:13',  // Assuming RFC has a specific format/length
        'address' => 'required|string|max:255',
        'phone' => 'required|string|max:15', // Assuming phone numbers are up to 15 digits
        'email' => 'required|email|max:255',
        'client_name' => 'required|string|max:255'
    ]);

    // Create the project
    $project = Project::create($validatedData);

    // Return the newly created project and a 201 HTTP status code
    return response()->json($project, 201);
}



   // PUT or PATCH update a supplier
   public function update(Request $request, $id)
   {
       $project = Project::find($id);
       if (!$project) {
           return response()->json(['message' => 'Supplier not found'], 404);
       }

       $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'reason_social' => 'required|string|max:255',
        'rfc' => 'required|string|max:13',  // Assuming RFC has a specific format/length
        'address' => 'required|string|max:255',
        'phone' => 'required|string|max:15', // Assuming phone numbers are up to 15 digits
        'email' => 'required|email|max:255',
        'client_name' => 'required|string|max:255'
       ]);

       $project->update($request->all());
       return response()->json($project);
   }

// DELETE a supplier
public function destroy($id)
{
    $project = Project::find($id);
    if (!$project) {
        return response()->json(['message' => 'Supplier not found'], 404);
    }
    $project->delete();
    return response()->json(['message' => 'Supplier deleted successfully']);
}
}
