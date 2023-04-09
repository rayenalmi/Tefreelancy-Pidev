<?php

namespace App\Test\Controller;

use App\Entity\Workspace;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkspaceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/workspace/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Workspace::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Workspace index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'workspace[name]' => 'Testing',
            'workspace[description]' => 'Testing',
            'workspace[task]' => 'Testing',
            'workspace[publication]' => 'Testing',
            'workspace[freelancers]' => 'Testing',
            'workspace[idoffer]' => 'Testing',
            'workspace[idOffer]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Workspace();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setTask('My Title');
        $fixture->setPublication('My Title');
        $fixture->setFreelancers('My Title');
        $fixture->setIdoffer('My Title');
        $fixture->setIdOffer('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Workspace');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Workspace();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setTask('Value');
        $fixture->setPublication('Value');
        $fixture->setFreelancers('Value');
        $fixture->setIdoffer('Value');
        $fixture->setIdOffer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'workspace[name]' => 'Something New',
            'workspace[description]' => 'Something New',
            'workspace[task]' => 'Something New',
            'workspace[publication]' => 'Something New',
            'workspace[freelancers]' => 'Something New',
            'workspace[idoffer]' => 'Something New',
            'workspace[idOffer]' => 'Something New',
        ]);

        self::assertResponseRedirects('/workspace/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getTask());
        self::assertSame('Something New', $fixture[0]->getPublication());
        self::assertSame('Something New', $fixture[0]->getFreelancers());
        self::assertSame('Something New', $fixture[0]->getIdoffer());
        self::assertSame('Something New', $fixture[0]->getIdOffer());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Workspace();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setTask('Value');
        $fixture->setPublication('Value');
        $fixture->setFreelancers('Value');
        $fixture->setIdoffer('Value');
        $fixture->setIdOffer('Value');

        $$this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/workspace/');
        self::assertSame(0, $this->repository->count([]));
    }
}
