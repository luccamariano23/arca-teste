<?php


namespace App\Repository;


use App\Entity\Empresa;
use PhpParser\Node\Expr\Array_;

class ConnBD
{
    public static function selectBySearchField($entityManager, $value)
    {
        $query = $entityManager->createQuery('SELECT e FROM App\Entity\Empresa e WHERE 
        e.titulo =:titulo OR 
        e.endereco =:endereco OR 
        e.cep =:cep OR 
        e.cidade =:cidade OR 
        e.categoria LIKE :categoria');

        $query->setParameters(array(
            ':titulo'=>$value,
            ':endereco'=>$value,
            ':cep'=>$value,
            ':cidade'=>$value,
            ':categoria'=>'%'.$value.'%'
        ));

       return $query->getResult();
    }

    public static function selectById($entityManager, $id)
    {
        $query = $entityManager->createQuery('SELECT e FROM App\Entity\Empresa e WHERE e.id = :id');

        $query->setParameters(array(
            ':id'=>$id
        ));

        return $query->getResult();
    }

    public static function selectByAdmin($entityManager, $login, $senha)
    {
        $query = $entityManager->createQuery('SELECT a FROM App\Entity\Admin a WHERE 
        a.login = :login AND 
        a.senha = :senha');

        $query->setParameters(array(
            ':login'=>$login,
            ':senha'=>$senha
        ));

        return $query->getResult();
    }

    public static function selectAll($entityManager)
    {
        $query = $entityManager->createQuery('SELECT e FROM App\Entity\Empresa e');

        return $query->getResult();
    }
}