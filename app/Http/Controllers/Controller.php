<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\OpenApi(
 *  @OA\Info(
 *      version="1.0.0",
 *      title="API Laravel",
 *      description="API of Products",
 *      @OA\Contact(
 *          name="Suporte",
 *          email="suporte@gmail.com.br"
 *      )
 *  ),
 *  @OA\Server(
 *      url="http://localhost:{porta}/api/",
 *      description="Servidor de desenvolvimento",
 *      @OA\ServerVariable(
 *          serverVariable="porta",
 *          enum={"8000", "8001"},
 *          default="8000"
 *      )
 *  ),
 *  @OA\Components(
 *      @OA\SecurityScheme(
 *          type="http",
 *          scheme="bearer",
 *          name="JWT",
 *          securityScheme="JWT",
 *          bearerFormat="JWT",
 *          in="header"
 *      ),
 *      @OA\Response(
 *          response="UnauthorizedError",
 *          description="Token de acesso inválido, expirado ou não encontrado",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              example={"message": "Token não encontrado."}
 *          )
 *      ),
 *      @OA\Response(
 *          response="NotFound",
 *          description="Não encontrado",
 *          @OA\MediaType(
 *              mediaType="application/json"
 *          )
 *      ),
 *      @OA\Response(
 *          response="UnexpectedError",
 *          description="Erro inesperado",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              example={"status": "error", "message": "An exception occurred while executing a query: [...]"}
 *          )
 *      ),
 *      @OA\Parameter(
 *        name="fields",
 *        in="query",
 *        @OA\Schema(type="string"),
 *        description="Propriedade(s) do objeto a ser(em) exibida(s) no retorno da listagem",
 *      ),
 *      @OA\Parameter(
 *        name="sort",
 *        in="query",
 *        @OA\Examples(example="format1", summary="Dot format", value="PROPRIEDADE1.desc, PROPRIEDADE2.asc"),
 *        @OA\Examples(example="format2", summary="Parentheses format", value="desc(PROPRIEDADE1), asc(PROPRIEDADE2)"),
 *        @OA\Schema(type="string"),
 *        description="Propriedade(s) do objeto a ser(em) usada(s) como critério de ordenação da listagem",
 *      ),
 *      @OA\Parameter(
 *        name="offset",
 *        in="query",
 *        @OA\Schema(type="integer"),
 *        description="Quantidade de registros a desconsiderar de onde começar a listagem"
 *      ),
 *      @OA\Parameter(
 *        name="limit",
 *        in="query",
 *        @OA\Schema(type="integer"),
 *        description="Quantidade de registros que a listagem irá retornar"
 *      ),
 *  ),
 *  security={
 *      {"JWT": {}}
 *  }
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
