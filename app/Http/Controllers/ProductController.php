<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *  path="/products/product/",
     *  summary="Listar objetos",
     *  operationId="listarProducts",
     *  tags={"product"},
     *  @OA\Parameter(
     *    name="filter",
     *    in="query",
     *    description="Propriedade(s) do objeto para ser(em) filtro(s) da listagem.<br />
            É possível utilizar regras para montagem de filtros customizados para: Contendo texto (\*\*), Começando com texto (\*) e Comparações aritméticas (>, <, >=, <=, <>).<br />
            Ex.1: 'telefone': [{'\*\*': '(015)'}] => tabela.telefone like '%(015)%';<br />
            Ex.2: 'nome': [{'\*': 'João'}] => tabela.nome like '%João';<br />
            Ex.3: 'idade': [{'>'}: 18] => tabela.idade > 18",
     *    @OA\Schema(type="object")
     *  ),
     *  @OA\Parameter(ref="#/components/parameters/fields"),
     *  @OA\Parameter(ref="#/components/parameters/sort"),
     *  @OA\Parameter(ref="#/components/parameters/offset"),
     *  @OA\Parameter(ref="#/components/parameters/limit"),
     *  @OA\Response(
     *      response=200,
     *      description="Lista de objetos",
     *      @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref="#/components/schemas/Product")
     *      )
     *  ),
     *  @OA\Response(
     *      response=401,
     *      ref="#/components/responses/UnauthorizedError"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      ref="#/components/responses/UnexpectedError"
     *  )
     * )
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = function ($query) use($request) {
            $filter = $request->only('type','brand', 'name', 'price', 'stock');
            foreach ($filter as $name => $value) {
                if($value){
                    $query->where($name, 'LIKE', '%' . $value . '%');
                }
            }
        };

        $pessoa = Product::where($search)->orderBy('id')->paginate(10);
        return response()->json([
            "data" => $pessoa
        ], 200);
    }

    /**
     * @OA\Post(
     *  path="/products/product/",
     *  summary="Cadastrar objeto",
     *  operationId="cadastrarProduct",
     *  tags={"product"},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Informar dados do objeto",
     *      @OA\JsonContent(ref="#/components/schemas/Product"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Objeto criado com sucesso"
     *  ),
     *  @OA\Response(
     *      response=401,
     *      ref="#/components/responses/UnauthorizedError"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      ref="#/components/responses/UnexpectedError"
     *  )
     * )
     * 
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $product = Product::create($data);

        return response()->json($product, Response::HTTP_CREATED);

    }

    /**
     * @OA\Get(
     *  path="/products/product/{id}/",
     *  summary="Exibir objeto",
     *  operationId="exibirProduct",
     *  tags={"product"},
     *  @OA\Parameter(
     *      description="Código do objeto",
     *      in="path",
     *      name="id",
     *      required=true,
     *      example=1,
     *      @OA\Schema(type="integer")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Dados do objeto",
     *      @OA\JsonContent(ref="#/components/schemas/Product")
     *  ),
     *  @OA\Response(
     *      response=401,
     *      ref="#/components/responses/UnauthorizedError"
     *  ),
     *  @OA\Response(
     *      response=404,
     *      ref="#/components/responses/NotFound"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      ref="#/components/responses/UnexpectedError"
     *  )
     * )
     * 
     * Display the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        try {
            $data = Product::findOrFail($id);
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json([], 404);
        }
    }

    /**
     * @OA\Put(
     *  path="/products/product/{id}/",
     *  summary="Atualizar objeto",
     *  operationId="atualizarProduct",
     *  tags={"product"},
     *  @OA\Parameter(
     *      description="Código do objeto",
     *      in="path",
     *      name="id",
     *      required=true,
     *      example=1,
     *      @OA\Schema(type="integer")
     *  ),
     *  @OA\RequestBody(
     *      required=true,
     *      description="Informar dados do objeto",
     *      @OA\JsonContent(ref="#/components/schemas/Product"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Objeto atualizado com sucesso"
     *  ),
     *  @OA\Response(
     *      response=404,
     *      ref="#/components/responses/NotFound"
     *  ),
     *  @OA\Response(
     *      response=401,
     *      ref="#/components/responses/UnauthorizedError"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      ref="#/components/responses/UnexpectedError"
     *  )
     * )
     * 
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, int $id)
    {
        try {
            $product = Product::findOrFail($id);
            $data = $request->validated();

            $product->fill($data);
            $product->save();

            if ($request->has('type')) {
                $product->type()->associate($request->input('type'));
            }

            if ($request->has('brand')) {
                $product->brand()->associate($request->input('brand'));
            }
            
            $product->save();
            $product->update($data);
            $product = $product->fresh();

            return response()->json($product, Response::HTTP_OK);
        } catch (\Exception) {
            return response()->json([], 404);
        }
    }

    /**
     * @OA\Delete(
     *  path="/products/product/{id}/",
     *  tags={"product"},
     *  summary="Excluir objeto",
     *  operationId="excluirProduct",
     *  @OA\Parameter(
     *      description="Código do objeto",
     *      in="path",
     *      name="id",
     *      required=true,
     *      @OA\Schema(type="integer")
     *  ),
     *  @OA\Response(
     *      response=204,
     *      description="Objeto excluído com sucesso"
     *  ),
     *  @OA\Response(
     *      response=401,
     *      ref="#/components/responses/UnauthorizedError"
     *  ),
     *  @OA\Response(
     *      response=404,
     *      ref="#/components/responses/NotFound"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      ref="#/components/responses/UnexpectedError"
     *  )
     * )
     * 
     * Remove the specified resource from storage.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try{
            Product::findOrFail($id)->delete();
            return response()->json([], Response::HTTP_NO_CONTENT);
        }catch (\Exception $e) {
            return response()->json([], 404);
        }
    }

    /**
     * @OA\Put(
     *  path="/products/product/{id}/restore/",
     *  tags={"product"},
     *  summary="Restaurar objeto excluído",
     *  operationId="restaurarProduct",
     *  @OA\Parameter(
     *      description="Código do objeto",
     *      in="path",
     *      name="id",
     *      required=true,
     *      @OA\Schema(type="integer")
     *  ),
     *  @OA\Response(
     *      response=204,
     *      description="Objeto restaurado com sucesso"
     *  ),
     *  @OA\Response(
     *      response=401,
     *      ref="#/components/responses/UnauthorizedError"
     *  ),
     *  @OA\Response(
     *      response=404,
     *      ref="#/components/responses/NotFound"
     *  ),
     *  @OA\Response(
     *      response=500,
     *      ref="#/components/responses/UnexpectedError"
     *  )
     * )
     * 
     * Restore the specified resource from storage.
     *
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function restore(int $id)
    {
        try {
            Product::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json([], 404);
        }
    }
}
