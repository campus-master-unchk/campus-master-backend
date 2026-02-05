<?php 

namespace App\Http\Controllers;

use App\Core\Application\Services\LevelService;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function __construct(protected LevelService $levelService) {}

    /**
     * Liste tous les niveaux (utile pour les formulaires d'inscription sur Next.js)
     */
    public function getAllLevel()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->levelService->listLevels()
        ]);
    }

    /**
     * Création d'un nouveau niveau (Réservé à l'Admin via Middleware)
     */
    public function createLevel(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:levels,name|max:50'
        ]);

        $level = $this->levelService->addLevel($data['name']);

        return response()->json([
            'status' => 'success',
            'message' => 'Niveau créé',
            'data' => $level
        ], 201);
    }

    /**
     * Mise à jour d'un niveau (Réservé à l'Admin via Middleware)
     */
    public function updateLevel(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:levels,name,' . $id . '|max:50'
        ]);

        $level = $this->levelService->updateLevel($id, $data['name']);

        return response()->json([
            'status' => 'success',
            'message' => 'Niveau mis à jour',
            'data' => $level
        ]);
    }

    /**
     * Suppression d'un niveau (Réservé à l'Admin via Middleware)
     */
    public function deleteLevel($id)
    {
        $this->levelService->removeLevel($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Niveau supprimé'
        ]);
    }
}