<?php 
// URL de la API local que proporciona LM Studio
$apiUrl = 'http://localhost:1234/v1/chat/completions';

// La pregunta que quieres hacer a la IA
$pregunta = '¿Cuál es la capital de Nayarit y qué puedo visitar allí?';

// Estructura de datos para la petición (formato OpenAI)
$data = [
    'model' => 'local-model', // No es crucial, el servidor usa el modelo cargado
    'messages' => [
        [
            'role' => 'system',
            'content' => 'Eres un asistente útil y directo.'
        ],
        [
            'role' => 'user',
            'content' => $pregunta
        ]
    ],
    'temperature' => 0.7,
];

// Inicializar cURL
$ch = curl_init($apiUrl);

// Configurar las opciones de cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);

// Ejecutar la petición y obtener la respuesta
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Procesar la respuesta
if ($error) {
    echo "Error en la petición cURL: " . $error;
} else {
    $responseData = json_decode($response, true);
    // Extraer y mostrar la respuesta de la IA
    $respuestaIA = $responseData['choices'][0]['message']['content'];

    echo "<h2>Pregunta:</h2>";
    echo "<p>" . htmlspecialchars($pregunta) . "</p>";
    echo "<h2>Respuesta de la IA:</h2>";
    echo "<p>" . nl2br(htmlspecialchars($respuestaIA)) . "</p>";
}

?>