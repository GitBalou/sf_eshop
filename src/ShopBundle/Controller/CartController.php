<?php

namespace ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class CartController
 * @package ShopBundle\Controller
 */
class CartController extends Controller
{
    /**
     * Cart index action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $cartProvider = $this->get('shop.cart.provider');
        $items = $cartProvider->getCart()->getItems();

        return $this->render('ShopBundle:Cart:index.html.twig', [
            'items' => $items
        ]);
    }

    /**
     * Cart add item action
     *
     * @param integer $productId
     * @param integer $quantity
     *
     * @return RedirectResponse
     */
    public function addAction($productId, $quantity=1) {

        $cartProvider = $this->get('shop.cart.provider');

        if( $cartProvider->add($productId, $quantity) ){
            $this->addFlash('success', 'Article ajouté !');
        }
        else {
            $this->addFlash('warning', 'Une erreur est survenue : (');
        }

        return $this->redirectToRoute('shop_cart_index');
    }

    /**
     * Remove item from cart
     *
     * @param integer $productId
     * @return RedirectResponse
     */
    public function removeAction($productId) {

        $cartProvider = $this->get('shop.cart.provider');

        if( $cartProvider->remove($productId)) {
            $this->addFlash('success', 'Article retiré !');
        }
        else {
            $this->addFlash('danger', 'Une erreur est survenue :\' (');
        }

        return $this->redirectToRoute('shop_cart_index');
    }

    /**
     * increment item quantity by one
     *
     * @param integer $productId
     * @return RedirectResponse
     */
    public function incrementAction($productId){
        $cartProvider = $this->get('shop.cart.provider');

        if( $cartProvider->increment($productId)) {
            $this->addFlash('success', 'Quantitée augmentée');
        }
        else {
            $this->addFlash('danger', 'Une erreur est survenue :\' (');
        }

        return $this->redirectToRoute('shop_cart_index');
    }

    /**
     * decrement item quantity by one
     *
     * @param integer $productId
     * @return RedirectResponse
     */
    public function decrementAction($productId){
        $cartProvider = $this->get('shop.cart.provider');

        if( $cartProvider->decrement($productId)) {
            $this->addFlash('success', 'Quantitée diminuée');
        }
        else {
            $this->addFlash('danger', 'Une erreur est survenue :\' (');
        }

        return $this->redirectToRoute('shop_cart_index');
    }
}
