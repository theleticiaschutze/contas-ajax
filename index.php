<!-- a parte do json fica dentro do index agora e retorna php não ajax/js -->
<?php    
$dados = 'contas.json'; //arquivo json onde os dados ficam
$contas = json_decode(file_get_contents($dados), true) ?? []; //decodifica para formato que array q o php usa

$acao = $_POST['acao'] ?? $_GET['acao'] ?? ''; //pode usar get ou post para acao, ou vazio

if ($acao == 'inserir') {  //se acao for inserir  
        $adicionarId = empty($contas) ? 1 : max(array_keys($contas)) + 1; //se for vazio começa de 1, se tiver id adiciona mais 1
        $contas[$adicionarId] = [ //id é chave do objeto    
            "codigo"     => $_POST['codigo'],
            "favorecido" => $_POST['favorecido'],
            "vencimento" => $_POST['vencimento'],
            "valor"      => str_replace(',', '.', $_POST['valor'])
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

$contaModificar = null; //garante a variavel vazia enquanto não está sendo usada
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Contas a pagar</title>
</head>
<body>

    <div class="container mt-5" style="max-width: 1100px">
        <h1 class="my-4 text-center"> Registro de contas a pagar</h1>

        <!-- Mostra o alerta de sucesso da operacao abaixo do titulo, pode ser clicado para fechar, usei echo para comprar com sempre envolver o php em < ? -->
        <?php 
        $status = $_GET['status'] ?? ''; 
        if ($status === 'sucesso') { 
            echo '<div class="alert alert-success alert-dismissible fade show">';
            echo    'Conta foi registrada!!';
            echo    '<button type="button" class="btn-close" onclick="this.parentElement.classList.add(\'invisible\')"></button>';
            echo'</div>';
        } elseif ($status === 'atualizado') { 
            echo'<div class="alert alert-info alert-dismissible fade show">';
            echo    'Conta foi atualizada!!!';
            echo    '<button type="button" class="btn-close" onclick="this.parentElement.classList.add(\'invisible\')"></button>';
            echo'</div>';
        } elseif ($status === 'removido') { 
            echo'<div class="alert alert-warning alert-dismissible fade show">';
            echo    'Conta foi removida.';
            echo    '<button type="button" class="btn-close" onclick="this.parentElement.classList.add(\'invisible\')"></button>';
            echo'</div>';
        //} elseif ($status === 'erro_duplicado') { //esse se colocou duplicado
        //    echo '<div class="alert alert-warning alert-dismissible fade show">';
        //    echo    'Erro: Este código já existe, insira um código diferente.';
        //    echo    '<button type="button" class="btn-close" onclick="this.parentElement.classList.add(\'invisible\')"></button>';
        //    echo '</div>';
        } else {  // alerta nvisivel para tela não ficar mexendo, com a classe 'invisible'
            echo '<div class="alert alert-dismissible invisible">';
            echo    '&nbsp;'; 
            echo    '<button type="button" class="btn-close"></button>';
            echo '</div>';
        }
        ?>
    <div class="row">
        <div class="col-lg-4 mb-4">
        <!-- Formulário 4 de 12 grids -->
            <form method="post" action="index.php">
                <input type="hidden" name="acao" value="<?= $contaModificar ? 'atualizar' : 'inserir' ?>">
                <input type="hidden" name="id" value="<?= $contaModificar['_id'] ?? '' ?>">           
                <div class="card shadow-sm p-3">             
                    <div class="mb-3">
                        <label for="codigo">Código:</label>
                        <input type="text" name="codigo" id="codigo" class="form-control" required autofocus
                            value="<?= $contaModificar['codigo'] ?? '' ?>">
                            <div class="invalid-feedback">
                                Código já cadastrado!
                            </div>
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

        <!-- tabela 8 de 12 grids -->
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
                        <?php foreach ($contas as $id => $conta){ // vai preencher as linhas com info usando loop ?>
                        <tr>
                            <td><?= $conta['codigo'] ?></td>
                            <td><?= $conta['favorecido'] ?></td>
                            <td><?= date('d/m/Y', strtotime($conta['vencimento'])) ?></td>
                            <td>R$ <?= number_format($conta['valor'], 2, ',', '.') //formatação do valor para exibição no modo usado no brasil ?></td>
                            <td>
                                <a href="?acao=modificar&id=<?= $id ?>" class="btn btn-sm btn-outline-warning" title="Modificar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger" title="Remover"
                                onclick="confirmarRemover('?acao=remover&id=<?= $id ?>')">
                                <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>                       
                    </tbody>
                    <?php
                    $total = array_sum(array_column($contas, 'valor')); //usamos os valores da coluna valor com sum para achar o total
                    ?>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total devido:</td>
                            <td>R$ <?= number_format($total, 2, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>  
        </div>
    </div>

    <!-- usaremos um modal do bootstrap para o remover ficar mais bonito, 
     lembra que usamos modal naquele primeiro trabalho!! -->
    <div class="modal fade" id="modalRemover" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirme a remoção</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            Tem certeza que deseja remover a conta?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
            <a id="btnConfirmarRemover" href="#" class="btn btn-danger">Sim</a>
        </div>
    </div>
</div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>   
    <script>
        //aqui vamos fazer checagem de código duplicado para cadastro código JS, dependemos do bootstrap para invalid-feedback e etc
        document.querySelector('form').addEventListener('submit', function(e) {
            const acao = document.querySelector('input[name="acao"]').value;

            if (acao == 'atualizar') {
                return;
            }

            const codigoDigitado = document.getElementById('codigo').value;
            const codigosNaTabela = [...document.querySelectorAll('tbody td:first-child')]
                .map(td => td.textContent.trim());

            if (codigosNaTabela.includes(codigoDigitado)) {
                e.preventDefault();
                document.getElementById('codigo').classList.add('is-invalid');
            }
        });

        //tirar erro ao digitar novamente
        document.getElementById('codigo').addEventListener('input', function(){
            this.classList.remove('is-invalid');
        })
          
        //remover codigo js para o modal, oa invés do alert, acho que é bem opcional, mas fica mais obnito
        function confirmarRemover(url) {
        document.getElementById('btnConfirmarRemover').href = url;
        new bootstrap.Modal(document.getElementById('modalRemover')).show();
        }

        //remove status ex sucesso para o próximo refresh não repetir a mensagem
        if (window.location.search.includes('status=')) {
            const url = new URL(window.location.href);
             url.searchParams.delete('status');
            window.history.replaceState({}, '', url);
        }   

        </script>
    
</body>
</html>