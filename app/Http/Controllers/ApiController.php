<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    /**
     * @OA\Post(
     *  path="/cadastrar",
     *  description="Credenciamento de usuário",
     *  operationId="register",
     *  tags={"auth"},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Informe credenciais do usuário",
     *      @OA\JsonContent(
     *          required={"name", "email", "password"},
     *          @OA\Property(property="name", type="string", example="User One"),
     *          @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *          @OA\Property(property="password", type="string", format="password", example="password12345"),
     *      )
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Usuário cadastrado com sucesso"
     *  ),
     *  security={}
     * )
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request)
    {
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *  path="/login",
     *  description="Login informando email e password",
     *  operationId="login",
     *  tags={"auth"},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Informe credenciais do usuário",
     *      @OA\JsonContent(
     *          required={"email", "password"},
     *          @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *          @OA\Property(property="password", type="string", format="password", example="password12345")
     *      )
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Credenciais incorretas",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Credenciais de login inválidas")
     *      )
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Erro de login",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Não foi possível criar o token")
     *      )
     *  ),
     *  security={}
     * )
     *
     * @param Request $request
     * @return void
     */
    public function authenticate(Request $request)
    {
        $credentials = ['email' => $request->get('email'), 'password' => $request->get('password')];

        $validator = Validator::make($request->only('email', 'password'), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid login credentials'
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to create token'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'token' => $token
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *  path="/logout",
     *  description="Deslogar usuário",
     *  operationId="logout",
     *  tags={"auth"},
     *  @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean", example=true),
     *          @OA\Property(property="message", type="string", example="Usuário foi deslogado")
     *      )
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Erro ao deslogar usuário",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Desculpe, usuário não foi deslogado. Tente novamente."),
     *      )
     *  )
     * )
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ], Response::HTTP_OK);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user has not been logged out. Try again.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
