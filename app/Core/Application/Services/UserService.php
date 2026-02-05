<?php

namespace App\Core\Application\Services;

use App\Core\Domain\Entities\User;
use App\Core\Infrastructure\Repositories\UserRepository;
use App\Mail\SuccessRegisterMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    private function generatePassword($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $password = $this->generatePassword();

            // 1. Création de l'utilisateur de base
            $user = $this->userRepository->create([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'user_type'  => $data['user_type'],
                'email'      => $data['email'],
                'password'   => Hash::make($password),
            ]);

            // 2. Création du profil spécifique selon le type
            if ($data['user_type'] === 'student') {
                $this->userRepository->createStudentProfile([
                    'user_id'       => $user->id,
                    'department_id' => $data['department_id'],
                    'specialty_id'  => $data['specialty_id'],
                    'level_id'      => $data['level_id'],
                ]);
            } elseif ($data['user_type'] === 'teacher') {
                $this->userRepository->createTeacherProfile([
                    'user_id'       => $user->id,
                    'department_id' => $data['department_id'],
                ]);
            }

            // 3. Envoi du mail (après la réussite de la transaction)
            Mail::to($user->email)->send(new SuccessRegisterMail(
                $user->first_name,
                $user->last_name,
                $user->email,
                $password,
                env('APP_URL_FRONT')
            ));

            return $user;
        });
    }

    public function updateUser(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Mise à jour des infos de base
            $this->userRepository->update($user->id, [
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $data['email'],
            ]);

            // 2. Mise à jour spécifique selon le rôle
            if ($user->isStudent()) {
                $this->userRepository->updateStudentProfile($user->id, [
                    'department_id' => $data['department_id'],
                    'specialty_id'  => $data['specialty_id'],
                    'level_id'      => $data['level_id'],
                ]);
            } elseif ($user->isTeacher()) {
                $this->userRepository->updateTeacherProfile($user->id, [
                    'department_id' => $data['department_id'],
                ]);
            }

            return $user->refresh(); // On retourne l'utilisateur à jour
        });
    }
}
