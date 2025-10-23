<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthService
{
    protected $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function register(array $data)
    {
        // Simulando um tempo de espera para exibir os loadings no front
        sleep(1.5);

        $data['password'] = Hash::make($data['password']);
        $user = $this->repository->createUser($data);

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(array $data)
    {
        // Simulando um tempo de espera para exibir os loadings no front
        sleep(1.5);

        $user = $this->repository->findByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new Exception('Usuário ou senha estão incorretos.');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'ok',
            'token' => $token,
            'user' => $user
        ];
    }

    public function logout($user)
    {
        $user->currentAccessToken()->delete();
    }
}
