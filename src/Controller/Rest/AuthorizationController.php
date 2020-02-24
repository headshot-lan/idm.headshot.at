<?php

namespace App\Controller\Rest;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;


use App\Entity\User;
use App\Service\LoginService;

/**
 * Class AuthorizationController
 * @package App\Controller\Rest
 * @Prefix("/authorize")
 * @NamePrefix("rest_authorize_")
 */
class AuthorizationController extends AbstractFOSRestController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoginService
     */
    private $loginService;

    function __construct(EntityManagerInterface $entityManager, LoginService $loginService)
    {
        $this->em = $entityManager;
        $this->loginService = $loginService;
    }

    /**
     * Checks if the User is allowed to Login
     *
     * Checks Username/Password against the Database
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns UserObject",
     * @Model(type=User::class)
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     type="string",
     *     description="EMail"
     * )
     * * @SWG\Parameter(
     *     name="passwordhash",
     *     in="formData",
     *     type="string",
     *     description="Passwordhash"
     * )
     * @SWG\Tag(name="Authorization")
     *
     * @Rest\Post("/check")
     */
    public function postCheckAction(Request $request)
    {
        //Check if User can login
        $credentials = [ 'email' => $request->get('email'), 'passwordhash' => $request->get('passwordhash') ];
        $user = $this->loginService->checkCredentials($credentials);

        if($user) {
            $view = $this->view(array('data' => $user));
            return $this->handleView($view);
        } else {
            return $this->createNotFoundException();
        }
    }

}
