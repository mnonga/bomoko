<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use UserBundle\Entity\User;
use UserBundle\Entity\ActivationCode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserController extends Controller
{
    /**
     * @Route("/inscription", name="user_signin")
     */
    public function inscriptionAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('UserBundle\Form\UserType', $user);
        //$form->setAction($this->generateUrl('user_login_check'));
        //$form->setMethod('POST');

        $session = $request->getSession();

        $deny_licence = NULL;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('accept_licence')->getData() == TRUE) {//conditions accepté, plus necessaire
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $user->setSalt(base64_encode($user->getPassword()));
                    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                    $user->setPassword($password);
                    $user->setName(trim($user->getName()));
                    $user->setRoles(array('ROLE_BUSINESSMAN'));

                    $activation = new ActivationCode();
                    $activation->setUserid($user);
                    $activation->setCode(uniqid());

                    $em = $this->getDoctrine()->getManager();

                    $em->getConnection()->beginTransaction();

                    $em->persist($user);
                    $em->flush();

                    try {
                        $em->persist($activation);
                        $em->flush();
                        //$em->getConnection()->commit();
                    } catch (Exception $e) {
                        $em->getConnection()->rollback();
                        throw $e;
                    }

                    $em->refresh($activation);

                    try {
                        $url = $this->generateUrl('user_activation', array('code' => $activation->getCode()), true);
                        // Récupération du service
                        $mailer = $this->get('mailer');
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Lien d\'activation du compte Bomoko')
                            ->setFrom('bomoko.app@gmail.com')
                            ->setTo($user->getEmail())
                            ->setBody('Cliquez sur ce lien pour activer votre compte Bomoko : ' . $url);
                            $mailer->send($message);
                        $em->getConnection()->commit();
                    } catch (Exception $e) {
                        $em->getConnection()->rollback();
                    }


                    /*
                                    $file = $user->getPhoto();
                                    //$filename=md5(uniqid()).'.'.$file->guessExtension();
                                    $filename=$user->getUserid().'.'.$file->guessExtension();
                                    try {
                                        $dir=$this->getParameter('user_profiles_dir');
                                        $file->move($dir, $filename);
                                    } catch (Exception $e) {

                                    }
                                    $user->setPhoto($filename);
                                    $em->flush();
                    */
                    //Auhtenticate the user after registration
                    /*$token=new UsernamePasswordtoken($user,null,'main',$user->getRoles());
                    $this->get('security.token_storage')->setToken($token);
                    $this->get('session')->set('_security_main', serialize($token));*/

                    //return $this->redirect($this->generateUrl('user_profile'));
                    return $this->redirect($this->generateUrl('user_signin_success'));
                } else {//fin conditions accepté
                    $deny_licence = TRUE;
                }

            }//fin form valid
        }//fin POST method
        return $this->render('@User/User/inscription.html.twig',
            array(
                'form' => $form->createView(),
                'deny_licence' => $deny_licence
            ));
    }


    /**
     * @Route("/inscription/success", name="user_signin_success")
     */
    public function inscriptionSuccessAction(Request $request)
    {
        return $this->render('@User/User/inscription_success.html.twig', array());
    }


    /**
     * @Route("/connexion", name="user_login")
     */
    public function connexionAction(AuthenticationUtils $authenticationUtils)
    {
        //$authenticationUtils=$this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        //'@User/Default/index.html.twig'
        return $this->render('@User/User/connexion.html.twig', array(
            'token' => uniqid(),
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }


    /**
     * @Route("/admina", name="administration")
     */
    public function connexion_checkAction()
    {
        return $this->render('@User/User/connexion.html.twig', array());
    }


    /**
     * @Route("/profile", name="user_profile")
     */
    public function profileAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->refresh($user);
        //$repository = $this->getDoctrine()->getRepository(Pub::class);
        $qb = $em->createQueryBuilder();
        $qb->select('count(pub.id)')->from('UserBundle:Pub', 'pub')
            ->join('pub.user', 'user')
            ->where($qb->expr()->eq('user.userid', $user->getUserid()));
        $pub_count = $qb->getQuery()->getSingleScalarResult();
        return $this->render('@User/User/profile.html.twig', array(
            'user' => $user,
            'pub_count' => $pub_count
        ));
    }

    /**
     * @Route("/activation/{code}", name="user_activation")
     * @ParamConverter("code", options={"mapping"={"code"="code"}})
     */
    public function activationAction(Request $request, ActivationCode $code)
    {
        $em = $this->getDoctrine()->getManager();
        $code->getUserid()->setEtat('NORMAL');
        $em->remove($code);
        $em->flush();
        return $this->redirect($this->generateUrl('user_activation_success', array('email' => $code->getUserid()->getEmail())));
    }

    /**
     * @Route("/activation/success/{email}", name="user_activation_success",)
     * @ParamConverter("user", options={"mapping"={"email"="email"}})
     */
    public function activationSuccessAction(Request $request, User $user)
    {
        return $this->render('@User/User/activation.html.twig', array(
            "user" => $user
        ));
    }


    /**
     * @Route("/admin", name="user_admin")
     */
    public function adminAction()
    {
        return $this->render('@User/User/activation.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/admin/connexion", name="admin_login")
     */
    public function adminConnexionAction()
    {
        //'@User/Default/index.html.twig'
        return $this->render('@User/User/connexion.html.twig', array(
            'token' => uniqid()
        ));
    }

    /**
     * @Route("/condition", name="user_condition")
     */
    public function conditionUtilisationAction()
    {
        //'@User/Default/index.html.twig'
        return $this->render('@User/User/conditions.html.twig', array(
            'token' => uniqid()
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/photo_profile", name="user_profile_change")
     */
    public function changeProfilePhotoAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->refresh($user);

        $builder = $this->createFormBuilder();
        $builder
            ->add('photo', FileType::class,
                array('label' => 'Choisir votre photo de profile', 'required' => true, 'mapped' => false, 'multiple' => false,
                    'attr' => array('class' => 'form-control'),
                    'constraints' => array(
                        new Assert\NotBlank(['message' => "Veuillez choisir une photo de profile !"]),
                        new Assert\Image(["maxSize" => "3072Ki",
                            "mimeTypesMessage" => "Le fichier que vous avez envoyé n'est pas un fichier image valid !",
                            "maxSizeMessage" => "L'image ne doit pas peser plus de Mo !",
                            "uploadErrorMessage" => "Echec de l'envoi de l'image !",
                            "corruptedMessage" => "Image corrompu !"])
                    )
                ));
        $form = $builder->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $file = $form->get('photo')->getData();
                $manager = new \Intervention\Image\ImageManager(["driver" => "gd"]);
                $img = $manager->make($file->getRealPath());
                //$img->resize(200,200);
                //resize the image to widht of 100, key aspect ratio and prevent upsizing
                $img->resize(200, 200, function ($contraint) {
                    $contraint->aspectRatio();
                    $contraint->upsize();
                });
                // $img->widen(200, function($contraint){
                //     $contraint->upsize();
                // });
                //$filename=md5(uniqid()).'.'.$file->guessExtension();
                $filename = $user->getUserid() . '.' . $file->guessExtension();
                $dir = $this->getParameter('user_profiles_dir');
                $changed = false;
                try {
                    //Supprimer l'ancienne photo
                    if ($user->getPhoto() !== null) {
                        $oldPhoto = $dir . '/' . $user->getPhoto();
                        unlink($oldPhoto);
                    }
                    //$file->move($dir, $filename);
                    $img->save($dir . '/' . $filename);
                    $changed = true;
                } catch (Exception $e) {

                }
                if ($changed) {
                    $user->setPhoto($filename);
                    $em->flush();
                }
                return $this->redirectToRoute('user_profile');
            }
        }
        return $this->render('@User/User/changephoto.html.twig', array(
            'form' => $form->createView()
        ));
    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/delete_account", name="user_delete")
     */
    public function deleteUserAction(Request $request)
    {
        //Un formulaire avec mot de passe et boutton valider comme dans la suppresion d'une Pub
    }

    /**
     * Modifier le nom, sexe, adresse et date de naissance de l'user
     * @Security("has_role('ROLE_USER')")
     * @Route("/modify_account", name="user_modify")
     */
    public function modifyAction(Request $request)
    {
        /**@var $user User */
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $em->refresh($user);

        $modifyUser = new User();
        $modifyUser->setAdresse($user->getAdresse());
        $modifyUser->setSexe($user->getSexe());
        $modifyUser->setDatenaissance($user->getDatenaissance());
        $modifyUser->setName($user->getName());

        $builder = $this->createFormBuilder($modifyUser);
        $builder
            ->add('name', TextType::class, array('label' => 'Nom complet', 'required' => true))
            ->add('datenaissance', BirthdayType::class, array('label' => 'Date de naissance', 'required' => true))
            ->add('adresse', TextType::class, array('label' => 'Adresse', 'required' => true))
            ->add('sexe', ChoiceType::class, array('label' => 'Sexe', 'required' => true, 'choices' => array('Masculin' => 'M', 'Feminin' => 'F'), 'expanded' => true, 'multiple' => false));
        $form = $builder->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user->setName($modifyUser->getName());
                $user->setSexe($user->getSexe());
                $user->setDatenaissance($modifyUser->getDatenaissance());
                $user->setAdresse($modifyUser->getAdresse());
                try {
                    $em->flush();
                    return $this->redirectToRoute('user_profile');
                } catch (\Exception $e) {
                    $request->getSession()->getFlashBag()->add('user_modify_message', 'Echec de la modification !');
                }
            }
        }
        return $this->render('@User/User/modify.html.twig', array(
            'form' => $form->createView()
        ));
    }


    /**
     * Mot de passe oublié, formulaire avec email et boutton soumettre la demande de reinitialisation
     * Puis on envoi un lien par email
     * Une fois le lien ouvert, on entre son email(authentication) et un nouveau mot de passe
     * Security("has_role('ROLE_USER')")
     * @Route("/password_forgotten/{done}", name="user_password_forgotten", requirements={"done"="\d+"})
     */
    public function passwordForgottenAction(Request $request, $done = 0)
    {
        if ($done) {//Message de succès
            return $this->render('@User/User/password_forgotten.html.twig');
        }
        //Afficher le formulaire
        $builder = $this->createFormBuilder();
        $builder->add('email', EmailType::class, array('label' => 'Email du compte', 'required' => true));
        $form = $builder->getForm();
        $error_message = null;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $email = $form->get('email')->getData();
                $em = $this->getDoctrine()->getManager();
                /**@var $user User */
                $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('email' => $email));
                if (!isset($user)) {
                    $error_message = "Compte non trouvé !";
                } else {
                    $activation = new ActivationCode();
                    $activation->setUserid($user);
                    $activation->setCode(uniqid());
                    $em->getConnection()->beginTransaction();
                    try {
                        //echec en cas d'une demande de reinitialisation existante ou compte non activé
                        $activation = $em->merge($activation);
                        $em->flush();
                        try {
                            $url = $this->generateUrl('user_password_reset', array('code' => $activation->getCode()), true);
                            // Récupération du service
                            $mailer = $this->get('mailer');
                            $message = \Swift_Message::newInstance()
                                ->setSubject("Lien de réinitialisation du mot de passe de votre compte Bomoko")
                                ->setFrom('bomoko@gmail.com')
                                ->setTo($user->getEmail())
                                ->setBody('Cliquez sur ce lien pour réinitialiser le mot de passe de votre compte Bomoko : ' . $url);
//                    $mailer->send($message);
                            $em->getConnection()->commit();
                            return $this->redirectToRoute('user_password_forgotten', ['done' => 1]);
                        } catch (Exception $e) {
                            $em->getConnection()->rollback();
                            $error_message = "Echec de l'opération !";
                        }
                    } catch (\Exception $e) {
                        $error_message = "Echec de l'opération !";
                        $this->get('logger')->info($e->getMessage());
                    }
                }
            }
        }
        return $this->render('@User/User/password_forgotten.html.twig', array(
            'form' => $form->createView(),
            'error_message' => $error_message
        ));
    }


    /**
     * Mot de passe oublié, formulaire avec email et nouveau password et confirmation puis boutton valider
     * Security("has_role('ROLE_USER')")
     * @Route("/password_reset/{code}", name="user_password_reset")
     */
    public function passwordResetAction(Request $request, $code = 0)
    {
        if (!$code) {//Message de succès
            return $this->render('@User/User/password_reset.html.twig');
        }
        //Afficher le formulaire
        $builder = $this->createFormBuilder();
        $builder->add('email', EmailType::class, array('label' => 'Email du compte', 'required' => true));
        $builder->add('password', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'Veuillez bien confirmer votre mot de passe !',
            'required' => true,
            'first_options' => array('label' => 'Nouveau mot de passe'),
            'second_options' => array('label' => 'Confirmation mot de passe'),
        ));
        $form = $builder->getForm();
        $error_message = null;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $email = $form->get('email')->getData();
                $password=$form->get('password')->getData();
                $em = $this->getDoctrine()->getManager();
                /**@var $user User */
                //$user = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('email' => $email));
                /**@var $activation ActivationCode */
                $activation=$this->getDoctrine()->getRepository(ActivationCode::class)->findOneBy(array('code' => $code));
                if (!isset($activation)) {
                    $error_message = "Lien de réinitialisation invalid";
                }else if($email!=$activation->getUserid()->getEmail()){
                    $error_message = "Email invalid pour ce lien de réinitialisation";
                }
                else {
                    $user=$activation->getUserid();
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $user->setSalt(base64_encode($user->getPassword()));
                    $password = $encoder->encodePassword($password, $user->getSalt());
                    $user->setPassword($password);

                    //$em->getConnection()->beginTransaction();
                    try {
                        //echec en cas d'une demande de reinitialisation existante ou compte non activé
                        $user=$em->merge($user);
                        $em->remove($activation);
                        $em->flush();
                        //$em->getConnection()->commit();
                        return $this->redirectToRoute('user_password_reset');
                    } catch (\Exception $e) {
                        //$em->getConnection()->rollback();
                        $error_message = "Echec de l'opération !";
                        $this->get('logger')->info($e->getMessage());
                    }
                }
            }
        }
        return $this->render('@User/User/password_reset.html.twig', array(
            'form' => $form->createView(),
            'error_message' => $error_message,
            'code'=>$code
        ));
    }


    /**
     * Changer son mot de passe, formulaire ancien password, new and confirmation, boutton valider
     * @Security("has_role('ROLE_USER')")
     * @Route("/password_change/{done}", name="user_password_change")
     */
    public function passwordChangeAction(Request $request, $done = 0)
    {
        if ($done) {//Message de succès
            return $this->render('@User/User/password_change.html.twig');
        }
        //Afficher le formulaire
        $builder = $this->createFormBuilder();
        $builder->add('old_password', PasswordType::class, array('label' => 'Ancien mot de passe', 'required' => true));
        $builder->add('password', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'Veuillez bien confirmer votre mot de passe !',
            'required' => true,
            'first_options' => array('label' => 'Nouveau mot de passe'),
            'second_options' => array('label' => 'Confirmation mot de passe'),
        ));
        $form = $builder->getForm();
        $error_message = null;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $old_password = $form->get('old_password')->getData();
                $password=$form->get('password')->getData();
                /**@var $user User */
                $user = $this->getUser();
                $em = $this->getDoctrine()->getManager();
                $em->refresh($user);
                /**@var $encoder \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder*/
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                if (!isset($user)) {
                    $error_message = "Utilisateur invalid";
                }else if(!$encoder->isPasswordValid($user->getPassword(),$old_password,$user->getSalt()) ){
                    $error_message = "Ancien mot de passe incorrect";
                }
                else {
                    $user->setSalt(base64_encode($password));
                    $password = $encoder->encodePassword($password, $user->getSalt());
                    $user->setPassword($password);

                    //$em->getConnection()->beginTransaction();
                    try {
                        //echec en cas d'une demande de reinitialisation existante ou compte non activé
                        $user=$em->merge($user);
                        $em->flush();
                        //$em->getConnection()->commit();
                        return $this->redirectToRoute('user_password_change', ['done' => 1]);
                    } catch (\Exception $e) {
                        //$em->getConnection()->rollback();
                        $error_message = "Echec de l'opération !";
                        $this->get('logger')->info($e->getMessage());
                    }
                }
            }
        }
        return $this->render('@User/User/password_change.html.twig', array(
            'form' => $form->createView(),
            'error_message' => $error_message
        ));
    }

}
