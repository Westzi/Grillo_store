<?php
// Teste para verificar se as imagens existem
$images = [
    "../imagens-produtos/box1.jpg",
    "../imagens-produtos/box2.jpg", 
    "../imagens-produtos/box3.jpg",
    "../imagens-produtos/box4.jpg",
    "../imagens-produtos/box5.jpg"
];

echo "<h1>Teste de Imagens - Produto 16</h1>";
foreach ($images as $image) {
    if (file_exists($image)) {
        echo "<p style='color: green;'>✓ $image - EXISTE</p>";
        echo "<img src='$image' width='100' style='border: 1px solid green;'><br><br>";
    } else {
        echo "<p style='color: red;'>✗ $image - NÃO ENCONTRADA</p>";
    }
}
?>