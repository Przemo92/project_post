<?php


namespace App\Command;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;

class CreatePostsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-posts';

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates posts.')

            // the full command description shown when running the command with
            ->setHelp('This command allows you to create post from REST API')
        ;
        $this
            // configure an argument
            ->addArgument('user', InputArgument::REQUIRED, 'The user id.')
            // ...
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://jsonplaceholder.typicode.com/posts');

        $posts = json_decode($response->getContent());
        $user = $this->entityManager->getRepository(User::class)->find($input->getArgument('user'));
        foreach ($posts as $postFromJson) {
            //var_dump($input->getArgument('user'));
            $postFromJson = (array) $postFromJson;
            $post = new Post();
            $post->setUser($user);
            $post->setTitle($postFromJson["title"]);
            $post->setBody($postFromJson["body"]);
            $this->entityManager->persist($post);
        }
        $this->entityManager->flush();
        return 0;
    }
}