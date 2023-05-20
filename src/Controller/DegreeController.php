<?php

namespace App\Controller;

use App\Entity\Degree;
use App\Form\DegreeType;
use App\Repository\DegreeRepository;
use App\Repository\CandidateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\Validator\ValidatorInterface;



#[Route('/degree')]
class DegreeController extends AbstractController
{
    #[Route('/', name: 'app_degree_index', methods: ['GET'])]
    public function index(DegreeRepository $degreeRepository): Response
    {
        return $this->render('degree/index.html.twig', [
            'degrees' => $degreeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_degree_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DegreeRepository $degreeRepository, ValidatorInterface $validator): Response
    {
        $degree = new Degree();
        $form = $this->createForm(DegreeType::class, $degree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $degreeRepository->save($degree, true);
            return $this->redirectToRoute('app_degree_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('degree/new.html.twig', [
            'degree' => $degree,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_degree_show', methods: ['GET'])]
    public function show(Degree $degree): Response
    {
        return $this->render('degree/show.html.twig', [
            'degree' => $degree,
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_degree_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Degree $degree, DegreeRepository $degreeRepository): Response
    {
        $form = $this->createForm(DegreeType::class, $degree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $degreeRepository->save($degree, true);

            return $this->redirectToRoute('app_degree_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('degree/edit.html.twig', [
            'degree' => $degree,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_degree_delete', methods: ['POST'])]
    public function delete(Request $request, Degree $degree, DegreeRepository $degreeRepository, CandidateRepository $candidateRepository): Response
    {

        if ($this->isCsrfTokenValid('delete'.$degree->getId(), $request->request->get('_token'))) {
            $candidateWithDegreeForDeletion = $candidateRepository->findOneByDegree($degree->getId());
            if($candidateWithDegreeForDeletion) {
                $this->addFlash('errorDegree', 'Cannot delete a degree that is already assigned to candidate.');
                return $this->redirectToRoute('app_degree_index', [], Response::HTTP_SEE_OTHER);
            }
            $degreeRepository->remove($degree, true);
        }

        return $this->redirectToRoute('app_degree_index', [], Response::HTTP_SEE_OTHER);
    }
}
