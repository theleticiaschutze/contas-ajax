<!-- a parte do json fica dentro do index agora e retorna php não ajax/js AINDA ESTOU ADAPTANDO!!!!!!!!!!!!!!!!11! -->
    <?php    
$dados = 'contas.json'; //arquivo json onde os dados ficam
$contas = json_decode(file_get_contents($dados), true) ?? []; //decodifica para formato que array q o php usa

$acao = $_POST['acao'] ?? $_GET['acao'] ?? ''; //pode usar get ou post para acao, ou vazio

if ($acao == 'inserir') {  //se acao for inserir
    $adicionarId = empty($contas) ? 1 : max(array_keys($contas)) + 1; //se for vazio começa de 1, se tiver id adiciona mais 1
     $contas[$adicionarId] = [ //id é chave do objeto    
        "codigo"     => $_POST['codigo'],
        "favorecido"     => $_POST['favorecido'],
        "vencimento" => $_POST['vencimento'],
        "valor"    => str_replace(',', '.', $_POST['valor'])
    ];
    file_put_contents($dados, json_encode($contas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: index.php?status=sucesso");
    exit;

} else if ($acao == 'atualizar') { //se acao for atualizar
    $id = $_POST['id'];  //esse retorna e continua o mesmo
    $contas[$id]['codigo']     = $_POST['codigo'];
    $contas[$id]['favorecido']     = $_POST['favorecido'];
    $contas[$id]['vencimento'] = $_POST['vencimento'];
    $contas[$id]['valor']    = str_replace(',', '.', $_POST['valor']);                
    file_put_contents($dados, json_encode($contas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: index.php?status=atualizado");
    exit;

} else if ($acao == 'remover') { 
    $id = $_GET['id']; //recebe o id do contato que vai ser excluido
    unset($contas[$id]); //remove contato
    file_put_contents($dados, json_encode($contas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: index.php?status=removido");
    exit;
}

$contaModificar = null;
if ($acao == 'modificar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (isset($contas[$id])){
        $contaModificar = $contas[$id];
        $contaModificar['_id'] = $id;
    }
}

    
    
    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <title>Contas a pagar</title>
</head>
<body>

<div class="container mt-4" style="max-width: 1200px">
<h1 class="my-4 text-center"> Registro de contas a pagar</h1>

<?php $status = $_GET['status'] ?? ''; ?>

<?php if ($status === 'sucesso') { ?>
    <div class="alert alert-success alert-dismissible fade show">
        Conta foi registrada!!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } elseif ($status === 'atualizado') { ?>
    <div class="alert alert-info alert-dismissible fade show">
        Conta foi atualizada!!!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } elseif ($status === 'removido') { ?>
    <div class="alert alert-warning alert-dismissible fade show">
        Conta foi removida.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

    <div class="row">
    <div class="col-lg-4 mb-4">
<!-- Formulário 4/12 -->
<form method="post" action="index.php">
    <input type="hidden" name="acao" value="<?= $contaModificar ? 'atualizar' : 'inserir' ?>">
    <input type="hidden" name="id" value="<?= $contaModificar['_id'] ?? '' ?>">           
        <!--form para inserir e atualizar a tabela-->
        
        <div class="card shadow-sm p-3">             
                    
                    <div class="mb-3">
                        <label for="codigo">Código:</label>
                        <input type="text" name="codigo" id="codigo" class="form-control" required autofocus
                            value="<?= $contaModificar['codigo'] ?? '' ?>">
                    </div> 
                    <div class="mb-3">
                        <label for="favorecido">Favorecido:</label>
                        <input type="text" name="favorecido" id="favorecido" class="form-control" required
                            value="<?= $contaModificar['favorecido'] ?? '' ?>">
                    </div>    
                    <div class="mb-3">
                        <label for="vencimento">Vencimento:</label>
                        <input type="date" name="vencimento" id="vencimento" class="form-control" required
                            value="<?= $contaModificar['vencimento'] ?? '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="valor">Valor:</label>
                        <input type="text" name="valor" id="valor" class="form-control" placeholder="00.00" step="0.01" required
                            value="<?= $contaModificar['valor'] ?? '' ?>">
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success">Salvar Conta</button>                                              
                    </div>                    
        </div>      
                </form>
        </div>


 <!-- tabela 8/12 -->
    <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabela-toda"> 
                        <thead class="table-light">
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Favorecido</th>
                    <th scope="col">Vencimento</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Ações</th>                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contas as $id => $conta){ ?>
        <tr>
            <td><?= $conta['codigo'] ?></td>
            <td><?= $conta['favorecido'] ?></td>
            <td><?= $conta['vencimento'] ?></td>
            <td>R$ <?= number_format($conta['valor'], 2, ',', '.') ?></td>
            <td>
                <a href="?acao=modificar&id=<?= $id ?>">Modificar</a>
                <a href="?acao=remover&id=<?= $id ?>" onclick="return confirm('Deseja remover?')">Remover</a>
            </td>
        </tr>
    <?php } ?>                       
            </tbody>
            <?php
            $total = array_sum(array_column($contas, 'valor'));
            ?>
            <tfoot>
                <tr>
                 <td colspan="3">Total devido:</td>
                <td><?= number_format($total, 2, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
     </div>  
     </div>
    </div>
</div>





 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>   
</body>
</html>

