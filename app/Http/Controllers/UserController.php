<?php

namespace App\Http\Controllers;

use App\Core\Application\Services\userService;
use App\Core\Domain\Entities\User;
use App\Core\Infrastructure\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected UserRepository $userRepository
    ) {}

    public function getAll()
    {
        $users = $this->userRepository->getAll();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ], 200);
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users',
            'user_type'     => 'required|in:student,teacher',
            // Champs conditionnels
            'department_id' => 'required|exists:departments,id',
            'specialty_id'  => 'required_if:user_type,student|exists:specialties,id',
            'level_id'      => 'required_if:user_type,student|exists:levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $this->userService->createUser($request->all());
            return response()->json(['status' => 'success', 'message' => 'Compte et profil créés !'], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Erreur lors de la création'], 500);
        }
    }


    public function updateUser(Request $request, $id)
    {
        $user = User::findOrfaild($id);

        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            // Validation selon le type de l'utilisateur connecté
            'department_id' => 'required|exists:departments,id',
            'specialty_id'  => 'required_if:user_type,student|exists:specialties,id',
            'level_id'      => 'required_if:user_type,student|exists:levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $updatedUser = $this->userService->updateUser($user, $request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Profil mis à jour avec succès',
                'user' => $updatedUser
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    public function changeStatusUser(Request $request, $id)
    {
        User::findOrfaild($id);
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try{
            // modification du status 
            User::where('id', $id)->update(['status' => $request->status]);
            return response()->json([
                'status' => 'success',
                'message' => 'Statut modifié avec succès'
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du changement de statut'
            ], 500);
        }

    }
}
