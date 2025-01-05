<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserService {

    public function findUserByEmail($email){
        $user = User::where('email', $email)->first();
        
        return $user;
    }

    public function findUserById($id){
        $user = User::findOrFail($id);

        if(!$user){
            throw new \Exception('Введены неверные данные');
        }

        return $user;
    }

    public function createUser($data){

        if($this->findUserByEmail($data['email'])){
            throw new \Exception('Аккаунт по указанным данным уже существует');
        };

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;

    }

    public function updateUser($data, $id){
        $user = $this->findUserById($id);

        $user = User::update([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }
    
    public function verifyUser($data){

        $user = $this->findUserByEmail($data['email']);

        if(!$user){
            throw new \Exception('Введены неверные данные');
        };

        if(!Hash::check($data['password'], $user->password)){
            throw new \Exception('Введены неверные данные');
        }

        return $user;
    }
}