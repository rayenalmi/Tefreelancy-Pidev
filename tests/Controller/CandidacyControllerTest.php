<?php

namespace App\Test\Controller;

use App\Entity\Candidacy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CandidacyControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/candidacy/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Candidacy::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Candidacy index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'candidacy[object]' => 'Testing',
            'candidacy[message]' => 'Testing',
            'candidacy[accepted]' => 'Testing',
            'candidacy[idFreelancer]' => 'Testing',
            'candidacy[idOffer]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Candidacy();
        $fixture->setObject('My Title');
        $fixture->setMessage('My Title');
        $fixture->setAccepted('My Title');
        $fixture->setIdFreelancer('My Title');
        $fixture->setIdOffer('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Candidacy');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Candidacy();
        $fixture->setObject('Value');
        $fixture->setMessage('Value');
        $fixture->setAccepted('Value');
        $fixture->setIdFreelancer('Value');
        $fixture->setIdOffer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'candidacy[object]' => 'Something New',
            'candidacy[message]' => 'Something New',
            'candidacy[accepted]' => 'Something New',
            'candidacy[idFreelancer]' => 'Something New',
            'candidacy[idOffer]' => 'Something New',
        ]);

        self::assertResponseRedirects('/candidacy/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getObject());
        self::assertSame('Something New', $fixture[0]->getMessage());
        self::assertSame('Something New', $fixture[0]->getAccepted());
        self::assertSame('Something New', $fixture[0]->getIdFreelancer());
        self::assertSame('Something New', $fixture[0]->getIdOffer());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Candidacy();
        $fixture->setObject('Value');
        $fixture->setMessage('Value');
        $fixture->setAccepted('Value');
        $fixture->setIdFreelancer('Value');
        $fixture->setIdOffer('Value');

        $$this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/candidacy/');
        self::assertSame(0, $this->repository->count([]));
    }
}
