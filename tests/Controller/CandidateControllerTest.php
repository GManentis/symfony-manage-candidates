<?php

namespace App\Test\Controller;

use App\Entity\Candidate;
use App\Entity\Degree;

use App\Repository\CandidateRepository;
use App\Repository\DegreeRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;



class CandidateControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CandidateRepository $repository;
    private string $path = '/candidate/';
    private DegreeRepository $degreeRepository;

   

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Candidate::class);
        $this->degreeRepository = static::getContainer()->get('doctrine')->getRepository(Degree::class);

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

        $fixtureDegree = new Degree();
        $fixtureDegree->setDegreeTitle('My Title');
        $this->degreeRepository->save($fixtureDegree, true);

        //$this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'candidate[lastName]' => 'Testing',
            'candidate[firstName]' => 'Testing',
            'candidate[email]' => 'test@mail.com',
            'candidate[mobile]' => '6979200044',
            //'candidate[resume]' => null,
            'candidate[degree]' => $fixtureDegree->getId(),
        ]);

        self::assertResponseRedirects('/candidate/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        //$this->markTestIncomplete();
        $fixture = new Candidate();
        $fixture->setLastName('My Lastname');
        $fixture->setFirstName('My firstname');
        $fixture->setEmail('newmail@test.com');
        $fixture->setMobile('6970000000');
        $fixture->setApplicationDate(new \DateTime());

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Candidate');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $fixtureDegree = new Degree();
        $fixtureDegree->setDegreeTitle('My Title');
        $this->degreeRepository->save($fixtureDegree, true);


        //$this->markTestIncomplete();
        $fixture = new Candidate();
        $fixture->setLastName('My Title');
        $fixture->setFirstName('My Title');
        $fixture->setEmail('myemail@test.com');
        $fixture->setMobile('0000000000');
        $fixture->setApplicationDate(new \DateTime());


        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'candidate[lastName]' => 'Something New',
            'candidate[firstName]' => 'Something New',
            'candidate[email]' => 'something.new@test.com',
            'candidate[mobile]' => '6972000001',
            'candidate[degree]' => $fixtureDegree->getId(),
            'candidate[resume]' => null
        ]);

        self::assertResponseRedirects("/candidate/");

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getLastName());
        self::assertSame('Something New', $fixture[0]->getFirstName());
        self::assertSame('something.new@test.com', $fixture[0]->getEmail());
        self::assertSame('6972000001', $fixture[0]->getMobile());
        self::assertSame($fixtureDegree->getDegreeTitle(), $fixture[0]->getDegree()->getDegreeTitle());
    }

    public function testRemove(): void
    {
        //$this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Candidate();
        $fixture->setLastName('My Title');
        $fixture->setFirstName('My Title');
        $fixture->setEmail('my.email@test.com');
        $fixture->setMobile('0000000000');
        $fixture->setApplicationDate(new \DateTime());


        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/candidate/');
    }
}
