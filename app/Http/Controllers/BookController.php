<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Autor;
use GuzzleHttp\Client;

class BookController extends Controller {

    public function index() {

        $books = Book::with('autors')->get();

        if ($books) {

            return response()->json(['success' => true, 'data' => $books], 200);
        }

        return response()->json(['success' => true], 204);
    }

    public function show($isbn) {

        $book = Book::where('isbn', $isbn)->with('autors')->first();        
 

        if (!$book) {
            return response()->json(['success' => true, 'message' => 'No se encontro data con el isbn', 'data' => $isbn], 200);
        }

        $book = $book->toArray();
                
        $httpCode = 200;
        $headers = [];
        $rootXmlTag = 'book';

        return response()->xml($book, $httpCode, $headers, $rootXmlTag);
        
    }

    public function store($isbn) {

        //Primero consultamos el isbn en nuestra base de datos
        $book = Book::where('isbn', $isbn)->first();

        //Sino existe en nuestra base de datos, se consulta el servicio
        if (!$book) {

            try {

                $client = new Client();
                $url = "https://openlibrary.org/api/books?bibkeys=ISBN:" . $isbn . "&jscmd=data&format=json";

                //Realizamos la petición al servicio
                $response = $client->request('GET', $url);

                if ($response->getBody()->getContents()) {

                    //Decodificamos la respuesta en formato json para extraer la información
                    $responseJson = json_decode($response->getBody());

                    //Recorremos el json
                    foreach ($responseJson as $obj) {
               
                        if (isset($obj->cover) && isset($obj->title) && isset($obj->authors)) {

                            //creamos el registro en la tabla book con el cover
                            $book = Book::create([
                                        'isbn' => $isbn,
                                        'title' => $obj->title,
                                        'cover_large' => $obj->cover->large
                            ]);
                        }                     

                        //Validamos que se halla creado el registro
                        if ($book) {

                            //Extraemos el array con los autores, lo recorremos y creamos el registro con el modelo
                            $authors = $obj->authors;

                            //Recorremos y creamos los registros del autor o autores del libro
                            foreach ($authors as $author) {

                                $name = $author->name;

                                Autor::create([
                                    'name' => $name,
                                    'book_id' => $book->id
                                ]);
                            }
                            
                            return response()->json(['success' => true, 'message' => 'registro creado', 'data' => $book], 200);
                        }
                    }
                }
                
                 return response()->json(['success' => true, 'message' => 'No se encontro data con el isbn', 'data' => $isbn], 200);
                
            } catch (\Exception $e) {
                
                return response()->json($e->getMessage(), 500);
            }
        }

        return response()->json(['success' => true, 'data' => $book], 200);
    }

    public function delete($isbn) {

        $book = Book::where('isbn', $isbn)->first();

        if (!$book) {
             return response()->json(['success' => true, 'message' => 'No se encontro data con el isbn', 'data' => $isbn], 200);
        }

        $book->delete();

        return response()->json(['success' => true, 'message' => 'registro Eliminado', 'data' => $book], 200);
    }

}
