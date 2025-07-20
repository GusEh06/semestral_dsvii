<?php
// generar_claves.php

// Carpeta donde se guardarán las claves
$keysDir = __DIR__ . '/keys';

// Crea la carpeta si no existe
if (!is_dir($keysDir)) {
    mkdir($keysDir, 0777, true);
    echo "✅ Carpeta 'keys' creada.\n";
}

// Archivos de salida
$privateKeyFile = $keysDir . '/private.pem';
$publicKeyFile = $keysDir . '/public.pem';

echo "🔐 Generando claves RSA de 2048 bits...\n";

// Configuración de OpenSSL
$config = [
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
];

// Generar clave privada
$res = openssl_pkey_new($config);

if ($res === false) {
    echo "❌ Error al generar la clave privada.\n";
    exit(1);
}

// Exportar clave privada a archivo
openssl_pkey_export($res, $privateKey);
file_put_contents($privateKeyFile, $privateKey);
echo "✅ Clave privada guardada en: $privateKeyFile\n";

// Extraer clave pública
$keyDetails = openssl_pkey_get_details($res);
$publicKey = $keyDetails['key'];
file_put_contents($publicKeyFile, $publicKey);
echo "✅ Clave pública guardada en: $publicKeyFile\n";

echo "🎉 Claves generadas con éxito.\n";
