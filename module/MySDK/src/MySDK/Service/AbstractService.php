<?php

namespace MySDK\Service;

use Zend\Stdlib\Hydrator;
use Symfony\Component\Console\Application;

abstract class AbstractService {

    /**
     * Service Manager
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected static $sm;

    /**
     * EntityManager
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Entity
     * @var \Application\Entity\AbstractEntity
     */
    protected $entity;

    public function identity() {
        $authService = self::getServiceManager()->get('AuthService');
        return $authService->getIdentity();
    }

    /**
     * Executa o insert de um array
     * auto-comit;
     * @param array $data
     * @return \Application\Entity\AbstractEntity|boolean
     */
    public function persist($entity) {
        if ($this->entity && $entity instanceof $this->entity) {
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
            return $entity;
        }
        return false;
    }

    /**
     * Executa o insert de um array
     * auto-comit;
     * @param array $data
     * @return \Application\Entity\AbstractEntity|boolean
     */
    public function insert(array $data) {
        if ($this->entity) {
            $entity = new $this->entity($data);
            return $this->persist($entity);
        }
        return false;
    }

    /**
     * Executa o update de um array
     * auto-comit;
     * @param array $data
     * @param int $id
     * @return \Application\Entity\AbstractEntity|boolean
     */
    public function update(array $data, $id) {
        if ($this->entity) {
            $entity = $this->getEntityManager()->getReference($this->entity, $id);
            (new Hydrator\ClassMethods())->hydrate($data, $entity);

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
            return $entity;
        }
        return false;
    }

    /**
     * Executa o delete
     * auto-comit;
     * @param int $id
     * @return int|boolean
     */
    public function delete($id) {
        if ($this->entity) {
            $entity = $this->getEntityManager()->getReference($this->entity, $id);
            if ($entity) {
                $this->getEntityManager()->remove($entity);
                $this->getEntityManager()->flush();
                return $id;
            }
        }
        return false;
    }

    /**
     * Metodo responsavel por capturar a service manager
     */
    public static function setServiceManager(\Zend\ServiceManager\ServiceManager $sm) {
        self::$sm = $sm;
    }

    public static function getServiceManager() {
        return self::$sm;
    }

    /**
     * Metodo responsavel por instanciar e retornar o entity manager do doctrine
     *
     * @return Doctrine\ORM\EntityManager
     */
    protected function getEntityManager() {

        if (null == $this->em) {
            $this->em = self::getServiceManager()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }

    /**
     * Metodo para retornar o repositorio
     */
    public function getRepository() {
        if ($this->entity) {
            return $this->getEntityManager()->getRepository($this->entity);
        }
    }

    public function flashMessenger() {
        $controller = self::getServiceManager()->get('controllerpluginmanager');
        return $controller->get('flashmessenger');
    }

    /*
     * Metodos de transacao
     */

    public function beginTransaction() {
        //suspend auto-commit
        return $this->getEntityManager()->getConnection()->beginTransaction();
    }

    public function commit() {
        return $this->getEntityManager()->getConnection()->commit();
    }

    public function rollback() {
        return $this->getEntityManager()->getConnection()->rollback();
    }

    public function close() {
        return $this->getEntityManager()->close();
    }

    /*
     * Metodos para consulta
     */

    public function find($id) {
        return $this->getRepository()->find($id);
    }

    public function findAll() {
        return $this->getRepository()->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria) {
        return $this->getRepository()->findOneBy($criteria);
    }

}
