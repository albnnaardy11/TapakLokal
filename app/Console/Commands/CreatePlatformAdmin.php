<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AccessService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreatePlatformAdmin extends Command
{
    protected $signature = 'platform:admin {email} {--name=Administrator}';

    protected $description = 'Create a platform administrator without a default password';

    public function handle(AccessService $access): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Jalankan secara interaktif untuk memasukkan password tersembunyi.');

            return self::FAILURE;
        }
        $password = $this->secret('Password baru (minimal 10 karakter, huruf dan angka)');
        $data = ['email' => $this->argument('email'), 'name' => $this->option('name'), 'password' => $password];
        $validator = Validator::make($data, ['email' => ['required', 'email', 'unique:users,email'], 'name' => ['required', 'string', 'max:100'], 'password' => ['required', Password::min(10)->letters()->numbers()]]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        DB::transaction(function () use ($data, $access) {
            $access->seed();
            $access->grant(User::create($data), 'super_admin');
        });
        $this->info('Administrator dibuat. Masuk melalui /login.');

        return self::SUCCESS;
    }
}
