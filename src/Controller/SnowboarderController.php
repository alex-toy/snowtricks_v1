<?php
namespace App\Controller;
//require_once '/Users/alexei/Sites/SnowTricks/vendor/autoload.php';
use Symfony\Component\Finder\Finder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Entity\Message;
use App\Entity\Snowboarder;

class SnowboarderController extends Controller
{
    
	/**
 	* @Route("/createaccount", name="createaccount")
 	*/
	public function createaccountAction(Request $request)
	{
		$Snowboarder = new Snowboarder();
    	$formSnowboarder = $this->createFormBuilder($Snowboarder)
        ->add('name',     TextType::class)
        ->add('email',     TextType::class)
        ->add('password',     TextType::class)
		->add('save',      SubmitType::class)
        ->getForm();
		$formSnowboarder->handleRequest($request);
		if ($formSnowboarder->isSubmitted() && $formSnowboarder->isValid()) {
        	$Snowboarder = $formSnowboarder->getData();
        	
        	$em = $this->getDoctrine()->getManager();
        	$em->persist($Snowboarder);
        	$em->flush();
        	
        	$subject = 'Validation de compte';
        	$message = ucfirst($Snowboarder->getName()) . ', suivez ce lien pour valider votre compte : http://127.0.0.1:8000/validateAccount/' . $Snowboarder->getToken();
     		$headers = 'From: webmaster@snowtricks.com' . "\r\n" .
     		'Reply-To: webmaster@snowtricks.com' . "\r\n" .
    		 'X-Mailer: PHP/' . phpversion();
        	mail($Snowboarder->getEmail(), $subject, $message, $headers);
        	
        	$this->addFlash('messages', ucfirst($Snowboarder->getName()) . ', merci de regarder dans vos mail afin de valider votre compte' );
        	return $this->redirectToRoute('index');
    	}
    	
    return $this->render('accueil/createaccount.html.twig', array(
        'formSnowboarder' => $formSnowboarder->createView(),
    ));
		
	}
	
	
	/**
 	* @Route("/connect", name="connect")
 	*/
	public function connectAction(Request $request)
	{
		$Snowboarder = new Snowboarder();
    	$formSnowboarder = $this->createFormBuilder($Snowboarder)
        ->add('name',     TextType::class)
        ->add('password',     PasswordType::class)
		->add('Se connecter',      SubmitType::class)
        ->getForm();
		$formSnowboarder->handleRequest($request);
		if ($formSnowboarder->isSubmitted() ) {
        	$Snowboarder = $formSnowboarder->getData();
        	 
        	$name = $Snowboarder->getName();
        	$pwd = $Snowboarder->getPassword();
        	
        	$SnowboarderDB = $this->getDoctrine()
    		->getRepository(Snowboarder::class)
    		->findOneWithNameAndEmail($name, $pwd);
    		
    		
    		
    		if(isset( $SnowboarderDB[0]) && $SnowboarderDB[0]->isValidated()){
				//$session = new Session();
				//$session->start();
				$session = $request->getSession();
				$session->set('name', $SnowboarderDB[0]->getName());
				$session->getFlashBag()->add('notice', 'Profile updated');
				echo 'test : ' . $session->getId();
				$session->set('sessionId', $session->getId());
				$this->addFlash('messages', 'Bienvenue parmi nous ' . $SnowboarderDB[0]->getName() . '!' );
				return $this->redirectToRoute('index');
			}else if( isset( $SnowboarderDB[0]) && !$SnowboarderDB[0]->isValidated() ){
				$this->addFlash('messages', 'Merci de valider votre profil avant de vous connecter ' . ucfirst($SnowboarderDB[0]->getName()) . '!' );
				return $this->redirectToRoute('index'); 
			}else{
				return $this->render('accueil/connected.html.twig', array('unknown' => true)); 
			}
        	
    	}
    	
    return $this->render('accueil/connect.html.twig', array(
        'formSnowboarder' => $formSnowboarder->createView(),
    ));
		
	}
	
	
	
