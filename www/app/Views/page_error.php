<!DOCTYPE html>
<html lang='pt-BR'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title><?= $title; ?></title>
  <style>
    body {
      display: flex;
      min-height: 100vh;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background-color: #f2f2f2;
      font-family: Arial, sans-serif;
    }

    .card-panel {
      padding: 40px;
      text-align: center;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      max-width: 320px;
    }

    h1 {
      font-size: 3rem;
      color: #007bff;
      margin-bottom: 10px;
    }

    p {
      font-size: 1.2rem;
      color: #777;
      margin-bottom: 20px;
    }

    .btn {
      background-color: #007bff;
      color: #fff;
      display: inline-block;
      padding: 12px 24px;
      border-radius: 4px;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .btn:hover {
      background-color: #0069d9;
    }

    @media (max-width: 425px) {
      h1 {
        font-size: 2.5rem;
      }

      p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class='card-panel'>
    <h1><?= $h1; ?></h1>
    <p><?= $p; ?></p>
    <a href='<?= $a_href; ?>' class='btn'><?= $a_text; ?></a>
  </div>
</body>
</html>
