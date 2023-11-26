<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
	#[Route('/', name: 'list_products')]
	public function index(Request $request, ManagerRegistry $doctrine): Response
	{

		$em = $doctrine->getManager();
		$products = $em->getRepository(Product::class)->findAll();

		$session = $request->getSession();
		$notification = $session->get('notification');
		$type_notif = $session->get('type_notif');

		return $this->render('product/index.html.twig', [
			'products' => $products,
			'notification' => $notification,
			'type_notif' => $type_notif
		]);
	}
	#[Route('/view-product/{id_product}', name: 'view_product')]
	public function viewproduct($id_product, ManagerRegistry $doctrine): Response
	{
		$em = $doctrine->getManager();

		$product = $em->getRepository(Product::class)->find($id_product);
		if ($product === null) {
			return $this->redirectToRoute('list_products');
		}

		return $this->render('product/viewProduct.html.twig', [
			'product' => $product,
		]);
	}
	#[Route('/edit-product/{id_product}', name: 'edit_product')]
	public function editProduct($id_product, ManagerRegistry $doctrine, Request $request): Response
	{
		$em = $doctrine->getManager();

		$product = $em->getRepository(Product::class)->find($id_product);

		if ($product === null) {
			return $this->redirectToRoute('list_products');
		}

		$form = $this->createForm(ProductType::class, $product);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if ($form->get('submit')->isClicked()) {
				// Le bouton Valider a été cliqué
				$em->flush();

				$session = $request->getSession();
				$session->set('notification', "Produit modifié avec succès");
				$session->set('type_notif', "alert-success");

				return $this->redirectToRoute('list_products');
			} elseif ($form->get('delete')->isClicked()) {
				// Le bouton Supprimer a été cliqué
				$em->remove($product);
				$em->flush();

				$session = $request->getSession();
				$session->set('notification', "Produit supprimé avec succès");
				$session->set('type_notif', "alert-success");

				return $this->redirectToRoute('list_products');
			}
		}

		return $this->render('product/editProduct.html.twig', [
			'form' => $form->createView(),
		]);
	}
	#[Route('/delete-product/{id_product}', name: 'delete_product')]
    public function deleteProduct($id_product, ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $product = $em->getRepository(Product::class)->find($id_product);

        if ($product === null) {
            return $this->redirectToRoute('list_products');
        }

        $em->remove($product);
        $em->flush();
		$session = $request->getSession();
		$session->set('notification', "Produit supprimé avec succès");
		$session->set('type_notif', "alert-success");

        return $this->redirectToRoute('list_products');
    }
	#[Route('/add-product', name: 'add_product')]
    public function addProduct(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            $em->persist($product);
            $em->flush();

            $session = $request->getSession();
            $session->set('notification', "Produit ajouté avec succès");
            $session->set('type_notif', "alert-success");

            return $this->redirectToRoute('list_products');
        }

        return $this->render('product/addProduct.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