	/**
 	* @Route("/disconnect/{sessionId}", name="disconnect")
 	*/
	public function disconnectAction(Request $request, $sessionId)
	{	
		$session = new Session();
		$name = $session->get('name');
		$session->clear($sessionId);
		
		$this->addFlash('messages', 'Au revoir et à bientôt ' . $name );
		return $this->redirectToRoute('index');
		
	}
	
	
	/**
 	* @Route("/validateAccount/{token}", name="validateAccount")
 	*/
	public function validateAccountAction(Request $request, $token)
	{
		$entityManager = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository(Snowboarder::class);
    	
    	$Snowboarder = $repository->findBy(
    		array('token' => $token)
    	);
    	
    	if (!$Snowboarder) {
        	throw $this->createNotFoundException(
            	'No Snowboarder found for token '.$token
        	);
    	}
		$Snowboarder[0]->ValidateAccount();
    	$entityManager->flush();
    	
    	$this->addFlash('messages', 'Félicitations, votre compte vient d\'être validé !!' );
        return $this->redirectToRoute('index');
		
	}
	
	
	/**
 	* @Route("/forgotPassword", name="forgotPassword")
 	*/
	public function forgotPasswordAction(Request $request)
	{
		$Snowboarder = new Snowboarder();
    	$formSnowboarder = $this->createFormBuilder($Snowboarder)
        ->add('name',     TextType::class)
        ->add('email',     TextType::class)
        ->add('Réinitialiser le mot de passe',      SubmitType::class)
        ->getForm();
		
		$formSnowboarder->handleRequest($request);
		if ($formSnowboarder->isSubmitted() ) {
        	$Snowboarder = $formSnowboarder->getData();
        	 
        	$email = $Snowboarder->getEmail();
        	$name = $Snowboarder->getName();
        	
        	$repository = $this->getDoctrine()->getRepository(Snowboarder::class);
        	$SnowboarderDB = $repository->findOneBy(['email' => $email, 'name' => $name]);
        	
        	if(isset( $SnowboarderDB )){
        	
        		$subject = 'oubli de mot de passe';
        		$message = ucfirst($SnowboarderDB->getName()) . ', suivez ce lien pour regénérer votre mot de passe : http://127.0.0.1:8000/regeneratePassword/' . $SnowboarderDB->getToken();
     			$headers = 'From: webmaster@snowtricks.com' . "\r\n" .
     				'Reply-To: webmaster@snowtricks.com' . "\r\n" .
    		 		'X-Mailer: PHP/' . phpversion();
        		mail($Snowboarder->getEmail(), $subject, $message, $headers);
        	
        		$this->addFlash('messages', ucfirst($SnowboarderDB->getName()) . ', merci de regarder dans vos mail afin de regénérer votre mot de passe.' );
        		return $this->redirectToRoute('index');
        	
    		}
		}
		
		return $this->render('accueil/forgotPassword.html.twig', array(
        	'formSnowboarder' => $formSnowboarder->createView(),
    	));
		

		
	}
	
	
	
	/**
 	* @Route("/regeneratePassword/{token}", name="regeneratePassword")
 	*/
	public function regeneratePasswordAction(Request $request, $token)
	{
		
		$entityManager = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository(Snowboarder::class);
		$SnowboarderToUpdate = $repository->findOneBy(
    		array('token' => $token)
    	);
		
		
    	if (!$SnowboarderToUpdate) {
       	 	$this->addFlash('messages', 'Désolé, ce lien n\'est plus valide.' );
        	return $this->redirectToRoute('index');
    	}
    	
    	
		$SnowboarderForm = new Snowboarder();
    	$formSnowboarder = $this->createFormBuilder($SnowboarderForm)
        ->add('password', RepeatedType::class, array(
    		'type' => PasswordType::class,
    		'invalid_message' => 'The password fields must match.',
    		'options' => array('attr' => array('class' => 'password-field')),
    		'required' => true,
    		'first_options'  => array('label' => 'Password'),
    		'second_options' => array('label' => 'Repeat Password'),
		))
        ->add('Réinitialiser le mot de passe',      SubmitType::class)
        ->getForm();
		
		$formSnowboarder->handleRequest($request);
		if ($formSnowboarder->isSubmitted() ) {
        	$password = $formSnowboarder->getData()->getPassword();
        	echo 'password : ' . $password;
        	$SnowboarderToUpdate->setPassword($password);
        	$SnowboarderToUpdate->setToken();
        	$entityManager->flush();
        	
        	$this->addFlash('messages', ucfirst($SnowboarderToUpdate->getName()) . ', votre mot de passe vient d\'être regénéré.' );
        	return $this->redirectToRoute('index');
        	
		}
		
		return $this->render('accueil/regeneratePassword.html.twig', array(
        	'formSnowboarder' => $formSnowboarder->createView(),
        	'name' => $SnowboarderToUpdate->getName()
    	));
		
		
	}

	
    
    
    
   
}



















