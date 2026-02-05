<?php

namespace App\Http\Controllers;

use App\Core\Application\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function __construct(protected ModuleService $moduleService) {}

    public function getAllModules()
    {
        $modules = $this->moduleService->getAllModules();

        return response()->json(['status' => 'success', 'data' => $modules], 201);
    }

    public function createModule(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'specialty_id'  => 'required|exists:specialties,id',
            'level_id'      => 'required|exists:levels,id',
            'semestre'      => 'required|in:SEMESTRE_1,SEMESTRE_2',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:12048',
        ]);

        $module = $this->moduleService->createModule($data, $request->file('image'));

        return response()->json(['status' => 'success', 'data' => $module], 201);
    }

    public function myModules()
    {
        $user = Auth::user();

        if ($user->user_type !== 'student') {
            return response()->json(['message' => 'Accès réservé aux étudiants'], 403);
        }

        // On récupère le profil student lié à l'user
        $modules = $this->moduleService->getModulesForStudent($user);

        return response()->json(['status' => 'success', 'data' => $modules]);
    }

    public function getModuleById($id)
    {
        $module = $this->moduleService->getModuleById($id);

        return response()->json(['status' => 'success', 'data' => $module]);
    }

    public function updateModule($id, Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'specialty_id'  => 'required|exists:specialties,id',
            'level_id'      => 'required|exists:levels,id',
            'semestre'      => 'required|in:SEMESTRE_1,SEMESTRE_2',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:12048'
        ]);

        $module = $this->moduleService->updateModule($id, $data, $request->file('image'));

        return response()->json(['status' => 'success', 'data' => $module]);
    }

    public function deleteModule($id)
    {
        $module = $this->moduleService->deleteModule($id);

        return response()->json(['status' => 'success', 'data' => $module]);
    }
}
