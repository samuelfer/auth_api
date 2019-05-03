<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiUsuarioController extends Controller
{
    public $loginAfterSignUp = true;

    public function register(Request $request)
    {
        $validar = validator($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:10'
        ]);

        if ($validar->fails()) {
            return response()->json(['error' => $validar->getMessageBag()], 401);
        }


        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        //autentica automaticamente após o cadastro bem sucedido
        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $validar = validator($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validar->fails()) {
            return response()->json(['error' => $validar->getMessageBag()], 401);
        }

        $credentials = $request->only('email', 'password');

        $jwt_token = null;

        try{
            if (!$jwt_token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false,
                    'message' => 'Email ou Senha inválido'], 401);
            }
        }catch(JWTException $e){
            return response()->json(['error' => 'Não foi possível gerar o token'], 500);
        }

        return response()->json(['success' => true, 'token' => $jwt_token]);
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([],204);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Desculpe, o usuário não pode ser deslogado'
            ], 500);
        }
    }

    //Recupera os dados do usuario autenticado
    public function getAuthUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

    public function refresh(){
        $token = \Auth::guard('api')->refresh();
        return ['token' => $token]; //No-content
    }

}
