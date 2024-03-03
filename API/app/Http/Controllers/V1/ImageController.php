<?php

namespace App\Http\Controllers\V1;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\OptimizedImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as ImageIntervention;

class ImageController extends Controller
{
    /**
     * Muestra un listado de todas las imágenes almacenadas.
     * Elimina los campos "created_at" y "updated_at" de la respuesta.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $images = Image::all();

        $imagesWithoutTimestamps = $images->map(function ($image) {
            return $image->only(['id', 'name']);
        });

        return response()->json($imagesWithoutTimestamps);
    }

    /**
     * Muestra el formulario para crear una nueva imagen.
     * No implementado en API REST.
     */
    public function create()
    {
        //
    }

    /**
     * Almacena una nueva imagen en el almacenamiento y crea registros en la base de datos.
     * También genera y almacena versiones optimizadas de la imagen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|max:2048',
            ]);

            $image = new Image;
            $image->name = $request->file('image')->getClientOriginalName();
            $image->save();

            $sizes = ['small' => [320, 240], 'medium' => [640, 480], 'large' => [1280, 960]];

            $optimizedImagePathBase = public_path('images/optimized');
            if (!File::exists($optimizedImagePathBase)) {
                File::makeDirectory($optimizedImagePathBase, 0755, true);
            }

            foreach ($sizes as $version => $dimensions) {
                $optimizedImagePath = 'images/optimized/' . $version . '_' . $request->file('image')->getClientOriginalName();
                $imageResized = ImageIntervention::make($request->file('image'))->resize($dimensions[0], $dimensions[1]);
                $imageResized->save(public_path($optimizedImagePath), 90, 'jpg');

                $optimizedImage = new OptimizedImage;
                $optimizedImage->image_id = $image->id;
                $optimizedImage->version = $version;
                $optimizedImage->path = $optimizedImagePath;
                $optimizedImage->url = asset($optimizedImagePath);
                $optimizedImage->save();
            }

            return response()->json([
                'message' => 'Imagen cargada y optimizada correctamente',
                'imageId' => $image->id,
                'optimizedImages' => OptimizedImage::where('image_id', $image->id)->get(['version', 'url']),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Error 400: Solicitud inválida'], 400);
        } catch (\Intervention\Image\Exception\NotSupportedException $e) {
            return response()->json(['message' => 'Error 415: Tipo de medio no soportado'], 415);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error 500: Error interno del servidor'], 500);
        }
    }

     /**
     * Muestra una imagen específica por su ID, incluyendo sus versiones optimizadas.
     * Elimina los campos "created_at" y "updated_at" de la respuesta.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $image = Image::with('optimizedImages')->find($id);

        if (!$image) {
            return response()->json(['message' => 'Error 404: Imagen no encontrada'], 404);
        }

        $image = $image->makeHidden(['created_at', 'updated_at']);
        $image->optimizedImages->makeHidden(['created_at', 'updated_at']);

        return response()->json($image, 200);
    }

    /**
     * Muestra el formulario para editar una imagen existente.
     * No implementado en API REST.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Actualiza la imagen especificada en el almacenamiento y actualiza el registro en la base de datos.
     * También actualiza sus versiones optimizadas.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ], [
            'image.required' => 'Error 400: The image field is required.',
            'image.image' => 'Error 400: The file must be an image.',
            'image.max' => 'Error 413: The image may not be greater than 2048 kilobytes in size.',
        ]);

        $image = Image::find($id);

        if (!$image) {
            return response()->json(['message' => 'Error 404: Image not found'], 404);
        }

        $optimizedImages = OptimizedImage::where('image_id', $image->id)->get();
        foreach ($optimizedImages as $optimizedImage) {
            $filePath = public_path($optimizedImage->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $image->name = $request->file('image')->getClientOriginalName();
        $image->save();

        $sizes = [
            'small' => [320, 240],
            'medium' => [640, 480],
            'large' => [1280, 960],
        ];

        foreach ($sizes as $version => $dimensions) {
            $optimizedImagePath = 'images/optimized/' . $version . '_' . $request->file('image')->getClientOriginalName();
            $imageResized = ImageIntervention::make($request->file('image'))->resize($dimensions[0], $dimensions[1]);
            $imageResized->save(public_path($optimizedImagePath), 90, 'jpg');

            $optimizedImage = OptimizedImage::where('image_id', $image->id)->where('version', $version)->first();
            if ($optimizedImage) {
                $optimizedImage->path = $optimizedImagePath;
                $optimizedImage->url = asset($optimizedImagePath);
                $optimizedImage->save();
            } else {
                $optimizedImage = new OptimizedImage;
                $optimizedImage->image_id = $image->id;
                $optimizedImage->version = $version;
                $optimizedImage->path = $optimizedImagePath;
                $optimizedImage->url = asset($optimizedImagePath);
                $optimizedImage->save();
            }
        }

        return response()->json([
            'message' => 'Imagen actualizada con éxito',
            'image' => $image,
            'optimizedImages' => OptimizedImage::where('image_id', $image->id)->get(['version', 'url']),
        ]);
    }

    /**
     * Elimina la imagen especificada del almacenamiento y su registro de la base de datos.
     * También elimina todas sus versiones optimizadas.
     *
     * @param  int $id  // ID de la imagen a eliminar.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json(['message' => 'Error 404: Imagen no encontrada'], 404);
        }

        $optimizedImages = OptimizedImage::where('image_id', $image->id)->get();

        foreach ($optimizedImages as $optimizedImage) {
            $filePath = public_path($optimizedImage->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $image->delete();

        return response()->json(['message' => 'Imagen eliminada con éxito']);
    }
}
