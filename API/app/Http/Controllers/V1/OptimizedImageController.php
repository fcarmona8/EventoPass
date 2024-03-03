<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Models\OptimizedImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class OptimizedImageController extends Controller
{
    /**
    * Muestra un listado de todas las imágenes optimizadas almacenadas.
    * Elimina los campos "created_at" y "updated_at" de la respuesta para simplificarla.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function index()
    {
        $imagesOptimized = OptimizedImage::all();

        $imagesOptimizedWithoutTimestamps = $imagesOptimized->map(function ($imagesOptimized) {
            return $imagesOptimized->only(['id', 'image_id', 'version', 'path', 'url']);
        });

        return response()->json($imagesOptimizedWithoutTimestamps);
    }

    /**
     * Muestra el formulario para crear una nueva imagen optimizada.
     * No implementado en API REST.
     */
    public function create()
    {
        //
    }

    /**
     * Almacena una nueva imagen optimizada en el almacenamiento.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Muestra una imagen optimizada específica basada en su ID de imagen y versión.
     * Valida que la versión especificada sea válida ('small', 'medium', 'large') antes de proceder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $image_id
     * @param  string  $version
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $image_id, $version)
    {
        $validator = Validator::make(['version' => $version], [
            'version' => 'required|in:small,medium,large',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Error 400: Invalid version specified.'], 400);
        }

        $optimizedImage = OptimizedImage::where('image_id', $image_id)->where('version', $version)->first();

        if (!$optimizedImage) {
            return response()->json(['message' => 'Error 404: Optimized Image not found'], 404);
        }

        $imagePath = public_path($optimizedImage->path);
        if (file_exists($imagePath)) {
            $type = mime_content_type($imagePath);
            $file = file_get_contents($imagePath);
            
            return response($file, 200)->header("Content-Type", $type);
        } else {
            return response()->json(['message' => 'Image file not found'], 404);
        }
    }

    /**
     * Muestra el formulario para editar una imagen optimizada existente.
     * No implementado en API REST.
     */
    public function edit(OptimizedImage $optimizedImage)
    {
        //
    }

    /**
     * Actualiza una imagen optimizada especificada en el almacenamiento.
     */
    public function update(Request $request, OptimizedImage $optimizedImage)
    {
        //
    }

    /**
     * Elimina una imagen optimizada especificada del almacenamiento.
     */
    public function destroy(OptimizedImage $optimizedImage)
    {
        //
    }
}
