<?php
/**
 * Created by PhpStorm.
 * User: kseni
 * Date: 12.06.2020
 * Time: 14:17
 */

namespace App\Controller;


use App\Entity\User;
use App\Entity\Organization;
use App\Form\SignupForm;
use App\Helper\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        return $this->render('main/home.html.twig');
    }

    /**
     * @Route("/signup", name="signup")
     */
    public function signup(Request $request, UserPasswordEncoderInterface $encoder)
    {
        if ($this->isGranted('ROLE_USER'))
            return $this->redirectToRoute('profile');

        $userRepository = $this->getDoctrine()
            ->getRepository(User::class);

        // Список пользователей для поля "Кто пригласил"
        $users = $userRepository->getUserList();

        if ($request->isMethod('POST')) {

            // Данные с формы
            $userArray = $request->request->get('user');

            $form = new SignupForm($userArray);
            if ($form->validate()) {
                $foundUser = $userRepository->findOneBy(['phone' => $form->phone]);
                if ($foundUser) {
                    $this->addFlash('error', 'Пользователь с таким номером телефона уже зарегистрирован.');
                } else {
                    $entityManager = $this->getDoctrine()->getManager();

                    // Проверяем, есть ли организация в БД
                    $org = $this->getDoctrine()
                        ->getRepository(Organization::class)
                        ->findOneByName($form->org);

                    $entityManager->getConnection()->beginTransaction();
                    try {
                        // Если такой организации еще нет, добавляем ее
                        if (!$org) {
                            $org = new Organization();
                            $org->setName($form->org);
                            $entityManager->persist($org);
                            $entityManager->flush();
                        }

                        // Добавляем пользователя
                        $user = new User();
                        $user->setFirstName($form->first_name)
                            ->setLastName($form->last_name)
                            ->setPhone($form->phone)
                            ->setIdUser($form->id_user) // TODO: проверить, что пользователь с таким id существует
                            ->setOrganization($org)
                            ->setPassword($encoder->encodePassword($user, $form->password));

                        $entityManager->persist($user);
                        $entityManager->flush();
                        $entityManager->getConnection()->commit();
                        $this->addFlash('success', 'Вы успешно зарегистрированы!');
                        return $this->redirectToRoute('app_homepage');
                    } catch (\Exception $ex) {
                        $entityManager->getConnection()->rollBack();
                        $this->addFlash('error', 'Ошибка. Попробуйте позже.');
                    }
                }

            } else {
                $this->addFlash('error', 'Поля формы заполнены неверно.');
            }
        }
        return $this->render('main/signup.html.twig', [
            'userArray' => $userArray ?? [],
            'userList' => $users
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        $currentUser = $this->getUser();
        // Список пользователей для поля "Кто пригласил"
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->getUserList($currentUser->getId());

        return $this->render('main/profile.html.twig', [
            'userList' => $users
        ]);
    }

    /**
     * @Route("/profile-save", name="profile-save")
     */
    public function profileSave(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Ошибка'
            ], 400);
        }

        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('first_name');
            $lastName = $request->request->get('last_name');
            $phone = $request->request->get('phone');
            $newOrg = $request->request->get('org');
            $newIdUser = $request->request->get('id_user');
            if (!Validator::test('simpleString', $firstName) || !Validator::test('simpleString', $lastName)) {
                return new JsonResponse([
                    'status' => 'error',
                    'errorMsg' => 'Неправильно заполнены Имя / Фамилия'
                ]);
            }
            if (!Validator::test('simplePhone', $phone) || !Validator::test('simpleString', $lastName)) {
                return new JsonResponse([
                    'status' => 'error',
                    'errorMsg' => 'Неправильно заполнено поле Телефон'
                ]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();
            try {
                $currentUser = $this->getUser();

                // Если организация поменялась, проверяем, если она в базе.
                // Если нет, добавляем
                if ($newOrg !== $currentUser->getOrgName()) {
                    $org = $this->getDoctrine()
                        ->getRepository(Organization::class)
                        ->findOneByName($newOrg);

                    if (!$org) {
                        $org = new Organization();
                        $org->setName($newOrg);
                        $entityManager->persist($org);
                        $entityManager->flush();
                    }
                    $currentUser->setOrganization($org);
                }

                if ($newIdUser != $currentUser->getIdUser()) {
                    // TODO: проверить, что пользователь с таким id существует
                    $currentUser->setIdUser($newIdUser);
                }

                $currentUser->setFirstName($firstName);
                $currentUser->setLastName($lastName);
                // TODO: проверить, что телефон уникальный
                $currentUser->setPhone($phone);

                $entityManager->persist($currentUser);
                $entityManager->flush();
                $entityManager->getConnection()->commit();

                return new JsonResponse([
                    'status' => 'ok',
                    'data' => $firstName
                ]);
            } catch (Exception $ex) {
                $entityManager->getConnection()->rollBack();
                return new JsonResponse([
                    'status' => 'error',
                    'errorMsg' => 'При сохранении данных возникла ошибка.'
                ]);
            }
        }
    }

    /**
     * @Route("/get-organizations")
     */
    public function getOrgs(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Ошибка'
            ], 400);
        }

        $orgs = $this->getDoctrine()
            ->getRepository(Organization::class)
            ->getOrgArray();

        if ($request->isMethod('POST')) {
            return new JsonResponse([
                'status' => 'ok',
                'orgs' => $orgs
            ]);
        }
    }
}