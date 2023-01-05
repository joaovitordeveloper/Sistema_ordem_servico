<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>

    <h2><?php echo $titulo; ?></h2>

    <table style="width: 30%; border: 1px solid black;">
        <tr>
            <th>Cor</th>
            <th>Descrição</th>
            <th>Ativo</th>
        </tr>
        <tbody>
            <?php foreach($cores as $cor): ?>
                <tr>
                    <td><?php echo $cor->nome ?></td>
                    <td><?php echo $cor->descricao ?></td>
                    <td><?php echo ($cor->ativa == true ? 'Sim' : 'Não'); ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    
    </body>
</html>
