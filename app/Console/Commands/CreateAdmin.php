<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if (User::where('user_type', "super-admin")->exists()) {
            $this->error('Un super administrateur existe déjà.');
            return 1;
        }

        $firstname = $this->ask('Entrez le nom de l\'administrateur');
        $lastname = $this->ask('Entrez le prénom de l\'administrateur');
        $email = $this->ask('Entrez l\'adresse email');
        $password = $this->secret('Entrez le mot de passe');

        User::create([
            'first_name' => $firstname,
            'last_name' => $lastname,
            'user_type' => 'admin',
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info('Admin créé avec succès !');
    }
}
