<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Liga;
use App\Models\Equipo;
use App\Models\Fecha;
use App\Models\Partido;
use Illuminate\Support\Facades\Validator;

class LigaController extends Controller
{
    /**
     * Obtener todas las ligas del usuario autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Obtener el usuario autenticado
        $user = auth()->user();
        //dd($user);
        // Obtener las ligas del usuario
        $ligas = $user->ligas;

        return jsonResponse(['ligas' => $ligas], 200, 'Ligas obtenidas exitosamente');
    }

    /**
     * Crear una nueva liga.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return jsonResponse([], 422, 'Error de validación', $validator->errors());
        }

        // Crear la liga
        $liga = Liga::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'user_id' => auth()->id(), // Asignar el ID del usuario autenticado
        ]);

        return jsonResponse(['liga' => $liga], 201, 'Liga creada exitosamente');
    }

    /**
     * obetener todas las ligas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllLigas()
    {
        // Obtener todas las ligas con sus respectivos creadores
        $ligas = Liga::with('user')->get();

        return jsonResponse(['ligas' => $ligas], 200, 'Todas las ligas obtenidas exitosamente');
    }

    /**
     * Obtener los equipos de una liga.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showEquipos($id)
    {
        // Obtener la liga con sus equipos
        $liga = Liga::with('equipos')->find($id);

        if (!$liga) {
            return jsonResponse([], 404, 'Liga no encontrada');
        }

        //['equipos' => $equipos] = $liga->toArray();
        
        //dd($equipos);
        //$array = $this->generarFixture($equipos);


        return jsonResponse(['liga' => $liga], 200, 'Equipos de la liga obtenidos exitosamente');
    }

    public function generarFixture(Request $request)
    {
        $ligaId = $request->validate([
            'liga' => 'required|integer', // Debe ser un array con al menos 2 equipos
        ])['liga'];
            
        //dd($request['liga']);
        $equipos = $request->validate([
            'equipos' => 'required|array|min:2', // Debe ser un array con al menos 2 equipos
            'equipos.*.id' => 'required|integer',
            'equipos.*.nombre' => 'required|string',
        ])['equipos'];

        $cantidadEquipos = count($equipos);
        $fixture = [];
        
        // Si el número de equipos es impar, agregamos un "equipo fantasma"
        $hayDescanso = $cantidadEquipos % 2 !== 0;
        if ($hayDescanso) {
            $equipos[] = ["id" => null, "nombre" => "Descansa"]; // Equipo de descanso
            $cantidadEquipos++;
        }

        // Generamos las jornadas (round-robin)
        // Generamos las jornadas (round-robin)
        for ($i = 0; $i < $cantidadEquipos - 1; $i++) {
            $fecha = "Fecha " . ($i + 1);
            $partidos = [];

            for ($j = 0; $j < $cantidadEquipos / 2; $j++) {
                $local = $equipos[$j];
                $visitante = $equipos[$cantidadEquipos - 1 - $j];

                // Si hay un equipo de descanso, lo agregamos como tal
                if ($local["nombre"] === "Descansa") {
                    $partidos[] = [
                        "descansa" => [
                            "id" => $visitante["id"],
                            "nombre" => $visitante["nombre"]
                        ]
                    ];
                } elseif ($visitante["nombre"] === "Descansa") {
                    $partidos[] = [
                        "descansa" => [
                            "id" => $local["id"],
                            "nombre" => $local["nombre"]
                        ]
                    ];
                } else {
                    $partidos[] = [
                        "local" => [
                            "id" => $local["id"],
                            "nombre" => $local["nombre"]
                        ],
                        "visitante" => [
                            "id" => $visitante["id"],
                            "nombre" => $visitante["nombre"]
                        ]
                    ];
                }
            }

            $fixture[$fecha] = $partidos;

            // Rotamos los equipos para la siguiente ronda (dejando fijo el primero)
            array_splice($equipos, 1, 0, array_splice($equipos, -1, 1));
        }
        //dd($fixture);

        $fechasArray = $fixture; // Suponiendo que envías el array en la request

        foreach ($fechasArray as $numeroFecha => $partidos) {
            // Extraer el número de la fecha (Ej: "Fecha 1" -> 1)
            $numeroFecha = intval(str_replace('Fecha ', '', $numeroFecha));
    
            // Insertar la fecha en la base de datos
            $fecha = Fecha::create([
                'liga_id' => $ligaId,
                'fecha' => $numeroFecha,
            ]);
    
            // Recorrer los partidos de la fecha
            foreach ($partidos as $partido) {
                if (isset($partido['descansa'])) {
                    // Si un equipo descansa, se inserta en la tabla con la columna "descansa"
                    Partido::create([
                        'fecha_id' => $fecha->id,
                        'descansa' => $partido['descansa']['id'],
                    ]);
                } else {
                    // Si hay un partido entre dos equipos
                    Partido::create([
                        'fecha_id' => $fecha->id,
                        'equipo_local' => $partido['local']['id'],
                        'equipo_visitante' => $partido['visitante']['id'],
                    ]);
                }
            }
        }
        return jsonResponse(['fixture' => $fixture], 200, 'fixture creado y guardado exitosamente');
    }

      
    public function inscribirEquipo(Request $request, $id)
    {

        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return jsonResponse([], 422, 'Error de validación', $validator->errors());
        }

        // Verificar que la liga exista
        $liga = Liga::find($id);
        if (!$liga) {
            return jsonResponse([], 404, 'Liga no encontrada');
        }

        $existeNombre = Equipo::where('liga_id', $id)
            ->where('nombre', $request->nombre)
            ->exists();

        if ($existeNombre) {
            return jsonResponse([], 400, 'El nombre del equipo ya está registrado en esta liga');
        }

        // Crear el equipo
        $equipo = Equipo::create([
            'nombre' => $request->nombre,
            'liga_id' => $id,
            'user_id' => auth()->id(), // Asignar el ID del usuario autenticado
        ]);

        return jsonResponse(['equipo' => $equipo], 201, 'Equipo inscrito exitosamente');
    }

    public function getEquiposAll()
    {
        $equipos = Equipo::with(['liga', 'user'])->get();
    
        return jsonResponse(['equipos' => $equipos], 200, 'Equipos obtenidos correctamente');
    }
}
