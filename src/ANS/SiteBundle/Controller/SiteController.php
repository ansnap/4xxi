<?php

namespace ANS\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ANS\SiteBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use ANS\SiteBundle\Form\Type\ProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ANS\SiteBundle\Form\Type\CommentType;
use ANS\SiteBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SiteController extends Controller
{

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $facebook = $this->get('facebook');

        if (!($user instanceof User)) {
            return array('facebook' => $facebook);
        } else {
            $friends = $facebook->api('/me/friends');

            $em = $this->getDoctrine()->getManager();
            $comments = $em->getRepository('ANSSiteBundle:Comment')->findAll();

            return array(
                'friends' => $friends,
                'comments' => $comments,
            );
        }
    }

    /**
     * @Route("/login")
     * @Template()
     */
    public function loginAction()
    {
        $facebook = $this->get('facebook');

        if ($facebook->getUser()) {
            $user_profile = $facebook->api('/me');

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('ANSSiteBundle:User')->findOneBy(array('email' => $user_profile['email']));

            if (!$user) {
                $user = new User();
                $user->setEmail($user_profile['email']);
                $user->setName($user_profile['first_name'] . ' ' . $user_profile['last_name']);

                $em->persist($user);
                $em->flush();
            }

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.context')->setToken($token);
        }

        return $this->redirect($this->generateUrl('ans_site_site_index'));
    }

    /**
     * @Route("/profile")
     * @Template()
     */
    public function profileAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(new ProfileType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Изменения сохранены');

            return $this->redirect($this->generateUrl('ans_site_site_profile'));
        }


        return array('form' => $form->createView());
    }

    /**
     * @Route("/add_comment")
     * @Template()
     */
    public function addCommentAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        $comment = new Comment();
        $comment->setAuthor($user);
        $form = $this->createForm(new CommentType(), $comment);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return new Response('ok');
        }

        return $this->render('ANSSiteBundle::form.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/edit_comment/{id}")
     * @Template()
     * @ParamConverter("comment", class="ANSSiteBundle:Comment")
     */
    public function editCommentAction(Request $request, Comment $comment)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof User) || $comment->getAuthor() != $user) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(new CommentType(), $comment);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return new Response('ok');
        }

        return $this->render('ANSSiteBundle::form.html.twig', array('form' => $form->createView()));
    }

}
