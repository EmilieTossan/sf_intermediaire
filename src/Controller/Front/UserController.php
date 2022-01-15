<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @Route("create/user", name="user_create")
     */
    public function createUser(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ){
        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid()){
            $user->setRoles(["ROLE_USER"]);
            $user->setDate(new \DateTime("NOW"));
            // On récupère le mot de passe entré dans le formulaire
            $plainPassWord = $userForm->get('password')->getData();
            // On hashe le mot de passe pour le sécuriser
            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $plainPassWord);
            $user->setPassword($hashedPassword);
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('product_list');
        }
        return $this->render("front/userform.html.twig", ['userForm' => $userForm->createView()]);
    }

    /**
     * @Route("update/user/{id}", name="user_update")
     */
    public function updateUser(
        $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ){
        $user = $userRepository->find($id);
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid()){
            $user->setRoles(["ROLE_USER"]);
            $plainPassWord = $userForm->get('password')->getData();
            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $plainPassWord);
            $user->setPassword($hashedPassword);
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('product_list');
        }
        return $this->render("front/userform.html.twig", ['userForm' => $userForm->createView()]);
    }
}