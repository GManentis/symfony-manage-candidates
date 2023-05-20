<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\CandidateType;
use App\Repository\CandidateRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\HttpFoundation\StreamedResponse;



#[Route('/candidate')]
class CandidateController extends AbstractController
{
    #[Route('/', name: 'app_candidate_index', methods: ['GET'])]
    public function index(CandidateRepository $candidateRepository): Response
    {
        return $this->render('candidate/index.html.twig', [
            'candidates' => $candidateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_candidate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CandidateRepository $candidateRepository): Response
    {
        $candidate = new Candidate();
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('resume')->getData();
            $resumeFileType = null;
            $resumeFileExtension = null;

            if ($file) {
                $resumeFileExtension = $file->getClientOriginalExtension();
                $resumeFileType = $file->getMimeType();
                $candidate->setResume(file_get_contents($file->getPathname()));
            }

            
            $candidate->setResumeFileExtension($resumeFileExtension);
            $candidate->setResumeFileType($resumeFileType);

            $candidate->setApplicationDate(new \DateTime());
            $candidateRepository->save($candidate, true);

            return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidate/new.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidate_show', methods: ['GET'])]
    public function show(Candidate $candidate): Response
    {
        return $this->render('candidate/show.html.twig', [
            'candidate' => $candidate,
        ]);
    }

    #[Route('/{id}/resume', name: 'app_candidate_resume', methods: ['GET'])]
    public function candidateResume(Candidate $candidate) : Response
    {
        // Replace with the actual path to your Blob file
        // Create the response object
        $response = new StreamedResponse(function () use ($candidate) {
            // Output the Blob data
            fpassthru($candidate->getResume());
        });

        // Set the appropriate headers
        $response->headers->set('Content-Type', $candidate->getResumeFileType());
        $filenameToSend = str_replace(" ","-", $candidate->getFirstName()."-".$candidate->getLastName()."-resume.".$candidate->getResumeFileExtension());
        
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filenameToSend.'"');
        return $response;
    }

    #[Route('/{id}/edit', name: 'app_candidate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidate $candidate, CandidateRepository $candidateRepository): Response
    {
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('resume')->getData();
            $resumeFileType = null;
            $resumeFileExtension = null;

            if ($file) {
                $candidate->setResume(file_get_contents($file->getPathname()));
                $resumeFileExtension = $file->getClientOriginalExtension();
                $resumeFileType = $file->getMimeType();
            }

            $candidate->setResumeFileExtension($resumeFileExtension);
            $candidate->setResumeFileType($resumeFileType);

            $candidateRepository->save($candidate, true);

            return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidate/edit.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidate_delete', methods: ['POST'])]
    public function delete(Request $request, Candidate $candidate, CandidateRepository $candidateRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidate->getId(), $request->request->get('_token'))) {
            $candidateRepository->remove($candidate, true);
        }

        return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
    }
}
