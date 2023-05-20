<?php

namespace App\Test\Controller;

use App\Entity\Candidate;
use App\Repository\CandidateRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CandidateControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CandidateRepository $repository;
    private string $path = '/candidate/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Candidate::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Candidate index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'candidate[lastName]' => 'Testing',
            'candidate[firstName]' => 'Testing',
            'candidate[email]' => 'Testing',
            'candidate[mobile]' => 'Testing',
            'candidate[resume]' => 'Testing',
            'candidate[applicationDate]' => 'Testing',
            'candidate[degree]' => 'Testing',
        ]);

        self::assertResponseRedirects('/candidate/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Candidate();
        $fixture->setLastName('My Title');
        $fixture->setFirstName('My Title');
        $fixture->setEmail('My Title');
        $fixture->setMobile('My Title');
        $fixture->setResume('My Title');
        $fixture->setApplicationDate('My Title');
        $fixture->setDegree('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Candidate');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Candidate();
        $fixture->setLastName('My Title');
        $fixture->setFirstName('My Title');
        $fixture->setEmail('My Title');
        $fixture->setMobile('My Title');
        $fixture->setResume('My Title');
        $fixture->setApplicationDate('My Title');
        $fixture->setDegree('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'candidate[lastName]' => 'Something New',
            'candidate[firstName]' => 'Something New',
            'candidate[email]' => 'Something New',
            'candidate[mobile]' => 'Something New',
            'candidate[resume]' => 'Something New',
            'candidate[applicationDate]' => 'Something New',
            'candidate[degree]' => 'Something New',
        ]);

        self::assertResponseRedirects('/candidate/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getLastName());
        self::assertSame('Something New', $fixture[0]->getFirstName());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getMobile());
        self::assertSame('Something New', $fixture[0]->getResume());
        self::assertSame('Something New', $fixture[0]->getApplicationDate());
        self::assertSame('Something New', $fixture[0]->getDegree());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Candidate();
        $fixture->setLastName('My Title');
        $fixture->setFirstName('My Title');
        $fixture->setEmail('My Title');
        $fixture->setMobile('My Title');
        $fixture->setResume('My Title');
        $fixture->setApplicationDate('My Title');
        $fixture->setDegree('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/candidate/');
    }
}
