<?php
// Verificar si se ha enviado el formulario
if (isset($_POST['calcular'])) {
    // Obtener la operación del formulario
    $operacion = $_POST['operacion'];
    
    // Validar la operación para evitar código malicioso
    if (!preg_match('/^[0-9\+\-\*\/\.]+$/', $operacion)) {
        $resultado = "Error: Operación no válida";
    } else {
        // Procesar la operación matemática de forma segura sin usar eval()
        $resultado = procesarOperacion($operacion);
    }
}

/**
 * Procesa una operación matemática de forma segura
 * @param string $operacion La operación a procesar
 * @return float|string El resultado o un mensaje de error
 */
function procesarOperacion($operacion) {
    // Eliminar espacios en blanco si los hubiera
    $operacion = str_replace(' ', '', $operacion);
    
    // Verificar división por cero
    if (preg_match('/\/0(\D|$)/', $operacion)) {
        return "Error: División por cero";
    }
    
    // Extraer números y operadores
    $numeros = [];
    $operadores = [];
    $numero = '';
    
    for ($i = 0; $i < strlen($operacion); $i++) {
        $char = $operacion[$i];
        
        // Si es un dígito o punto decimal, añadirlo al número actual
        if (is_numeric($char) || $char == '.') {
            $numero .= $char;
        } 
        // Si es un operador, guardar el número anterior y el operador
        else if ($char == '+' || $char == '-' || $char == '*' || $char == '/') {
            // Guardar el número completado
            if ($numero !== '' || count($numeros) == 0) {
                $numeros[] = ($numero === '') ? 0 : floatval($numero);
                $numero = '';
            }
            
            // Guardar el operador
            $operadores[] = $char;
        }
    }
    
    // Añadir el último número
    if ($numero !== '') {
        $numeros[] = floatval($numero);
    }
    
    // Si no hay suficientes números, retornar error
    if (count($numeros) <= 0) {
        return "Error: Operación inválida";
    }
    
    // Realizar las operaciones siguiendo el orden de precedencia
    $resultado = realizarOperaciones($numeros, $operadores);
    
    // Formatear el resultado para mostrar enteros sin decimales
    if (is_float($resultado) && floor($resultado) == $resultado) {
        $resultado = (int)$resultado;
    }
    
    return $resultado;
}

/**
 * Realiza las operaciones siguiendo el orden de precedencia
 * @param array $numeros Lista de números
 * @param array $operadores Lista de operadores
 * @return float El resultado final
 */
function realizarOperaciones($numeros, $operadores) {
    // Si no hay operadores, devolver el único número
    if (empty($operadores)) {
        return $numeros[0];
    }
    
    // Primero realizar multiplicaciones y divisiones
    for ($i = 0; $i < count($operadores); $i++) {
        if ($operadores[$i] == '*' || $operadores[$i] == '/') {
            $resultado = 0;
            
            if ($operadores[$i] == '*') {
                $resultado = $numeros[$i] * $numeros[$i + 1];
            } else if ($operadores[$i] == '/') {
                // Verificar división por cero
                if ($numeros[$i + 1] == 0) {
                    return "Error: División por cero";
                }
                $resultado = $numeros[$i] / $numeros[$i + 1];
            }
            
            // Reemplazar los dos números por el resultado
            array_splice($numeros, $i, 2, [$resultado]);
            // Eliminar el operador
            array_splice($operadores, $i, 1);
            // Retroceder el índice para procesar desde la nueva posición
            $i--;
        }
    }
    
    // Luego realizar sumas y restas
    while (!empty($operadores)) {
        $resultado = 0;
        
        if ($operadores[0] == '+') {
            $resultado = $numeros[0] + $numeros[1];
        } else if ($operadores[0] == '-') {
            $resultado = $numeros[0] - $numeros[1];
        }
        
        // Reemplazar los dos números por el resultado
        array_splice($numeros, 0, 2, [$resultado]);
        // Eliminar el operador
        array_splice($operadores, 0, 1);
    }
    
    return $numeros[0];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Calculadora Simple</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="calculadora-container">
        <h1>Resultado</h1>
        <div class="pantalla">
            <input type="text" value="<?php echo isset($resultado) ? $resultado : ''; ?>" readonly>
        </div>
        <div class="botones">
            <div class="fila">
                <a href="index.html" style="text-decoration: none; width: 100%;">
                    <button type="button" style="width: 100%;">Volver a la calculadora</button>
                </a>
            </div>
        </div>
    </div>
</body>
</html>