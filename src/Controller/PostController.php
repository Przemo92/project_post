<?php


namespace App\Controller;


use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class PostController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(UserInterface $user): \Symfony\Component\HttpFoundation\Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository(Post::class)->findAll();

        return $this->render('list.html.twig', array(
            'posts' => $posts,
        ));
    }
    /**
     * @Route("/remove", name="remove")
     */
    public function remove(UserInterface $user): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository(Post::class)->findAll();

        foreach ($posts as $post) {
            $entityManager->remove($post);
        }
        $entityManager->flush();

        return $this->redirectToRoute("list");

    }

    /**
     * @Route("/create", name="create")
     * @throws \Exception
     */
    public function createFromCommand(UserInterface $user, KernelInterface $kernel): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:create-posts',
            // (optional) define the value of command arguments
            'user' => $user,
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        return $this->redirectToRoute("list");

    }
}