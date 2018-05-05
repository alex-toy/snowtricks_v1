<?php
namespace App\Controller;
//require_once '/Users/alexei/Documents/OPENCLASSROOM/Projet6/SnowTricks/vendor/autoload.php';


use Symfony\Component\Finder\Finder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Twig\Environment;

use App\Entity\Image;
use App\Entity\Figure;
use App\Entity\Message;

use App\Form\FigureType;



class FigureController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index(Environment $twig){
          
        $repository = $this->getDoctrine()->getRepository(Figure::class);
		$figures = $repository->findAll();
		
        return $this->render('accueil/figures.html.twig', 
		[
         	'figures' => $figures,
        	'numberFigures' => count($figures)
        ]); 
    }
    
    
    
    /**
     * @Route("/allfigures", name="allfigures")
     */
    public function allfiguresAction(SessionInterface $session){
        
        $repository = $this->getDoctrine()->getRepository(Figure::class);
		$figures = $repository->findAll();
		
        return $this->render('accueil/figures.html.twig', ['figures' => $figures]); 
    }
    
    
    
    /**
 	* @Route("/figure/{id}", name="figure_show")
 	*/
	public function showFigureByIdAction($id, Request $request)
	{
		$figure = $this->getDoctrine()
			->getRepository(Figure::class)
			->find($id);
		
		$messages = $figure->getMessages();
	
	
		$messagestest = $this->getDoctrine()
		->getRepository(Figure::class)
		->findAllMessages($figure->getId());
	
	
         
		if (!$figure) {
			throw $this->createNotFoundException(
				'No figure found for id '.$id
			);
		}
	
		$message = new Message();
		$form = $this->createFormBuilder($message)
		->add('contenu',     TextType::class)
		->add('save',      SubmitType::class)
		->getForm();
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$message = $form->getData();
			$message->setFigure($figure);
			$session = $request->getSession();
			$message->setAuthor($session->get('name'));
			$em = $this->getDoctrine()->getManager();
			$em->persist($message);
			$em->flush();
			
			$this->addFlash('messages', 'Vous venez de créer un message!!' );
			return $this->redirectToRoute('figure_show', array('id' => $id ));
		}
		
		
	
		return $this->render('accueil/showfigure.html.twig', 
		[
			'figure' => $figure, 
			'messages' => $messages, 
			'form' => $form->createView()
		]);
		
		
	
		
	}
	
	
	
	/**
 	* @Route("/createfigure", name="createfigure")
 	*/
	public function createFigureAction(Request $request)
	{
		$figure = new Figure();
    	$form = $this->createForm(FigureType::class, $figure);
		
		
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
         	$figure = $form->getData();
        	
         	$files = $figure->getUploadedFiles();
         	$figure->setUploadedFiles($files);
         	$figure->upload($this->getParameter('images_directory'));
         	$figure->setCreatedAt(getdate());
        	
         	$em = $this->getDoctrine()->getManager();
         	$em->persist($figure);
         	$images = $figure->getImages();
         	foreach($images as $image){
         		$image->setIsMasterImage();
         		$em->persist($image);
         	}
         	$em->flush();
         	
         	$this->addFlash('messages', 'Vous venez de créer une figure!!' );
        	
         	return $this->redirectToRoute('index');
     	}
    
    
    return $this->render('accueil/createfigure.html.twig', array(
        'form' => $form->createView(),
    ));
		
	}
	
	
	
	
	/**
 	* @Route("/modifyfigure/{id}", name="modifyfigure")
 	*/
	public function modifyfigureAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$figure = $em->getRepository(Figure::class)->find($id);
    	$form = $this->createForm(FigureType::class, $figure);
				
		$form->handleRequest($request);
		$image = new Image();
		if ($form->isSubmitted() && $form->isValid()) {
         	$figure = $form->getData();
        	
         	$files = $figure->getUploadedFiles();
         	$figure->setUploadedFiles($files);
         	$figure->upload($this->getParameter('images_directory'));
         	$figure->setCreatedAt(getdate());
        	
         	$em = $this->getDoctrine()->getManager();
         	$em->persist($figure);
         	$images = $figure->getImages();
         	foreach($images as $image){
         		if($image->getIsMasterImage() === null)$image->unSetIsMasterImage();
         		$em->persist($image);
         	}
         	$em->flush();
         	
         	$this->addFlash('messages', 'Vous venez de modifier la figure ' . $figure->getName() . '!!' );
        	
         	//return $this->redirectToRoute('index');
         	return $this->redirectToRoute('figure_show', array('id' => $id ));
     	}
    
    //return $this->redirectToRoute('figure/' . $figure->getId());
    return $this->render('accueil/modifyfigure.html.twig', array(
        'form' => $form->createView(),
    ));
		
	}
	
	
	
	
	/**
 	* @Route("/setMaster/{figureId}/{imageId}", name="setMaster")
 	*/
	public function setMasterAction(Request $request, $figureId, $imageId)
	{
		$em = $this->getDoctrine()->getManager();
		$figure = $em->getRepository(Figure::class)->find($figureId);
    	$images = $figure->getImages();
		foreach($images as $image){
         	$image->unSetIsMasterImage();
         	$em->persist($image);
        }		
		$ImageRepository = $this->getDoctrine()->getRepository(Image::class);
		$imageToSetMaster = $ImageRepository->findBy(
    		array('id' => $imageId)
		);
		
		foreach($imageToSetMaster as $image) echo 'test : ' . $this->addFlash('messages', 'test : ' . $image->getLink() );
		
		
		//$this->addFlash('messages', 'test : ' . $image->getLink() );
		
		//$imageToSetMaster->setIsMasterImage();
        //$em->persist($imageToSetMaster);
    
    	return $this->redirectToRoute('figure_show', array('id' => $figureId ));
		
	}
	
	
	
	
	
	/**
 	* @Route("/deletefigure/{id}", name="deletefigure")
 	*/
	public function deletefigureAction($id)
	{
		$em = $this->getDoctrine()->getManager();
    	$figure = $em->getRepository(Figure::class)->find($id);
		
		$em->remove($figure);
		$em->flush();
    	return $this->redirectToRoute('index');
	}
	
	
	
	/**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
	
	
    
    
	
   
}
