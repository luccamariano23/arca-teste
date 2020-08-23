<?php


namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Empresa;
use App\Repository\ConnBD;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class EmpresaController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function telaInicial()
    {
        return $this->render('telaInicial.html.twig');

    }

    /**
     * @Route("/resultado", methods={"POST"})
     */
    public function telaResultado()
    {
        if(empty($_POST['search'])) {
            echo "Campo de pesquisa não pode estar vazio!";
            exit;
        }

        $results = ConnBD::selectBySearchField($this->entityManager, $_POST['search']);

        if(sizeof($results)<=0){
            echo "Não há nenhuma empresa cadastrada para o critério de pesquisa utilizado!";
            exit();
        }

        return $this->render('telaListaEmpresas.html.twig',[
            'empresas'=>$results,
             'search'=>ucfirst($_POST['search'])
        ]);
    }

    /**
     * @Route("/resultado/{id}/", methods={"GET"})
     */
    public function exibeInfosEmpresa($id)
    {

        $results = ConnBD::selectById($this->entityManager,$id);

        $categorias = explode('&',$results[0]->getCategoria());

        return $this->render('telaInfosEmpresa.html.twig',[
                'empresa'=>$results[0],
                'categorias'=>$categorias
        ]);

    }

    /**
     * @Route("/login", methods={"GET"})
     */
    public function realizaLogin()
    {
        if(isset($_SESSION['logado']))
            return $this->redirect('/admin/empresas');

        return $this->render('telaLogin.html.twig');

    }

    /**
     * @Route("/login", methods={"POST"})
     */
    public function validaLogin()
    {

        $results = ConnBD::selectByAdmin($this->entityManager, $_POST['login'], $_POST['senha']);

        if(sizeof($results)<=0){
            echo "Login e/ou Senha inválidos";
            exit();
        }

        $_SESSION['logado'] = true;

        return $this->redirect("/admin/empresas");

    }

    /**
     * @Route("/logout", methods={"GET"})
     */
    public function realizaLogout()
    {
        if(!isset($_SESSION['logado']))
            return $this->redirect('/login');

        $_SESSION = NULL;

        return $this->redirect('/login');

    }

    /**
     * @Route("/admin/empresas", methods={"GET"})
     */
    public function listaTodasEmpreas()
    {
        if(!isset($_SESSION['logado']))
            return $this->redirect('/login');

        $results = ConnBD::selectAll($this->entityManager);

        return $this->render('telaListaTodasEmpresas.html.twig',[
            'empresas'=>$results
        ]);
    }

    /**
     * @Route("/admin/cadastro", methods={"GET"})
     */
    public function cadastraEmpresaGet()
    {
        if(!isset($_SESSION['logado']))
            return $this->redirect('/login');

        return $this->render('telaCadastroEmpresa.html.twig');

    }

    /**
     * @Route("/admin/cadastro", methods={"POST"})
     */
    public function cadastraEmpresaPost()
    {

        //Valida se os campos de texto foram preenchidos
        foreach ($_POST as $key => $value)
        {
            if (empty($value)) {
                echo "Campo '" . ucfirst($key) . "' não pode estar vazio!";
                exit();
            }
        }

        //valida se uma ou mais categorias foram selecionada
        if(empty($_POST['categoria'])){
            echo "Favor selecionar ao menos uma Categoria!";
            exit();
        }

        $categoria = implode('&',$_POST['categoria']);

        $empresa =  new Empresa();
        $empresa->setTitulo($_POST['titulo']);
        $empresa->setTelefone($_POST['telefone']);
        $empresa->setEndereco($_POST['endereco']);
        $empresa->setCep($_POST['cep']);
        $empresa->setCidade($_POST['cidade']);
        $empresa->setEstado($_POST['estado']);
        $empresa->setDescricao($_POST['descricao']);
        $empresa->setCategoria($categoria);

        $this->entityManager->persist($empresa);
        $this->entityManager->flush();

        echo "Empresa cadastrada com sucesso!";

        exit();

    }

}