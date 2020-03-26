<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use UserBundle\Entity\Pub;
use UserBundle\Entity\PubLike;
use UserBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class PubController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/pub/new", name="pub_new")
     */
    public function newpubAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->refresh($user);
        $pub = new Pub();
        $form = $this->createForm('UserBundle\Form\PubType', $pub);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $pub->setUser($user);
                $dir = $this->getParameter('pub_images_dir');//dans config.yml
                foreach ($pub->getUploadedFiles() as $file) {
                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($dir, $filename);
                    $f = new \UserBundle\Entity\File();
                    $f->setPath($filename);
                    $f->setType('IMAGE');
                    $pub->addFile($f);
                }
                $pub->setNbLike(0);
                $pub->setNbDislike(0);
                $pub->setNbComment(0);

                $em->persist($pub);
                $em->flush();

                $session = $request->getSession();
                $session->getFlashBag()->add('pub_new_message', 'Publication bien enregistrée!');

                return $this->redirect($this->generateUrl('pub_view', array('pub' => $pub->getId())));
            }
        }


        return $this->render('@User/Pub/newpub.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/pub/modify/{pub}", name="pub_modify", requirements={"pub"="\d+"})
     */
    public function modifypubAction(Pub $pub, Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $pub)) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier cette publication!" . $pub);
        }

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->refresh($user);
        $form = $this->createForm('UserBundle\Form\PubType', $pub);

        $form->add('deleteFiles', EntityType::class, array('class' => 'UserBundle:File', 'choices' => $pub->getFiles(), 'choice_label' => 'path', 'mapped' => false, 'expanded' => true, 'multiple' => true, 'label' => 'Cocher les images à supprimer'));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $pub->setUser($user);
                $dir = $this->getParameter('pub_images_dir');
                foreach ($pub->getUploadedFiles() as $file) {
                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move($dir, $filename);
                    $f = new \UserBundle\Entity\File();
                    $f->setPath($filename);
                    $f->setType('IMAGE');
                    $pub->addFile($f);
                }


                foreach ($form->get('deleteFiles')->getData() as $file) {
                    //throw new \Exception("Error Processing Request", 1);
                    $pub->removeFile($file);
                    //$em->refresh($file);
                    //$em->remove($file);
                }


                $em->persist($pub);
                $em->flush();

                $session = $request->getSession();
                $session->getFlashBag()->add('pub_modify_message_success', 'Publication bien modifiée!');

                return $this->redirect($this->generateUrl('pub_view', array('pub' => $pub->getId())));
            }
        }


        return $this->render('@User/Pub/modifypub.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
            'pub' => $pub
        ));
    }

    /**
     * @Route("/pub/view/{pub}", name="pub_view", requirements={"pub"="\d+"})
     */
    public function viewpubAction(Pub $pub)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $form = null;
        if (isset($user)) {
            $em->refresh($user);
            $pubLike = $this->getDoctrine()->getRepository(PubLike::class)->findOneBy(array('liker' => $user, 'pub' => $pub));
            if (isset($pubLike)) {
                //Marquer les pubs liker afin d'y appliquer un style different
                $pub->setLikeExtra($pubLike->getIsLike() ? 'like' : 'dislike');
            }

            //Creer le formulaire de commentaire
            $comment = new Comment();
            $comment->setPub($pub);
            $comment->setUser($user);
            $form = $this->createForm('UserBundle\Form\CommentType', $comment);

        }

        //recuperer les commentaire
        $commentaires = $this->getDoctrine()->getRepository(Comment::class)->findBy(array('pub' => $pub,), array('dateheure' => 'asc'));


        //Verifier s'il est le proprietaire de la pub pour lui permettre de la supprimer/modifier
        $isOwner = $this->get('security.authorization_checker')->isGranted('edit', $pub);


        if (isset($form)) {
            return $this->render('@User/Pub/viewpub.html.twig', array(
                'pub' => $pub, 'form' => $form->createView(), 'commentaires' => $commentaires, 'isOwner' => $isOwner
            ));
        }
        return $this->render('@User/Pub/viewpub.html.twig', array(
            'pub' => $pub, 'commentaires' => $commentaires,
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/pub/delete/{pub}", name="pub_delete", requirements={"pub"="\d+"})
     */
    public function deletepubAction(Pub $pub, Request $request)
    {
        //$this->denyAccessUnlessGranted('edit', $pub);
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $pub)) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de supprimer cette publication!" . $pub);
        }
        $builder = $this->createFormBuilder();
        $builder
            ->add('Supprimer', SubmitType::class, array('label' => 'Confirmer la suppression', 'attr' => array('required' => 'true', 'class' => 'btn btn-success')))
            ->add('Annuler', SubmitType::class, array('label' => 'Annuler la suppression', 'attr' => array('required' => 'true', 'class' => 'btn btn-danger')));
        //add('action', ChoiceType::class, array('label' => 'Choisir l\'action', 'required'=> true, 'attr'=>array('required' => 'true'),
        //   'choices' => array('Confirmer la suppression' => TRUE, 'Annuler la suppression' => FALSE), 'expanded'=>true, 'multiple'=>false ) );
        $form = $builder->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // ... perform some action, such as saving the task to the database
                if ($form->get('Supprimer')->isClicked()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($pub);
                    $em->flush();
                    return $this->redirectToRoute('pub_list');
                } else if ($form->get('Annuler')->isClicked()) {
                    return $this->redirect($this->generateUrl('pub_view', array('pub' => $pub->getId(),)));
                }

            }
        }
        return $this->render('@User/Pub/deletepub.html.twig', array(
            'form' => $form->createView(), 'pub' => $pub
        ));
    }

    /**
     * @Route("/pub/list/{page}", name="pub_list", requirements={"page"="\d+"})
     */
    public function pubsAction($page = 1)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if ($user != null) $em->refresh($user);


        //nbpage = nbitem%nbitem_voulu==0 ? nbitem/nbitem_voulu : nbitem/nbitem_voulu+1;
        //page i
        //limit nbitem_voulu 
        //offset (nbitem_voulu*(i-1))+1

        $nb_item = 4; //Nombre de Pub par pages
        $offset = ($nb_item * ($page - 1));

        $repository = $this->getDoctrine()->getRepository(Pub::class);
        $pubs = $repository
            //->findAll();
            ->getPubs($offset, $nb_item);

        //Recuperer les likes
        $pubIds = array();
        foreach ($pubs as $pub) {
            array_push($pubIds, $pub->getId());
        }
        $pubLikes = $this->getDoctrine()->getRepository(PubLike::class)->findBy(array('liker' => $user, 'pub' => $pubIds));
        $likedPubs = array();//liste des likes
        $dislikedPubs = array();//liste des dislikes
        if (isset($pubLikes) && !empty($pubLikes)) {
            //Recuperer les ids des pubs liker
            foreach ($pubLikes as $like) {
                if ($like->getIsLike()) {
                    array_push($likedPubs, $like->getPub()->getId());
                } else if (!$like->getIsLike()) {
                    array_push($dislikedPubs, $like->getPub()->getId());
                }
            }
        }
        //Marquer les pubs liker afin d'y appliquer un style different
        foreach ($pubs as $pub) {
            if (in_array($pub->getId(), $likedPubs)) {
                $pub->setLikeExtra('like');
            } else if (in_array($pub->getId(), $dislikedPubs)) {
                $pub->setLikeExtra('dislike');
            }
        }

        $row_count = count($pubs);//Nombre des Pubs dans la bdd
        $nb_page = 0;
        if (($row_count % $nb_item) == 0) $nb_page = $row_count / $nb_item;
        else $nb_page = 1 + (int)($row_count / $nb_item);
        $is_last_page = ($nb_page == $page);
        return $this->render('@User/Pub/pubs.html.twig', array(
            'user' => $user,
            'pubs' => $pubs,
            'pagenum' => $page,
            'nombre_page' => $nb_page,
            'row_count' => $row_count
        ));
        //page 1
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/pub/like/{page}/{pub}", name="pub_like", requirements={"pub"="\d+","page"="\d+"})
     */
    public function likepubAction(Pub $pub, $page = 1)
    {
        //Page = 0 si on etait dans pub_view, on va rediriger la bas
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->refresh($user);

        $repository = $this->getDoctrine()->getRepository(PubLike::class);
        //Recuperer le like
        $pubLikes = $repository->findBy(array('liker' => $user, 'pub' => $pub));
        //Si le like exist, on le supprime
        if (isset($pubLikes) && !empty($pubLikes)) {
            $like = $pubLikes[0];
            if ($like->getIsLike()) {//Si c'est un like, comme on like, on le supprime
                $em->remove($like);
                $em->flush();
            } else if (!$like->getIsLike()) { //si c'est un dislike, on le tourne en like
                $like->setIsLike(TRUE);
                $em->flush();
            }
        } else {
            //On cree un nouveau like
            $like = new PubLike;
            $like->setLiker($user);
            $like->setPub($pub);
            $like->setIsLike(TRUE);
            $em->persist($like);
            $em->flush();
        }
        //On redigige vers la liste des pubs
        if ($page == 0) {
            return $this->redirect($this->generateUrl('pub_view', array('pub' => $pub->getId())));
        }
        return $this->redirect($this->generateUrl('pub_list', array('page' => $page,)));
    }


    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/pub/dislike/{page}/{pub}", name="pub_dislike", requirements={"pub"="\d+","page"="\d+"})
     */
    public function dislikepubAction(Pub $pub, $page = 1)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->refresh($user);

        $repository = $this->getDoctrine()->getRepository(PubLike::class);
        //Recuperer le like
        //findOneBy...
        $pubLikes = $repository->findBy(array('liker' => $user, 'pub' => $pub));
        //Si le like exist, on le supprime
        if (isset($pubLikes) && !empty($pubLikes)) {
            $like = $pubLikes[0];
            if (!$like->getIsLike()) {//Si c'est un dislike, comme on dislike, on le supprime
                $em->remove($like);
                $em->flush();
            } else if ($like->getIsLike()) { //si c'est un like, on le tourne en dislike
                $like->setIsLike(FALSE);
                $em->flush();
            }
        } else {
            //On cree un nouveau like
            $like = new PubLike;
            $like->setLiker($user);
            $like->setPub($pub);
            $like->setIsLike(FALSE);
            $em->persist($like);
            $em->flush();
        }
        //On redigige vers la liste des pubs
        if ($page == 0) {
            return $this->redirect($this->generateUrl('pub_view', array('pub' => $pub->getId())));
        }
        return $this->redirect($this->generateUrl('pub_list', array('page' => $page,)));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/pub/comment/{pub}", name="pub_comment", requirements={"pub"="\d+"})
     */
    public function commentpubAction(Pub $pub, Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->refresh($user);
        $comment = new Comment();
        $form = $this->createForm('UserBundle\Form\CommentType', $comment);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setUser($user);
                $comment->setPub($pub);
                $comment->setDateheure(new \DateTime());
                $em->persist($comment);
                $em->flush();

                $session = $request->getSession();
                $session->getFlashBag()->add('comment_new_message_success', 'Commentaire envoyé!');

                return $this->redirect($this->generateUrl('pub_view', array('pub' => $pub->getId(),)));
            }
        }
        $session = $request->getSession();
        $session->getFlashBag()->add('comment_new_message_failure', 'Echec de l\'envoi du commentaire!');
        return $this->redirect($this->generateUrl('pub_view', array('pub' => $pub->getId(),)));
    }

    /**
     * @Route("/pub/search/{page}", name="pub_search", requirements={"page"="\d+"})
     */
    public function searchPubAction(Request $request, $page = 1, $text='')
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if ($user != null) $em->refresh($user);

        $text=$request->get('text');

        //nbpage = nbitem%nbitem_voulu==0 ? nbitem/nbitem_voulu : nbitem/nbitem_voulu+1;
        //page i
        //limit nbitem_voulu
        //offset (nbitem_voulu*(i-1))+1

        $nb_item = 3; //Nombre de Pub par pages
        $offset = ($nb_item * ($page - 1));

        /**@var $repository \UserBundle\Entity\PubRepository*/
        $repository = $this->getDoctrine()->getRepository(Pub::class);
        $pubs = $repository->searchPubs($text, $offset, $nb_item);

        //Recuperer les likes
        $pubIds = array();
        foreach ($pubs as $pub) {
            array_push($pubIds, $pub->getId());
        }
        $pubLikes = $this->getDoctrine()->getRepository(PubLike::class)->findBy(array('liker' => $user, 'pub' => $pubIds));
        $likedPubs = array();//liste des likes
        $dislikedPubs = array();//liste des dislikes
        if (isset($pubLikes) && !empty($pubLikes)) {
            //Recuperer les ids des pubs liker
            foreach ($pubLikes as $like) {
                if ($like->getIsLike()) {
                    array_push($likedPubs, $like->getPub()->getId());
                } else if (!$like->getIsLike()) {
                    array_push($dislikedPubs, $like->getPub()->getId());
                }
            }
        }
        //Marquer les pubs liker afin d'y appliquer un style different
        foreach ($pubs as $pub) {
            if (in_array($pub->getId(), $likedPubs)) {
                $pub->setLikeExtra('like');
            } else if (in_array($pub->getId(), $dislikedPubs)) {
                $pub->setLikeExtra('dislike');
            }
        }

        $row_count = count($pubs);//Nombre des Pubs dans la bdd
        $nb_page = 0;
        if (($row_count % $nb_item) == 0) $nb_page = $row_count / $nb_item;
        else $nb_page = 1 + (int)($row_count / $nb_item);
        $is_last_page = ($nb_page == $page);
        return $this->render('@User/Pub/search_pub.html.twig', array(
            'user' => $user,
            'pubs' => $pubs,
            'pagenum' => $page,
            'nombre_page' => $nb_page,
            'row_count' => $row_count,
            'text'=>$text
        ));
        //page 1
    }


}
