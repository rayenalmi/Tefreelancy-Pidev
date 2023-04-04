<?php

namespace App\Test\Controller;

use App\Entity\Offer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OfferControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/offer/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Offer::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Offer index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'offer[name]' => 'Testing',
            'offer[description]' => 'Testing',
            'offer[duration]' => 'Testing',
            'offer[keywords]' => 'Testing',
            'offer[salary]' => 'Testing',
            'offer[idRecruter]' => 'Testing',
            'offer[idUser]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Offer();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDuration('My Title');
        $fixture->setKeywords('My Title');
        $fixture->setSalary('My Title');
        $fixture->setIdRecruter('My Title');
        $fixture->setIdUser('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Offer');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Offer();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setDuration('Value');
        $fixture->setKeywords('Value');
        $fixture->setSalary('Value');
        $fixture->setIdRecruter('Value');
        $fixture->setIdUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'offer[name]' => 'Something New',
            'offer[description]' => 'Something New',
            'offer[duration]' => 'Something New',
            'offer[keywords]' => 'Something New',
            'offer[salary]' => 'Something New',
            'offer[idRecruter]' => 'Something New',
            'offer[idUser]' => 'Something New',
        ]);

        self::assertResponseRedirects('/offer/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getDuration());
        self::assertSame('Something New', $fixture[0]->getKeywords());
        self::assertSame('Something New', $fixture[0]->getSalary());
        self::assertSame('Something New', $fixture[0]->getIdRecruter());
        self::assertSame('Something New', $fixture[0]->getIdUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Offer();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setDuration('Value');
        $fixture->setKeywords('Value');
        $fixture->setSalary('Value');
        $fixture->setIdRecruter('Value');
        $fixture->setIdUser('Value');

        $$this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/offer/');
        self::assertSame(0, $this->repository->count([]));
    }
}
