<?php

namespace App\Test\Controller;

use App\Entity\PublicationWs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicationWsControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/publication/ws/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(PublicationWs::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PublicationW index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'publication_w[title]' => 'Testing',
            'publication_w[content]' => 'Testing',
            'publication_w[author]' => 'Testing',
            'publication_w[creationdate]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new PublicationWs();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setCreationdate('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PublicationW');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new PublicationWs();
        $fixture->setTitle('Value');
        $fixture->setContent('Value');
        $fixture->setAuthor('Value');
        $fixture->setCreationdate('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'publication_w[title]' => 'Something New',
            'publication_w[content]' => 'Something New',
            'publication_w[author]' => 'Something New',
            'publication_w[creationdate]' => 'Something New',
        ]);

        self::assertResponseRedirects('/publication/ws/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getAuthor());
        self::assertSame('Something New', $fixture[0]->getCreationdate());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new PublicationWs();
        $fixture->setTitle('Value');
        $fixture->setContent('Value');
        $fixture->setAuthor('Value');
        $fixture->setCreationdate('Value');

        $$this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/publication/ws/');
        self::assertSame(0, $this->repository->count([]));
    }
}
