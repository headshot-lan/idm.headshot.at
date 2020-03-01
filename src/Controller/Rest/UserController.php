<?php

namespace App\Controller\Rest;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Service\LoginService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController.
 *
 * @Prefix("/users")
 * @NamePrefix("rest_users_")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var LoginService
     */
    private $loginService;

    public function __construct(EntityManagerInterface $entityManager, LoginService $loginService)
    {
        $this->em = $entityManager;
        $this->loginService = $loginService;
    }

    /**
     * Registers the User.
     *
     * @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Returned on a missing request parameter"
     *     ),
     * @SWG\Response(
     *         response="500",
     *         description="Returned on any other error"
     *     ),
     * @SWG\Parameter(
     *        name="JSON update body",
     *        in="body",
     *        description="json login request object",
     *        required=true,
     *        @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *          )
     *        )
     *      )
     * @SWG\Tag(name="Authorization")
     *
     * @Rest\Post("/register")
     */
    public function postRegisterAction(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            // get Data from Form
            $user = $form->getData();

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // set defaults in User
            $user->setStatus(1);
            $user->setEmailConfirmed(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            // send the confirmation Email
            //TODO: send the confirmation Email

            // return the User Object

            $query = $this->em->createQuery("SELECT u.id,u.email,u.status,u.firstname,u.emailConfirmed,
                                             u.nickname,u.roles,u.isSuperadmin,u.uuid
                                             FROM \App\Entity\User u WHERE u.email = :email");

            $query->setParameter('email', $user->getEmail());
            $user = $query->getOneOrNullResult();
            $view = $this->view(['data' => $user]);

            return $this->handleView($view);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Checks if the User is allowed to Login.
     *
     * Checks Username/Password against the Database
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns UserObject"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Returns if no EMail and/or Password could be found"
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     type="string",
     *     description="EMail"
     * )
     * * @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     type="string",
     *     description="Plaintext Password"
     * )
     * @SWG\Tag(name="Authorization")
     *
     * @Rest\Post("/authorize")
     */
    public function postAuthorizeAction(Request $request)
    {
        //Check if User can login
        $credentials = ['email' => $request->get('email'), 'password' => $request->get('password')];
        $user = $this->loginService->checkCredentials($credentials);

        if ($user) {
            $view = $this->view(['data' => $user]);
        } else {
            $view = $this->view(['message' => 'EMail and/or Password not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * Returns a Single Userobject.
     *
     * Supports searching via UUID and via E-Mail
     *
     * @Rest\Get("/{search}", requirements= {"search"="([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})|(\w+@\w+.\w+)"})
     */
    public function getUserAction(string $search, Request $request)
    {
        if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', $search)) {
            // UUID based Search
            $query = $this->em->createQuery("SELECT u.email,u.status,u.firstname, u.surname, u.postcode, u.city,
                                             u.street, u.country, u.phone, u.gender, u.emailConfirmed,
                                             u.nickname, u.isSuperadmin, u.uuid, u.id,
                                             u.infoMails, u.website, u.steamAccount, u.registeredAt,
                                             u.modifiedAt, u.hardware, u.favoriteGuns, u.statements
                                             FROM \App\Entity\User u WHERE u.uuid = :search");
        } elseif (preg_match("/\w+@\w+.\w+/", $search)) {
            // E-Mail based Search
            $query = $this->em->createQuery("SELECT u.email,u.status,u.firstname, u.surname, u.postcode, u.city,
                                             u.street, u.country, u.phone, u.gender, u.emailConfirmed,
                                             u.nickname, u.isSuperadmin, u.uuid, u.id,
                                             u.infoMails, u.website, u.steamAccount, u.registeredAt,
                                             u.modifiedAt, u.hardware, u.favoriteGuns, u.statements
                                             FROM \App\Entity\User u WHERE u.email = :search");
        } else {
            $view = $this->view('', Response::HTTP_BAD_REQUEST);

            return $this->handleView($view);
        }
        $query->setParameter('search', $search);
        $user = $query->getResult();

        if ($user) {
            $view = $this->view(['data' => $user]);
        } else {
            $view = $this->view(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * Edits a User.
     *
     * Edits a User
     *
     * @Rest\Patch("/{uuid}", requirements= {"search"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"})
     * @ParamConverter()
     */
    public function editUserAction(User $user, Request $request)
    {

        // TODO: add support for updating specific Fields only
        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $query = $this->em->createQuery("SELECT u.email,u.status,u.firstname, u.surname, u.postcode, u.city,
                                             u.street, u.country, u.phone, u.gender, u.emailConfirmed,
                                             u.nickname, u.isSuperadmin, u.uuid, u.id,
                                             u.infoMails, u.website, u.steamAccount, u.registeredAt,
                                             u.modifiedAt, u.hardware, u.favoriteGuns, u.statements
                                             FROM \App\Entity\User u WHERE u.uuid = :uuid");
            $query->setParameter('uuid', $user->getUuid());

            $user = $query->getResult();
            $view = $this->view(['data' => $user]);

            return $this->handleView($view);
        } else {
            $view = $this->view(['message' => 'User not found'], Response::HTTP_NOT_FOUND);

            return $this->handleView($view);
        }
    }

    /**
     * Returns multiple Userobjects.
     *
     * Supports searching via UUID
     *
     * @Rest\Post("/search")
     */
    public function postUsersearchAction(Request $request)
    {
        // UUID based Search
        $query = $this->em->createQueryBuilder();
        $query->select('
                u.email,u.status,u.firstname, u.surname, u.postcode, u.city,
                u.street, u.country, u.phone, u.gender, u.emailConfirmed,
                u.nickname, u.isSuperadmin, u.uuid, u.id,
                u.infoMails, u.website, u.steamAccount, u.registeredAt,
                u.modifiedAt, u.hardware, u.favoriteGuns, u.statements
            ');
        $query->from('App\Entity\User', 'u');
        $i = 0;
        foreach (json_decode($request->getContent(), true)['uuid'] as $uuid) {
            dump($uuid);
            ++$i;
            if (1 == $i) {
                $query->where($query->expr()->eq('u.uuid', ":uuid{$i}"));
            } else {
                $query->orWhere($query->expr()->eq('u.uuid', ":uuid{$i}"));
            }
            $query->setParameter("uuid{$i}", $uuid);
        }
        $user = $query->getQuery()->getResult();

        if ($user) {
            $view = $this->view(['data' => $user]);
        } else {
            $view = $this->view(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * Returns all Userobjects.
     *
     * @Rest\Get("")
     */
    public function getUsersAction(Request $request)
    {
        $query = $this->em->createQuery("SELECT u.email,u.status,u.firstname, u.surname, u.postcode, u.city,
                                             u.street, u.country, u.phone, u.gender, u.emailConfirmed,
                                             u.nickname, u.isSuperadmin, u.uuid, u.id,
                                             u.infoMails, u.website, u.steamAccount, u.registeredAt,
                                             u.modifiedAt, u.hardware, u.favoriteGuns, u.statements
                                             FROM \App\Entity\User u WHERE u.status > 0");

        $user = $query->getResult();

        if ($user) {
            $view = $this->view(['data' => $user]);
        } else {
            $view = $this->view(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }
}
