<?php

namespace App\Controller;

use App\Entity\Product;
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
}
