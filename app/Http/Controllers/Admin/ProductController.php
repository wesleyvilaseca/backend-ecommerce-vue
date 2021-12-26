<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDepartament;
use App\Models\ProductDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private $product;
    private $product_departament;
    private $product_description;

    public function __construct(
        Product             $product,
        ProductDepartament     $product_departament,
        ProductDescription  $product_description
    ) {
        $this->product                 = $product;
        $this->product_departament     = $product_departament;
        $this->product_description     = $product_description;
    }

    public function index(Request $request)
    {
        $products = $this->product->paginate(10);
        if (!$products || (sizeof($products) <= 0)) {
            return response()->json(['error' => 'Não há produtos cadastrados'], 203);
        }

        return response()->json($products);
    }

    public function getProduct(Request $request, $id)
    {
        if (!$request->id) return response()->json(['error' => 'Informe o id do produto'], 203);
        $product = $this->product->find($id);

        if (!$product) {
            return response()->json(['error' => 'produto não localizado'], 503);
        }

        return response()->json($product, 200);
    }

    public function save(Request $request)
    {
        if (!$request->name) return response()->json(['error' => 'Informe o nome do produto'], 203);

        $seo = Str::slug($request->name, '-');
        $exist = $this->product->where('seo', '=', $seo)->first();
        if ($exist) return response()->json(['error' => 'Já existe um produto com esse nome'], 203);

        $this->product->model           = $request->model;
        $this->product->sku             = $request->sku;
        $this->product->quantity        = tofloat($request->quantity);
        $this->product->status          = $request->status;
        $this->product->shipping        = $request->shipping;
        $this->product->price           = tofloat($request->price);
        $this->product->weight_id       = $request->weight_id;
        $this->product->lenght_id       = $request->lenght_id;
        $this->product->height          = $request->height;
        $this->product->width           = $request->width;
        $this->product->length          = $request->length;
        $this->product->weight          = $request->weight;
        $this->product->seo             = $seo;
        $this->product->stock_status_id = $request->stock_status_id;

        $productId = $this->product->save();

        if (!$productId) return response()->json(['error' => 'erro na operação'], 503);

        $this->product_description->product_id  = $productId;
        $this->product_description->name        = $request->name;
        $this->product_description->description = $request->content;

        $productDescriptionId = $this->product_description->save();

        if (!$productDescriptionId) {
            $this->product->where('id', $productId)->delete();
            return response()->json(['error' => 'erro na operação'], 503);
        }


        if (sizeof($request->departaments) > 0) {
            $data = [];
            foreach ($request->departaments as $departament) {
                $data[] = ['product_id' => $productId, 'departament_id' => $departament['id']];
            }
            ProductDepartament::insert($data);
        }

        return response()->json(['msg' => 'Produto criado com sucesso!'], 200);
    }

    public function imageUpload(Request $request, int $product_id)
    {
        if (!$request->id) return response()->json(['error' => 'Informe o id do produto'], 203);
        $product = $this->product->find($product_id);

        if (!$product) {
            return response()->json(['error' => 'produto não localizado'], 203);
        }

        $image = $request->file('image');
        if (!$image->hasFile('image')) {
            return response()->json(['error' => 'Imagem não localizada'], 203);
        }

        $name = "";
        if (count($image) > 1) {
            foreach ($image as $img) {
                $name = rand() . '.' . $img->getClientOriginalExtension();
                $image->move(public_path('/uploads/images/product'), $name);
            }
            return  response()->json(['error' => 'Imagem cadastrada com sucesso!'], 200);
        }

        $name = rand() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('/uploads/images/product'), $name);

        return  response()->json(['error' => 'Imagem cadastrada com sucesso!'], 200);
    }
}
