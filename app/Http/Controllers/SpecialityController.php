<?php 

namespace App\Http\Controllers;

use App\Core\Application\Services\SpecialtyService;
use Illuminate\Http\Request;

class SpecialityController extends Controller
{
    public function __construct(protected SpecialtyService $specialtyService) {}

    public function getAllSpecialities()
    {
        $specialties = $this->specialtyService->all();
        return response()->json(['status' => 'success', 'data' => $specialties]);
    }
    // Créer une nouvelle spécialité
    public function createSpeciality(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        $specialty = $this->specialtyService->createSpecialty($data);

        return response()->json(['status' => 'success', 'data' => $specialty], 201);
    }

    // Très utile pour tes listes déroulantes liées
    public function getByDepartment($departmentId)
    {
        $specialties = $this->specialtyService->getSpecialtiesByDept($departmentId);
        return response()->json(['status' => 'success', 'data' => $specialties]);
    }

    // Mettre à jour une spécialité
    public function updateSpeciality(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        $specialty = $this->specialtyService->updateSpecialty($id, $data);

        return response()->json(['status' => 'success', 'data' => $specialty]);
    }

    // Supprimer une spécialité
    public function deleteSpeciality($id)
    {
        $this->specialtyService->deleteSpecialty($id);

        return response()->json(['status' => 'success'], 204);
    }
}