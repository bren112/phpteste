<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta</title>
    <link rel="stylesheet" href="./style/style.css">
    <style>

a {
            text-decoration: none;
            background-image: linear-gradient(45deg, #f39c12, #e67e22);
            color: white;
            width: 10pc;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        a:hover {
            background-image: linear-gradient(45deg, #e67e22, #f39c12);
            transform: translateY(-2px); /* Efeito de levitação */
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            h1 {
                font-size: 24px;
            }

            a {
                font-size: 16px;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="./source/logo.png" alt="Logo" id="logo">
        <div class="container_center">
        <h1>Só Para quem ama festa!</h1>
        <h1>pague</h1>
        <br>
        <br>
        <img id="cont" src="./source/imgPag.png" alt="" srcset="">
        <br>
        <br>
        <a href="/apii/pix.php">Adquirir entrada</a>
        
    </div>
    </div>
</body>
</html>
