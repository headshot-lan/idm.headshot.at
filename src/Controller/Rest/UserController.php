<?php

namespace App\Controller\Rest;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use App\Service\LoginService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use Ramsey\Uuid\Uuid;
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
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, LoginService $loginService, UserRepository $userRepository)
    {
        $this->em = $entityManager;
        $this->loginService = $loginService;
        $this->userRepository = $userRepository;
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
     * @SWG\Tag(name="UserManagement")
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

            $this->em->persist($user);
            $this->em->flush();

            // send the confirmation Email
            //TODO: send the confirmation Email

            // return the User Object

            $user = $this->userRepository->findOneBy(['email' => $user->getEMail()]);

            $view = $this->view(['data' => $user]);
            $view->getContext()->setSerializeNull(true);
            $view->getContext()->addGroup('default');

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
            $view->getContext()->setSerializeNull(true);
            $view->getContext()->addGroup('default');
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
    public function getUserAction(string $search)
    {
        if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', $search)) {
            // UUID based Search

            $user = $this->userRepository->findOneBy(['uuid' => $search]);
        } elseif (preg_match("/\w+@\w+.\w+/", $search)) {
            // E-Mail based Search

            $user = $this->userRepository->findOneBy(['email' => $search]);
        } else {
            $view = $this->view('', Response::HTTP_BAD_REQUEST);

            return $this->handleView($view);
        }

        if ($user) {
            $view = $this->view(['data' => $user]);
            $view->getContext()->setSerializeNull(true);
            $view->getContext()->addGroup('default');
        } else {
            $view = $this->view(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * Edits a User.
     *
     * Edits a User
     * WARNING: for now it's mandatory to supply a full UserObject
     *
     * @Rest\Patch("/{uuid}", requirements= {"search"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"})
     * @ParamConverter()
     */
    public function editUserAction(User $user, Request $request)
    {
        $form = $this->createForm(UserEditType::class, $user);

        // Specify clearMissing on false to support partial editing
        $form->submit($request->request->all(), false);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->em->persist($user);
            $this->em->flush();

            $user = $this->userRepository->findOneBy(['uuid' => $user->getUuid()]);
            $view = $this->view(['data' => $user]);
            $view->getContext()->setSerializeNull(true);
            $view->getContext()->addGroup('default');

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

        if ('json' !== $request->getContentType()) {
            $view = $this->view(['message' => 'Invalid Content-Type'], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            return $this->handleView($view);
        }
        $content = $request->getContent();
        if (empty($content)) {
            $view = $this->view(['message' => 'No Body supplied, please check the Documentation'], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            return $this->handleView($view);
        }
        $decode = json_decode($content);
        if (empty($decode) || !is_object($decode) || empty($decode->uuid) || !is_array($decode->uuid)) {
            $view = $this->view(['message' => 'Invalid JSON Body supplied, please check the Documentation'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        }
        foreach ($decode->uuid as $item) {
            if (!is_string($item) || !Uuid::isValid($item)) {
                $view = $this->view(['message' => 'Invalid UUIDs supplied'], Response::HTTP_BAD_REQUEST);
                return $this->handleView($view);
            }
        }

        $user = $this->userRepository->findBy(['uuid' => $decode->uuid]);

        if ($user) {
            $view = $this->view(['data' => $user]);
            $view->getContext()->setSerializeNull(true);
            $view->getContext()->addGroup('default');
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
    public function getUsersAction()
    {
        // Select all Users where the Status is greater then 0 (e.g. not disabled/locked/deactivated)
        $criteria = new Criteria();
        $criteria->where($criteria->expr()->gt('status', 0));

        $user = $this->userRepository->matching($criteria);

        if ($user) {
            $view = $this->view(['data' => $user]);
            $view->getContext()->setSerializeNull(true);
            $view->getContext()->addGroup('default');
        } else {
            $view = $this->view(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }
}
