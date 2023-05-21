<?php

namespace App\Test\Controller;

use App\Entity\Degree;
use App\Repository\DegreeRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DegreeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DegreeRepository $repository;
    private string $path = '/degree/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Degree::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Degree index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }


    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        //$this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        
        $this->client->submitForm('Save', [
            'degree[degreeTitle]' => 'Testing',
        ]);


        self::assertResponseRedirects('/degree/');
        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
        
    }


    public function testShow(): void
    {
        //$this->markTestIncomplete();
        $fixture = new Degree();
        $fixture->setDegreeTitle('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Degree');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        //$this->markTestIncomplete();
        $fixture = new Degree();
        $fixture->setDegreeTitle('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'degree[degreeTitle]' => 'Something New',
        ]);

        self::assertResponseRedirects('/degree/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getDegreeTitle());
    }

    
    public function testRemove(): void
    {
        //$this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Degree();
        $fixture->setDegreeTitle('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/degree/');
    }
    

}
