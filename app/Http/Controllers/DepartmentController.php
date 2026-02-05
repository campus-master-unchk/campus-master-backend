<?php 

namespace App\Http\Controllers;

use App\Core\Application\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(protected DepartmentService $departmentService) {}

    // Récupérer tous les départements
    public function index()
    {
        $departments = $this->departmentService->getAllDepartments();
        return response()->json(['status' => 'success', 'data' => $departments]);
    }

    // Créer un nouveau département
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code',
        ]);

        $department = $this->departmentService->createDepartment($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Département créé avec succès',
            'data' => $department
        ], 201);
    }

    // Mettre à jour un département
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code,' . $id,
        ]);

        $department = $this->departmentService->updateDepartment($id, $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Département mis à jour avec succès',
            'data' => $department
        ], 200);
    }

    // Supprimer un département
    public function destroy(int $id)
    {
        $this->departmentService->deleteDepartment($id);
        return response()->json(['status' => 'success', 'message' => 'Département supprimé avec succès']);
    }
}
