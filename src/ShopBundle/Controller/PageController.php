<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Customer;
use ShopBundle\ShopBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ShopBundle\Form\CustomerType;

/**
 * Class PageController
 * @package ShopBundle\Controller
 */
class PageController extends Controller
{
    /**
     * Index action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('ShopBundle:Page:index.html.twig');
    }

    /**
     * Contact action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {

        $form = $this->createFormBuilder()
            ->add('subject')
            ->add('email')
            ->add('attachment', 'file')
            ->add('message', 'textarea')
            ->add('envoyer', 'submit')
            ->getForm();

        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ) {

            $this->addFlash('success', 'Votre message a bien été envoyé');
            $data = $form->getData();

            // upload file
            /*
            $file = $data['attachment'];
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            $dir = $this->getParameter('kernel.root_dir').'/../var/data';
            $file->move($dir, $fileName);
            // end upload
            */

            // mail
            $message = \Swift_Message::newInstance()
                ->setTo('gabriel.daudin@nantes.imie.fr')
                ->setFrom('gabriel.daudin@gmail.com')
                ->setSubject('yo')
                ->setBody($this->renderView(
                    'ShopBundle:Email:contact.html.twig', [
                        'data' =>  $data
                    ]
                ), 'text/html');

            $sent = $this->get('mailer')->send($message);

            return $this->redirectToRoute('shop_contact');
        }

        return $this->render('ShopBundle:Page:contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
