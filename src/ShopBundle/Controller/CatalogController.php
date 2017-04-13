<?php

namespace ShopBundle\Controller;

use ShopBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CatalogController
 * @package ShopBundle\Controller
 */
class CatalogController extends Controller
{
    /**
     * Catalog index action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository('ShopBundle\Entity\Category');
        $categories = $repo->findByEnabled(true);

        return $this->render('ShopBundle:Catalog:index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Catalog image action.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function imageAction(Request $request)
    {
        /** @var \ShopBundle\Entity\Image $image */
        $image = $this
            ->getDoctrine()
            ->getRepository('ShopBundle:Image')
            ->find($request->attributes->get('imageId'));

        if (null === $image) {
            throw $this->createNotFoundException('Image not found');
        }

        $response = new Response();
        $response->setLastModified($image->getUpdatedAt());
        if ($response->isNotModified($request)) {
            return $response;
        }

        $uploader = $this->get('shop.upload.image_uploader');
        if (null === $file = $uploader->loadFile($image->getFile())) {
            throw $this->createNotFoundException('File not found');
        }

        $response = new BinaryFileResponse($file);
        $response->setLastModified($image->getUpdatedAt());

        return $response;
    }

    /**
     * Catalog category Action
     *
     * @param string $categorySlug
     *
     * @return Response
     */
    public function categoryAction($categorySlug){

        $repo = $this->getDoctrine()->getRepository('ShopBundle:Category');
        $category = $repo->findOneBySlug( $categorySlug);

        $repo = $this->getDoctrine()->getRepository('ShopBundle:Product');
        $products = $repo->findByCategory($category);

        return $this->render('ShopBundle:Catalog:category.html.twig', [
            'category' => $category,
            'products' => $products
        ]);
    }

    /**
     * Show product action
     *
     * @param Request $request
     *
     * @return Response
     */
    public function productAction(Request $request) {

        $categorySlug = $request->attributes->get('categorySlug');
        $productSlug = $request->attributes->get('productSlug');

        $repo = $this->getDoctrine()->getRepository('ShopBundle:Category');
        $category = $repo->findOneBySlug( $categorySlug);

        $repo = $this->getDoctrine()->getRepository('ShopBundle:Product');
        $product = $repo->findOneBy([
            'category' => $category,
            'slug' => $productSlug
        ]);

        $formData = [
            'productId' => $product->getId(),
            'quantity' => 1,
        ];

        $form = $this->createFormBuilder($formData)
            ->add('productId', 'hidden')
            ->add('quantity', 'integer', array(
                'attr' => array(
                    'min' => 0,
                    'max' => $product->getStock()
                ),
                'constraints' => array(
                    new NotBlank(),
                    new GreaterThanOrEqual(0),
                    new LessThanOrEqual( $product->getStock() ),
                )
            ))
            ->add('ajouter au panier', 'submit')
            ->getForm();

        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ) {

            $formData = $form->getData();

            $cartProvider = $this->get('shop.cart.provider');

            if( $cartProvider->add(
                $formData['productId'],
                $formData['quantity']
            ) ){
                $this->addFlash('success', 'Article ajouté !');
            }
            else {
                $this->addFlash('warning', 'Une erreur est survenue : (');
            }

            return $this->redirectToRoute('shop_catalog_product', [
                'categorySlug' => $categorySlug,
                'productSlug' => $productSlug
            ]);
        }

        return $this->render('ShopBundle:Catalog:product.html.twig', [
            'category' => $category,
            'product' => $product,
            'formView' => $form->createView()
        ]);
    }
}
